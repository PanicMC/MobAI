<?php

namespace Raidoxx\Entities\IA\combat;

use Raidoxx\Entities\IA\Utils\CheckBlocks;
use Raidoxx\Entities\IA\Utils\EntityEnemies;
use Raidoxx\Entities\IA\Utils\RandomPositions;

abstract class BaseCombat
{
    use CheckBlocks;
    use EntityEnemies;
    use RandomPositions;
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract function attack(): void;

    abstract function update(): void;
}