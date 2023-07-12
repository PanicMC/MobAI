<?php

namespace Raidoxx\Entities;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\item\Bow;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\Explosion;
use pocketmine\world\Position;
use pocketmine\world\sound\IgniteSound;
use Raidoxx\Entities\IA\Utils\BowUse;
use Raidoxx\Entities\monsters\Skeleton;

class AttackManager
{
    private RDXBaseMob $mob;
    private int $attack_time = 0;
    private int $attack_speed;
    public int $bomb_time = 0;

    public bool $exploding = false;

    use BowUse;
    public function __construct(RDXBaseMob $mob)
    {
        $this->mob = $mob;
        $this->attack_speed = $mob->getAtributes()->getAttackSpeed() ?? 20;
    }

    public function getMob(): RDXBaseMob
    {
        return $this->mob;
    }

    public function process(?Entity $victim): void
    {

        if($this->attack_time <= 0) {
            $this->attack_time = $this->attack_speed;
            switch ($this->getMob()->getAtributes()->getCombatStyle()){
                case "shooter":
                    $this->makeShoot($victim);
                    break;
                case "explosive":
                    $this->makeExplode($victim);
                    break;
                default:
                    $this->makeAttack($victim);
                    break;
            }
        }else{
            $this->attack_time--;
        }
    }

    public function makeAttack(Entity $target): void
    {
        $this->getMob()->lookAt($target->getPosition());
        if($target instanceof Living && !$target->isClosed()){
            $target->attack(new EntityDamageByEntityEvent($this->getMob(), $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getMob()->getAtributes()->getDamage()));
        }
    }

    private function makeShoot(?Entity $target): void
    {
        $this->getMob()->lookAt($target->getPosition());
        if($target instanceof Living) {
            $this->shoot();
            $this->getMob()->broadcastAnimation(new ArmSwingAnimation($this->getMob()));
        }
    }

    private function makeExplode(?Entity $target): void
    {
        if($target instanceof Living && !$target->isClosed()){
            $this->getMob()->lookAt($target->getPosition());
        }

        if(!$this->exploding){
            if($this->bomb_time >= 0){
                $this->mob->getWorld()->addSound($this->getMob()->getPosition(), new IgniteSound());
                $properties = $this->getMob()->getNetworkProperties();
                $properties->setGenericFlag(EntityMetadataFlags::IGNITED, true);
                $properties->setInt(EntityMetadataProperties::VARIANT, 0);
                $properties->setInt(EntityMetadataProperties::FUSE_LENGTH, $this->bomb_time);
            }
            $this->bomb_time++;
            if ($this->bomb_time >= 2) {
                $this->exploding = true;
                $this->createExplosion($this->getMob(), $this->getMob()->isPowered() ? 5 : 3, $this->getMob()->getPosition());
                $this->getMob()->flagForDespawn();
            }
        }
    }

    private function shoot(): void
    {
        if($this->getMob() instanceof Skeleton){
            $inv = $this->getMob()->getInventory();
            $bow = $inv->getItem(0);
            if($bow->isNull() || !$bow instanceof Bow) return;
            $this->use($this->getMob(), $bow);
            $this->mob->getNetworkProperties()->setLong(EntityMetadataProperties::TARGET_EID, 0, true);
        }
    }

    private function createExplosion(RDXBaseMob $mob, int $radius, Position $position): void
    {
        $ev = new EntityPreExplodeEvent($mob, $radius);
        $ev->call();
        if(!$ev->isCancelled()){
            //TODO: deal with underwater TNT (underwater TNT treats water as if it has a blast resistance of 0)
            $explosion = new Explosion($position, $radius, $mob);
            if($ev->isBlockBreaking()){
                $explosion->explodeA();
            }
            $explosion->explodeB();
        }
    }
}