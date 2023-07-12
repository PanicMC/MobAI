<?php

namespace Raidoxx\Entities\IA;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use Raidoxx\Entities\IA\Utils\CheckBlocks;

abstract class BaseIA
{
    private Entity $entity;

    use CheckBlocks;
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    abstract public function init(): void;
    abstract public function update(): bool;

    abstract public function attack(EntityDamageEvent $source);
}