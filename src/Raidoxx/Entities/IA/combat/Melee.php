<?php

namespace Raidoxx\Entities\IA\combat;

use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use Raidoxx\Entities\monsters\Creeper;
use Raidoxx\Entities\monsters\Enderman;
use Raidoxx\Entities\monsters\Skeleton;
use Raidoxx\Entities\RDXBaseMob;
use Raidoxx\Entities\Temperament;

class Melee extends BaseCombat
{
    private RDXBaseMob $entity;

    public function __construct(RDXBaseMob $entity)
    {
        $this->entity = $entity;
        parent::__construct("Melee");
    }


    function attack(): void
    {
        $entity = $this->entity;

        if($entity->getEnemyManager()->getVictim()->isAlive()){
            $entity->getAttackManager()->process($entity->getEnemyManager()->getVictim());
        }
    }

    function update(): void
    {
        if($this->haveEnemiesArround($this->entity) && $this->entity->getTemperamentManage()->isAggressive()){
            $bool = false;
            $enemyManager = $this->entity->getEnemyManager();
            if(is_null($enemyManager->getVictim()) || !$this->entity->getAtributes()->isInHunting()){
                $target = $this->getNearEnemy($this->entity)->getPosition();
                $this->entity->getAtributes()->setInHunting(true);
                $enemyManager->setVictim($this->getNearEnemy($this->entity));
            }else{
                if($this->isInOursideView($this->entity, $enemyManager->getVictim())) {
                    if($this->isInAttackRange($this->entity, $enemyManager->getVictim())){
                        $this->attack();
                    }

                    if($this->entity->getEntityMoviment()->getWalk()->isIdle() && !$this->entity->getAtributes()->getViewRange() / 2 > $this->entity->getPosition()->distance($enemyManager->getVictim()->getPosition())) {
                        $bool = true;
                    }

                    $target = $enemyManager->getVictim()->getPosition();
                }else{
                    $this->entity->getAtributes()->setInHunting(false);
                    $enemyManager->setVictim(null);
                    $target = $this->getRandomPositionByEntity($this->entity);
                }
            }
        }else{
            $bool = true;
            $target = $this->getRandomPositionByEntity($this->entity);
        }

        $entity_movement = $this->entity->getEntityMoviment();
        if(!is_null($entity_movement->getWalk()->getFinalPosition())){
            if($entity_movement->getWalk()->getFinalPosition()->equals($this->entity->getPosition())){
                $target = $this->getRandomPositionByEntity($this->entity);
            }
        }

        $this->entity->getEntityMoviment()->getWalk()->generatePath($target, $bool);
    }
}