<?php

namespace Raidoxx\Entities;

use pocketmine\world\Position;
use Raidoxx\Entities\IA\functions\Walk;
use Raidoxx\Libs\pathfinder\setting\Settings;
use Raidoxx\Libs\pathfinder\type\AsyncPathfinder;

class EntityMoviment
{
    private RDXBaseMob $mob;
    private Walk $walk;
    private AsyncPathfinder $pathfinder;



    public function __construct(RDXBaseMob $mob, Settings $settings){
        $this->mob = $mob;
        $this->walk = new Walk($mob);
        $this->pathfinder = new AsyncPathfinder($settings, $this->mob->getWorld());
        $this->walk->setOldPosition($this->mob->getPosition());
    }

    public function getPathfinder(): AsyncPathfinder
    {
        return $this->pathfinder;
    }

    public function getWalk(): Walk
    {
        return $this->walk;
    }

    public function getMob(): RDXBaseMob
    {
        return $this->mob;
    }

    public function lockLookAt(Position|null $getPosition): void
    {
        $this->walk->setLookAt($getPosition);
    }
}