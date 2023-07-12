<?php

namespace Raidoxx;

use Closure;
use Exception;
use Generator;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\Position;
use Raidoxx\Entities\IA\Utils\CheckBlocks;
use Raidoxx\Entities\IA\Utils\RandomPositions;
use Raidoxx\Entities\monsters\Creeper;
use Raidoxx\Entities\monsters\Enderman;
use Raidoxx\Entities\monsters\Skeleton;
use Raidoxx\Entities\monsters\Zombie;
use Raidoxx\Libs\AwaitGenerator\Await;
use Raidoxx\Loader\MobsLoader;
use Throwable;

final class Main extends PluginBase implements Listener
{

    use RandomPositions;
    use CheckBlocks;

    public array $mobs = [];
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
    public function onJoin(PlayerJoinEvent $event): void
    {
        $p = $event->getPlayer();

        $entities = [
            "Zombie" => Zombie::class,
            "Skeleton" => Skeleton::class,
            "Creeper" => Creeper::class,
            "Enderman" => Enderman::class,
        ];

        $this->spawnEntities($p->getPosition(), $entities);
    }


    public function onUse(PlayerInteractEvent $event): void
    {
        $p = $event->getPlayer();

        if($event->getItem()->getTypeId() == VanillaItems::STICK()->getTypeId()){
            $entities = [
                "Zombie" => Zombie::class,
                "Skeleton" => Skeleton::class,
                "Creeper" => Creeper::class,
                "Enderman" => Enderman::class,
            ];
            $this->spawnEntities($p->getPosition(), $entities);
        }
    }

    //Teste de spawn de mobs usando a lib AwaitGenerator
    function spawnEntities(Position $position, array $entities): void
    {
        Await::f2c(fn() => $this->spawnEntity($position, $entities), function (Throwable $e) {
            $this->getLogger()->error($e->getMessage());
        }, function () {
            $this->getLogger()->info("Spawned!");
        });
    }

    function spawnEntity(Position $position, array $entities): Generator
    {
        foreach ($entities as $entity) {
            $entity = new $entity(Location::fromObject($this->getRandomPosition($position, 10), $position->getWorld()));
            yield;
            $entity->spawnToAll();
            yield;
        }
    }
    //Fim do teste de spawn de mobs usando a lib AwaitGenerator

}