<?php

namespace Raidoxx\Entities;

use Raidoxx\Entities\IA\combat\BaseCombat;
use Raidoxx\Entities\IA\combat\Explosive;
use Raidoxx\Entities\IA\combat\Melee;
use Raidoxx\Entities\IA\combat\Shooter;

class CombatManager
{
    private RDXBaseMob $mob;

    public function __construct(RDXBaseMob $mob)
    {
        $this->mob = $mob;
    }

    public function getMob(): RDXBaseMob
    {
        return $this->mob;
    }

    public function getStyle(): BaseCombat{
        return match ($this->mob->getAtributes()->getCombatStyle()) {
            "shooter" => new Shooter($this->mob),
            "explosive" => new Explosive($this->mob),
            default => new Melee($this->mob),
        };
    }

    public function update(): void
    {
        $this->getStyle()->update();
    }
}