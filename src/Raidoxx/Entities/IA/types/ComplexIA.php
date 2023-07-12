<?php

namespace Raidoxx\Entities\IA\types;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\AmethystBlockChimeSound;
use Raidoxx\Entities\IA\BaseIA;
use Raidoxx\Entities\IA\functions\Walk;
use Raidoxx\Entities\IA\Utils\EntityEnemies;
use Raidoxx\Entities\IA\Utils\RandomPositions;
use Raidoxx\Entities\monsters\Creeper;
use Raidoxx\Entities\monsters\Enderman;
use Raidoxx\Entities\monsters\Skeleton;
use Raidoxx\Entities\RDXBaseMob;
use Raidoxx\Entities\Temperament;
use Raidoxx\Libs\pathfinder\type\AsyncPathfinder;

class ComplexIA extends BaseIA
{
    private Entity $entity;
    private AsyncPathfinder $pathfinder;
    private Walk $walk;

    use RandomPositions;
    use EntityEnemies;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
        parent::__construct($entity);
    }

    public function update(): bool
    {
        if($this->entity instanceof RDXBaseMob){
            $temperament = $this->entity->getTemperamentManage()->getTemperament();

            if($temperament === Temperament::PASSIVE){
                $this->passive();
            }

            if($temperament === Temperament::AGGRESSIVE){
                $this->aggressive();
            }

            if($temperament === Temperament::NEUTRAL){
                $this->neutral();
            }

            if(!is_null($victim = $this->entity->getEnemyManager()->getVictim())){
                if($victim instanceof Player){
                    if($victim->isSpectator() || $victim->isCreative()){
                        $this->resetEntity();
                    }
                }
                if(!$victim->isAlive() || $victim->isClosed()){
                    $this->resetEntity();
                }
            }
        }
        return true;
    }

    public function resetEntity(): void
    {
        if($this->entity instanceof RDXBaseMob){
            $this->entity->getEnemyManager()->setVictim(null);
            $this->entity->getAtributes()->setInHunting(false);
            $this->entity->getEntityMoviment()->getWalk()->setLookAt(null);
            if($this->entity instanceof Skeleton){
                $this->entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
            }

            if($this->entity instanceof Creeper){
                $this->entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::IGNITED, false);
            }

            if($this->entity instanceof Enderman){
                $this->entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::ANGRY, false);
                $this->entity->getTemperamentManage()->setTemperament(Temperament::NEUTRAL);
                $this->entity->getAtributes()->setSpeed(8);
            }
        }
    }

    public function attack(EntityDamageEvent $source): void
    {
        if($source instanceof EntityDamageByEntityEvent){
            $enemy = $source->getDamager();
            if($this->entity instanceof RDXBaseMob){
                $temperament = $this->entity->getTemperamentManage()->getTemperament();
                if($temperament === Temperament::AGGRESSIVE || $temperament === Temperament::NEUTRAL){
                    $this->entity->getAtributes()->setInHunting(true);
                    $this->entity->getEnemyManager()->setVictim($enemy);
                    $this->entity->getTemperamentManage()->setTemperament(Temperament::AGGRESSIVE);
                    $this->entity->setNameTagAlwaysVisible();

                }
            }
        }
    }


    private function passive(): void
    {
       //TODO: Passive IA
    }

    private function aggressive(): void{
        if($this->entity instanceof RDXBaseMob){
            $this->entity->getCombatManager()->update();
        }
    }

    private function neutral():void{
        if($this->entity instanceof RDXBaseMob){
            $this->entity->getCombatManager()->update();
        }
    }
}