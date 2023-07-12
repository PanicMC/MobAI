<?php

namespace Raidoxx\Loader;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use Raidoxx\Entities\monsters\Creeper;
use Raidoxx\Entities\monsters\Enderman;
use Raidoxx\Entities\monsters\Skeleton;
use Raidoxx\Entities\monsters\Zombie;
use Raidoxx\Entities\RDXBaseMob;

class MobsLoader
{
    public function __construct()
    {
        $this->registerEntities();
    }

    public function registerEntities(): void
    {
        $entityFactory = EntityFactory::getInstance();
        foreach ($this->getClasses() as $entityName => $typeClass) {
            $entityFactory->register($typeClass,
                static function(World $world, CompoundTag $nbt) use($typeClass): RDXBaseMob {
                    return new $typeClass(EntityDataHelper::parseLocation($nbt, $world), $nbt);
                },
                [$entityName]);
        }
    }

    private function getClasses(): array
    {
        return [
           "Zombie" => Zombie::class,
           "Skeleton" => Skeleton::class,
           "Creeper" => Creeper::class,
           "Enderman" => Enderman::class
        ];
    }
}