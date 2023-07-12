<?php

namespace Raidoxx\Entities;

class Temperament
{

    public const PASSIVE = 0;
    public const NEUTRAL = 1;
    public const AGGRESSIVE = 2;

    public function __construct(
        private int $temperament = self::PASSIVE
    ) {

    }

    public function getTemperament(): int
    {
        return $this->temperament;
    }

    public function setTemperament(int $temperament): void
    {
        $this->temperament = $temperament;
    }

    public function isPassive(): bool
    {
        return $this->temperament === self::PASSIVE;
    }

    public function isNeutral(): bool
    {
        return $this->temperament === self::NEUTRAL;
    }

    public function isAggressive(): bool
    {
        return $this->temperament === self::AGGRESSIVE;
    }
}