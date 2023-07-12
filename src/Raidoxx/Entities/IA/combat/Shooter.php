<?php

namespace Raidoxx\Entities\IA\combat;

use pocketmine\item\Bow;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\Position;
use Raidoxx\Entities\RDXBaseMob;

class Shooter extends BaseCombat
{
    private RDXBaseMob $entity;

    public function __construct(RDXBaseMob $entity)
    {
        $this->entity = $entity;
        parent::__construct("Shooter");
    }


    function attack(): void
    {
        $entity = $this->entity;
        $entity->getAttackManager()->process($entity->getEnemyManager()->getVictim());
    }

    function update(): void
    {
        $entity_movement = $this->entity->getEntityMoviment();
        $bool = true;
        if($this->haveEnemiesArround($this->entity)){
            $enemyManager = $this->entity->getEnemyManager();


            if(is_null($enemyManager->getVictim()) || !$this->entity->getAtributes()->isInHunting()){
                $target = $this->getNearEnemy($this->entity)->getPosition();
                $this->entity->getAtributes()->setInHunting(true);
                $enemyManager->setVictim($this->getNearEnemy($this->entity));
            }else{
                if(!$enemyManager->getVictim()->isAlive()){
                    $enemyManager->setVictim(null);
                    return;
                }
                if($this->isInOursideView($this->entity, $enemyManager->getVictim())) {
                    if($this->isInAttackRange($this->entity, $enemyManager->getVictim())){
                        if(!$this->entity->getInventory()->getItemInHand() instanceof Bow){
                            $this->entity->getAtributes()->setShooter(false);
                            $entity_movement->lockLookAt(null);
                        }
                        $this->attack();
                        $this->entity->getNetworkProperties()->setLong(EntityMetadataProperties::TARGET_EID, $this->entity->getEnemyManager()->getVictim()->getId(), true);
                    }

                    $target = $this->getRandomPostionByTarget($enemyManager->getVictim(), 7);
                    $lookAt = $enemyManager->getVictim()->getPosition()->add(0, 2, 0);
                    $entity_movement->lockLookAt(new Position($lookAt->getX(), $lookAt->getY(), $lookAt->getZ(), $this->entity->getWorld()));
                    if($enemyManager->getVictim()->getPosition()->distance($this->entity->getPosition()) > 10){
                        $bool = false;
                        $entity_movement->lockLookAt(null);
                    }
                }else{
                    $this->entity->getAtributes()->setInHunting(false);
                    $enemyManager->setVictim(null);
                    $target = $this->getRandomPositionByEntity($this->entity);
                }
            }
        }else{
            $target = $this->getRandomPositionByEntity($this->entity);
        }


        if(!is_null($entity_movement->getWalk()->getFinalPosition())){
            if($entity_movement->getWalk()->getFinalPosition()->equals($this->entity->getPosition())){
                $target = $this->getRandomPositionByEntity($this->entity);
            }
        }

        $this->entity->getEntityMoviment()->getWalk()->generatePath($target, $bool);
    }
}