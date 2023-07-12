<?php

namespace Raidoxx\Entities\IA\functions;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;
use Raidoxx\Entities\IA\Utils\CheckBlocks;
use Raidoxx\Entities\IA\Utils\PathFinder;
use Raidoxx\Entities\RDXBaseMob;

class Walk
{

    private int $speed;

    private Position|null $final_position = null;
    private Position|null $new_position = null;
    private Entity|RDXBaseMob $entity;

    private int $idle_time = 50;
    private int $max_idle_time = 50;

    private bool $stuck = false;

    private ?Position $old_position;

    use CheckBlocks;
    use PathFinder;
    private array $targets = [];
    private Position|null $lookat = null;

    public function __construct(Entity|RDXBaseMob $entity)
    {
        $this->entity = $entity;
    }

    public function setFinalPosition(Position $position): void
    {
        $this->final_position = $position;
    }

    public function getFinalPosition(): Position|null
    {
        return $this->final_position;
    }

    public function setTarget(Position|null $target): void
    {
        $this->new_position = $target;
    }

    public function setSpeed(int $speed): void
    {
        $this->speed = $speed;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function getTarget(): Position|null
    {
        return $this->new_position;
    }

    public function getTargets(): array
    {
        return $this->targets;
    }

    public function removeTarget(): void
    {
        array_shift($this->targets);
    }


    public function move(): void
    {
        if($this->targets !== []){
            $this->moveToTarget();
        }
    }

    public function isIdle(): bool
    {
        return $this->idle_time <= 0;
    }


    public function setOldPosition(Position $position): void
    {
        $this->old_position = $position;
    }

    public function isFinished(): bool
    {
        // as coordenadas podem dar (por exemplo) 100,5481240982148091284, temos que arredondar para 100,5

        $tx = round($this->getTarget()->getX(), 1);
        $ty = round($this->getTarget()->getY(), 1);
        $tz = round($this->getTarget()->getZ(), 1);

        $ex = round($this->entity->getPosition()->getX(), 1);
        $ey = round($this->entity->getPosition()->getY(), 1);
        $ez = round($this->entity->getPosition()->getZ(), 1);

        return $tx === $ex && $ty === $ey && $tz === $ez;
    }

    public function addTarget(Vector3 $vector3): void
    {
        $this->targets[] = $vector3;
        if($this->getTarget() === null){
            $this->setTarget($this->getNewTarget());
        }
    }

    private function moveToTarget(): void
    {
        if($this->isFinished()){
            $this->removeTarget();
            $newTarget = $this->getNewTarget();
            if($newTarget !== null){
                $this->setTarget($newTarget);
            }
        }else{
            $x = $this->getTarget()->getX() - $this->entity->getPosition()->getX();
            $z = $this->getTarget()->getZ() - $this->entity->getPosition()->getZ();
            $y = $this->getTarget()->getY() - $this->entity->getPosition()->getY();
            $module = sqrt($x*$x + $z*$z);

            if($module == 0 || $x == 0 || $z == 0){
                return;
            }

            $x = $x/$module;
            $z = $z/$module;


            if($this->entity->isUnderwater()){
                $this->entity->addMotion($x / 25, $y / 25, $z / 25);
                return;
            }else{
                if($this->entity->isOnGround()){
                    $speed = (110 / $this->entity->getAtributes()->getSpeed());
                    $this->entity->addMotion($x / $speed, 0, $z / $speed);
                    $this->entity->getLocation()->getYaw();
                    if($this->haveBlockInFront($this->entity, $this->entity->getPosition(), $this->entity->getWorld())){
                        $this->jump();
                    }
                }else{
                    $speed = (150 / $this->entity->getAtributes()->getSpeed());
                    $this->entity->addMotion($x / $speed, 0, $z / $speed);
                }
            }

            if($this->getLookAt()){
                $this->entity->lookAt($this->getLookAt());
            }else{
                $this->entity->lookAt($this->getTarget()->add(0,1,0));
            }

           if($this->isIdle()){
               $this->stuck = true;
               $this->generatePath($this->getFinalPosition(), true);
           }
        }
    }

    public function jump() : void{
        if($this->entity->isOnGround()){
            $this->entity->addMotion(0, $this->getJumpVelocity(), 0);
        }
    }

    private function getNewTarget()
    {
        $amount = count($this->targets);
        if($amount > 0){
            return $this->targets[array_keys($this->targets)[0]];
        }
        return null;
    }

    private function getJumpVelocity(): float|int
    {
        $effect_manager = $this->entity->getEffects();
        return 0.50 + ((($jumpBoost = $effect_manager->get(VanillaEffects::JUMP_BOOST())) !== null ? $jumpBoost->getEffectLevel() : 0) / 10);
    }

    public function removeTargets(): void
    {
        $this->targets = [];
        $this->setTarget(null);
    }

    public function generatePath(?Position $target, bool $generate_path): void
    {
        if($target === null) {
            return;
        }

        if($this->idle_time <= 0 ){
            $this->idle_time = $this->max_idle_time;
            $this->removeTargets();
            $this->sharePath($target, $this->entity);
        }

        if($this->entity->getPosition()->distance($this->old_position) < 2) {
            $this->idle_time--;
        }else{
            $this->old_position = $this->entity->getPosition();
            $this->idle_time = $this->max_idle_time;
        }

        if($generate_path){
            if($this->getTarget() === null) {
                $this->sharePath($target, $this->entity);
            }else{
                if($this->getTargets() === []) {
                    $this->sharePath($target, $this->entity);
                }
            }
        }else{
            if(!$this->stuck){
                $this->removeTargets();
                $this->addTarget($target);
                $this->setFinalPosition($target);
            }else{
                if($this->idle_time >= 50){
                    $this->stuck = false;
                }
            }
        }


        $this->move();
    }

    public function setLookAt(Position|null $getPosition): void
    {
        $this->lookat = $getPosition;
    }

    private function getLookAt(): Position|null
    {
        return $this->lookat;
    }

}