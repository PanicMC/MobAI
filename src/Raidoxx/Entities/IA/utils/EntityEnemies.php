<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\entity\Entity;
use pocketmine\player\Player;
use Raidoxx\Entities\RDXBaseMob;

trait EntityEnemies
{
    use RandomPositions;
    public function isEnemy(RDXBaseMob $mob, Entity $entity): bool
    {

        if ($mob->getEnemyManager()->isEnemy($entity::class)) {

            if($entity instanceof Player){

                if($entity->isSpectator() || $entity->isCreative()){
                    return false;
                }

                if($mob::class === $entity::class){
                    return false;
                }

            }

            return true;
        }
        return false;
    }

    public function isInOursideView(RDXBaseMob $mob, Entity $entity): bool
    {
        $view = $mob->getAtributes()->getViewRange();
        $distance = $mob->getPosition()->distance($entity->getPosition());
        if($distance <= $view){
            return true;
        }else{
            return false;
        }
    }
    public function isInAttackRange(RDXBaseMob $mob, Entity $entity): bool
    {
        $range = $mob->getAtributes()->getAttackRange();
        $distance = $mob->getPosition()->distance($entity->getPosition());
        if($distance <= $range){
            return true;
        }else{
            return false;
        }
    }

    public function haveEnemiesArround(RDXBaseMob $mob): bool
    {
        $world = $mob->getWorld();
        $view = $mob->getAtributes()->getViewRange();
        $entities = $world->getNearbyEntities($mob->getBoundingBox()->expandedCopy($view, $view, $view));
        foreach ($entities as $entity){
            if($this->isEnemy($mob, $entity)){
                return true;
            }
        }
        return false;
    }

    public function getNearEnemy(RDXBaseMob $mob): ?Entity
    {
        $world = $mob->getWorld();
        $view = $mob->getAtributes()->getViewRange();
        $entities = $world->getNearbyEntities($mob->getBoundingBox()->expandedCopy($view, $view, $view));
        foreach ($entities as $entity){
            if($this->isEnemy($mob, $entity)){
                return $entity;
            }
        }
        return null;
    }

    public function getEnemiesArround(RDXBaseMob $mob): array
    {
        $world = $mob->getWorld();
        $view = $mob->getAtributes()->getViewRange();
        $entities = $world->getNearbyEntities($mob->getBoundingBox()->expandedCopy($view, $view, $view));
        $enemies = [];
        foreach ($entities as $entity){
            if($this->isEnemy($mob, $entity)){
                $enemies[] = $entity;
            }
        }
        return $enemies;
    }
}