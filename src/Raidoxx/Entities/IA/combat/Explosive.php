<?php

namespace Raidoxx\Entities\IA\combat;

use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use Raidoxx\Entities\RDXBaseMob;

class Explosive extends BaseCombat
{

    private RDXBaseMob $entity;

    public function __construct(RDXBaseMob $entity)
    {
        $this->entity = $entity;
        parent::__construct("Explosive");
    }


    function attack(): void
    {
        $entity = $this->entity;
        $entity->getAttackManager()->process($entity->getEnemyManager()->getVictim());
    }

    function update(): void
    {
        if($this->haveEnemiesArround($this->entity)){
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
                    }else{
                        $this->entity->getAttackManager()->bomb_time = 0;
                        $properties = $this->entity->getNetworkProperties();
                        $properties->setGenericFlag(EntityMetadataFlags::IGNITED, false);
                        $properties->setInt(EntityMetadataProperties::FUSE_LENGTH, 0);
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