<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\world\Position;
use Raidoxx\Entities\RDXBaseMob;

trait RandomPositions
{
    use CheckBlocks;


    public function getRandomPostionByTarget(Entity $target, float $radius): Position
    {
        $directionVector = $target->getDirectionVector();

        // Multiplica o vetor de direção pelo raio negativo
        $oppositeVector = $directionVector->multiply(-$radius);

        // Calcula a posição oposta adicionando o vetor oposto ao alvo

        $targetPosition = $target->getPosition()->add($oppositeVector->getX(), 0, $oppositeVector->getZ());

        $x = $targetPosition->getX() + mt_rand(-$radius, $radius);
        $y = $targetPosition->getY();
        $z = $targetPosition->getZ() + mt_rand(-$radius, $radius);

        return new Position($x, $y, $z, $target->getWorld());
    }

    public function getRandomPosition(Position $position, int $radius): Position
    {
        $x = $position->getX() + mt_rand(-$radius, $radius);
        $y = $position->getY();
        $z = $position->getZ() + mt_rand(-$radius, $radius);

        return new Position($x, $y, $z, $position->getWorld());
    }
    public function getRandomPositionByEntity(RDXBaseMob $entity): Position
    {
        $start = $entity->getPosition();
        $view = $entity->getAtributes()->getViewRange();

        $x = (int) $start->getX() + mt_rand(-$view, $view);
        $y = (int) $start->getY();
        $z = (int) $start->getZ() + mt_rand(-$view, $view);

        return new Position($x, $y, $z, $start->getWorld());
    }

    public function haveSpaceToSpawn(Position $position, RDXBaseMob $mob): bool
    {
        $world = $position->getWorld();

        $box = $mob->getBoundingBox();
        $volume = (int)$box->getVolume();

        for ($x = -$volume; $x <= $volume; $x++) {
            for ($z = -$volume; $z <= $volume; $z++) {
                for ($y = -$volume; $y <= $volume; $y++) {
                    $block = $world->getBlockAt((int)($position->x + $x),(int)($position->y + $y), (int)($position->z + $z));
                    if ($block->getTypeId() !== VanillaBlocks::AIR()->getTypeId()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function getNearEntity(RDXBaseMob $entity): Entity|null
    {
        $world = $entity->getWorld();

        $view = $entity->getAtributes()->getViewRange();

        $entities = $world->getNearbyEntities($entity->getBoundingBox()->expandedCopy($view, $view, $view));

        if (count($entities) > 0) {
            return $entities[array_rand($entities)];
        }

        return null;
    }
}