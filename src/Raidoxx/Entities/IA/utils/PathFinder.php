<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\world\Position;
use Raidoxx\Entities\RDXBaseMob;
use Raidoxx\Libs\pathfinder\result\PathResult;

trait PathFinder
{
    public function sharePath(Position $target, RDXBaseMob $mob): void
    {
        $target_vector3 = $target->add(0, 0, 0);
        $start = $mob->getPosition()->add(0, 0, 0);

        if ($target->equals($start)) {
            return;
        }

        if ($mob->getEntityMoviment()->getPathfinder()->isRunning()) {
            return;
        }

        if ($mob->getEntityMoviment()->getWalk()->getTargets() !== []) {
            $mob->getEntityMoviment()->getWalk()->removeTargets();
        }

        $mob->getEntityMoviment()->getPathfinder()->findPath($start, $target_vector3, function (?PathResult $result) use ($target, $mob): void {
            if ($result === null) {
                return;
            }
            $path = $result->getNodes();
            foreach ($path as $node) {
                $vector3 = $node->asVector3();
                if($mob->isAlive() && !$mob->isClosed()){
                    $mob->getEntityMoviment()->getWalk()->addTarget(new Position($vector3->getX(), $vector3->getY(), $vector3->getZ(), $mob->getWorld()));
                    $mob->getEntityMoviment()->getWalk()->setFinalPosition($target);
                }
            }
        });
    }
}