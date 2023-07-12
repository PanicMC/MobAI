<?php

declare(strict_types=1);

namespace Raidoxx\Libs\pathfinder\type;

use Closure;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;
use Raidoxx\Libs\pathfinder\IPathfinder;
use Raidoxx\Libs\pathfinder\setting\Settings;
use Raidoxx\Libs\pathfinder\task\AsyncPathfinderTask;

class AsyncPathfinder implements IPathfinder {
    private bool $running = false;

    public function __construct(
        protected Settings $settings,
        protected World $world,
        protected int $chunkCacheLimit = 40
    ){}

    public function findPath(Vector3 $startVector, Vector3 $targetVector, Closure $onCompletion): void {
        if($startVector->floor()->equals($targetVector->floor())) {
            ($onCompletion)(null);
            return;
        }
        $this->running = true;
        Server::getInstance()->getAsyncPool()->submitTask(new AsyncPathfinderTask($startVector->asVector3(), $targetVector->asVector3(), $this->settings, $this->world, function (mixed $result) use ($onCompletion): void {
            ($onCompletion)($result);
            $this->running = false;
        }, $this->chunkCacheLimit));
    }

    public function isRunning(): bool {
        return $this->running;
    }
}