<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemUseResult;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use pocketmine\world\sound\BowShootSound;
use Raidoxx\Entities\RDXBaseMob;
use pocketmine\entity\projectile\Arrow as ArrowEntity;

trait BowUse
{
    public function use(RDXBaseMob $mob, Bow $bow) : ItemUseResult{
        $location = $mob->getLocation();

        //return $this->startAction === -1 ? -1 : ($this->server->getTick() - $this->startAction);
        $diff = Server::getInstance()->getTick() - -1;
        $dmg = $diff / 20;
        $baseForce = min((($dmg ** 2) + $dmg * 2) / 3, 1);

        $entity = new ArrowEntity(Location::fromObject(
            $mob->getEyePos(),
            $mob->getWorld(),
            ($location->yaw > 180 ? 360 : 0) - $location->yaw,
            -$location->pitch
        ), $mob, $baseForce >= 1);
        $entity->setMotion($mob->getDirectionVector());
        $entity->addMotion(0, 0.2, 0);

        $infinity = $bow->hasEnchantment(VanillaEnchantments::INFINITY());
        if($infinity){
            $entity->setPickupMode(ArrowEntity::PICKUP_CREATIVE);
        }
        if(($punchLevel = $bow->getEnchantmentLevel(VanillaEnchantments::PUNCH())) > 0){
            $entity->setPunchKnockback($punchLevel);
        }
        if(($powerLevel = $bow->getEnchantmentLevel(VanillaEnchantments::POWER())) > 0){
            $entity->setBaseDamage($entity->getBaseDamage() + (($powerLevel + 1) / 2));
        }
        if($bow->hasEnchantment(VanillaEnchantments::FLAME())){
            $entity->setOnFire(intdiv($entity->getFireTicks(), 20) + 100);
        }
        $ev = new EntityShootBowEvent($mob, $bow, $entity, $baseForce * 2);

        if($baseForce < 0.1 || $diff < 5){
            $ev->cancel();
        }

        $ev->call();

        $entity = $ev->getProjectile(); //This might have been changed by plugins

        if($ev->isCancelled()){
            $entity->flagForDespawn();
            return ItemUseResult::FAIL();
        }

        $entity->setMotion($entity->getMotion()->multiply($ev->getForce()));

        if($entity instanceof Projectile){
            $projectileEv = new ProjectileLaunchEvent($entity);
            $projectileEv->call();
            if($projectileEv->isCancelled()){
                $ev->getProjectile()->flagForDespawn();
                return ItemUseResult::FAIL();
            }

            $ev->getProjectile()->spawnToAll();
            $location->getWorld()->addSound($location, new BowShootSound());
        }else{
            $entity->spawnToAll();
        }

        $bow->applyDamage(1);

        return ItemUseResult::SUCCESS();
    }
}