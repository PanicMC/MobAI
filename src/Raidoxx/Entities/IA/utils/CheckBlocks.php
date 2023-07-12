<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\RedstoneParticle;
use pocketmine\world\Position;
use pocketmine\world\World;

trait CheckBlocks
{
    public function getNewPassage(Position $position, World $world): Position|null{

        if($this->havePassage($position, $world)){
            return $position;
        }else{
            $blocks = $this->getBlocksAround($position, $world);
            $block_checkds = [];
            foreach ($blocks as $block){
                $block_position = $block->getPosition();

                if($this->havePassage($block_position, $world)){
                    $block_checkds[] = $block_position;
                }
            }

            if(count($block_checkds) > 0){
                return $block_checkds[array_rand($block_checkds)];
            }else{
                return null;
            }
        }
    }
    public function havePassage(Position $position, World $world): bool
    {
        $x = (int) $position->getX();
        $y = (int) $position->getY();
        $z = (int) $position->getZ();

        $blockInFront = $world->getBlockAt($x, $y, $z);
        $blockFrontUp = $world->getBlockAt($x, $y + 1, $z);

        if(!$blockInFront->isSolid() && !$blockFrontUp->isSolid()){
            return true;
        }else{
            if($this->isJumpable($position, $world)) {
                if($this->isLava($position, $world)) return false;
                return true;
            }
            return false;
        }
    }
    private function isJumpable(Position $position, World $world): bool
    {
        $blockInFront = $world->getBlockAt((int) $position->getX(), (int) $position->getY(), (int) $position->getZ());
        $blockFrontUp = $world->getBlockAt((int) $position->getX(), (int) $position->getY() + 1, (int) $position->getZ());
        if($blockInFront->isSolid() && !$blockFrontUp->isSolid()){
            return true;
        }else{
            return false;
        }
    }

    private function isWater(Position $position, World $world): bool
    {
        $x = (int) $position->getX();
        $y = (int) $position->getY();
        $z = (int) $position->getZ();

        $blockInFront = $world->getBlockAt($x, $y, $z);

        if($blockInFront->getTypeId() == VanillaBlocks::WATER()->getTypeId()){
            return true;
        }else{
            return false;
        }
    }

    public function isLava(Position $position, World $world): bool
    {
        $x = (int) $position->getX();
        $y = (int) $position->getY();
        $z = (int) $position->getZ();

        $blockInFront = $world->getBlockAt($x, $y, $z);

        if($blockInFront->getTypeId() == VanillaBlocks::LAVA()->getTypeId()){
            return true;
        }else{
            return false;
        }
    }

//
//    public function getLookAtBlock(Position $position, Vector3 $directionVector, World $world): Block
//    {
//        $getInBlockPos = $position->getWorld()->getBlockAt((int)$position->getX(), (int)$position->getY() - 1, (int)$position->getZ())->getPosition();
//        $directionVector = $directionVector->normalize();
//        // Pegar o bloco que está na frente do bloco que o jogador está em pé
//        $x = (int) round($getInBlockPos->getX() + $directionVector->getX());
//        $y = (int) $getInBlockPos->getY();
//        $z = (int) round($getInBlockPos->getZ() + $directionVector->getZ());
//
//        // Pegar o bloco que está a cima do $blockFront
//
//        $position->getWorld()->addParticle(new Vector3($x, $y + 1, $z), new RedstoneParticle());
//        return $world->getBlockAt($x, $y + 1, $z);
//    }

    public function getDirectionLook(Entity $entity): string
    {
       $direction = $entity->getDirectionVector();

         $x = $direction->getX();
         $z = $direction->getZ();


            if($x > 0 && $z > 0){
                return 'NE';
            }elseif($x > 0 && $z < 0){
                return 'SE';
            }elseif($x < 0 && $z < 0){
                return 'SO';
            }elseif($x < 0 && $z > 0){
                return 'NO';
            }elseif($x > 0){
                return 'E';
            }elseif($x < 0){
                return 'O';
            }elseif($z < 0){
                return 'S';
            }elseif($z > 0){
                return 'N';
            }
    }

    public function getLookAtBlock(Entity $entity, Position $position, World $world): Block
    {
        return $this->lookAtBlock($this->getRotation($entity), $position, $world);
    }


    public function lookAtBlock(float $yaw, Position $position, World $world): Block
    {
        $yawRad = deg2rad($yaw);

        $directionVector = new Vector3(-sin($yawRad), 0, cos($yawRad));

        $x = (int) round($position->getX() + $directionVector->getX());
        $y = (int) $position->getY();
        $z = (int) round($position->getZ() + $directionVector->getZ());

        return $world->getBlockAt($x, $y, $z);
    }



    public function getRotation(Entity $entity): float
    {
        return $entity->getLocation()->getYaw();
    }



    public function haveBlockInFront(Entity $entity, Position $position, World $world): bool
    {
        $looking = $this->getLookAtBlock($entity, $position, $world);
        if($looking->isSolid()){
            return true;
        }else{
            return false;
        }
    }

    public function getBlocksAround(Position $position, World $world, $radius = 1): array
    {
        $blocks = [];
        for ($x = -$radius; $x <= $radius; $x++) {
            for ($z = -$radius; $z <= $radius; $z++) {
                $block = $world->getBlockAt((int) $position->getX() + $x, (int) $position->getY(), (int) $position->getZ() + $z);
                $blocks[] = $block;
            }
        } return $blocks;
    }

    public function isBlockIsJumpable(Block $block): bool
    {
        $position = $block->getPosition();
        $x = (int) $position->getX();
        $y = (int) $position->getY();
        $z = (int) $position->getZ();

        $getBlockUp = $position->getWorld()->getBlockAt($x, $y + 1, $z);


        if($block->isSolid() && !$getBlockUp->isSolid()) {
            return true;
        }else{
            return false;
        }
    }
}