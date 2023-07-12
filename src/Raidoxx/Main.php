<?php

namespace Raidoxx;

use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\world\particle\HeartParticle;
use Raidoxx\Entities\IA\Utils\CheckBlocks;
use Raidoxx\Entities\IA\Utils\RandomPositions;
use Raidoxx\Entities\monsters\Creeper;
use Raidoxx\Entities\monsters\Enderman;
use Raidoxx\Entities\monsters\Skeleton;
use Raidoxx\Entities\monsters\Zombie;
use Raidoxx\Loader\MobsLoader;

final class Main extends PluginBase implements Listener
{

    use RandomPositions;
    use CheckBlocks;

    private static Main $instance;

    public function onLoad(): void
    {
        new MobsLoader();
    }

    public function onEnable():void
    {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    //TODO: Fazer sistema de spawn de mobs por bioma usando a lib AwaitGenerator

    /*
     * Teste dos mobs
     */
    public function onUse(PlayerJoinEvent $event): void
    {
        $p = $event->getPlayer();

        $entity = new Skeleton(Location::fromObject($p->getPosition(), $p->getWorld()));
        $entity->spawnToAll();
        $entity2 = new Zombie(Location::fromObject($p->getPosition(), $p->getWorld()));
        $entity2->spawnToAll();
        $entity3 = new Creeper(Location::fromObject($p->getPosition(), $p->getWorld()));
        $entity3->spawnToAll();
        $entity4 = new Enderman(Location::fromObject($p->getPosition(), $p->getWorld()));
        $entity4->spawnToAll();
    }

    public function onU(PlayerInteractEvent $event){
        $p = $event->getPlayer();

        if($event->getItem()->getTypeId() == VanillaItems::STICK()->getTypeId()){
            $entities = [
                "Zombie" => Zombie::class,
                "Skeleton" => Skeleton::class,
                "Creeper" => Creeper::class,
                "Enderman" => Enderman::class,
            ];

            array_walk($entities, function ($entity) use ($p) {
                $random = $this->getRandomPosition($p->getPosition(), 10);
                $entity = new $entity(Location::fromObject($random, $random->getWorld()));
                $entity->spawnToAll();
            });
        }
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        $p = $event->getPlayer();
        $b = $this->lookAtBlock($p->getLocation()->getYaw(), $p->getPosition(), $p->getWorld());
        $p->sendPopup($b->getName());
        $p->getWorld()->addParticle($b->getPosition(), new HeartParticle());
    }
}