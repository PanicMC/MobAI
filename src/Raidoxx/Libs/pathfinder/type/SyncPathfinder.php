<?php

declare(strict_types=1);

namespace Raidoxx\Libs\pathfinder\type;

use pocketmine\block\Block;
use pocketmine\world\World;
use Raidoxx\Libs\pathfinder\BasePathfinder;
use Raidoxx\Libs\pathfinder\IPathfinder;
use Raidoxx\Libs\pathfinder\setting\Settings;

class SyncPathfinder extends BasePathfinder implements IPathfinder {
    public function __construct(
        Settings $settings,
        protected World $world
    ){
        parent::__construct($settings);
    }

    protected function getBlockAt(int $x, int $y, int $z): Block{
        return $this->world->getBlockAt($x, $y, $z);
    }
}