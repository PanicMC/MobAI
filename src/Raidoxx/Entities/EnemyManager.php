<?php

namespace Raidoxx\Entities;

use pocketmine\entity\Entity;
use Raidoxx\Entities\IA\Utils\EntityEnemies;

class EnemyManager
{

    use EntityEnemies;

    private ?Entity $victim = null;
    private array $enmies = [];
    private RDXBaseMob $mob;

    public function __construct(RDXBaseMob $mob)
    {
        $this->mob = $mob;
    }

    public function getVictim(): ?Entity
    {
        return $this->victim;
    }

    public function setVictim(?Entity $victim): void
    {
        $this->victim = $victim;
    }

    public function removeEnmies(string $enmies): void
    {
        unset($this->enmies[array_search($enmies, $this->enmies)]);
    }

    public function addEnmies(string $enmies): void
    {
        $this->enmies[] = $enmies;
    }

    public function isEnemy(string $mob): bool
    {
        if (in_array($mob, $this->enmies)) {
            return true;
        } else {
            return false;
        }
    }

    public function getEnmies(): array
    {
        return $this->enmies;
    }

    public function getMob(): RDXBaseMob
    {
        return $this->mob;
    }

}