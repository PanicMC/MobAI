<?php

namespace Raidoxx\Entities;

class Atributes
{
    private int $damage;
    private int $speed;
    private int $attack_speed;
    private int $attack_range;
    private int $view_range;
    private int $xp;
    private bool $isShooter = false;
    private bool $isExplosive = false;
    private bool $isInHunting = false;
    private bool $isTeleporter = false;
    private int $health;

    function __construct(int $damage, int $speed, int $attack_speed, int $attack_range, int $view_range, int $xp, int $health)
    {
        $this->damage = $damage;
        $this->speed = $speed;
        $this->attack_speed = $attack_speed;
        $this->attack_range = $attack_range;
        $this->view_range = $view_range;
        $this->xp = $xp;
        $this->health = $health;
    }

    public function getHealth(): int
    {
        return $this->health;
    }
    public function getDamage(): int
    {
        return $this->damage;
    }

    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function setSpeed(int $int): void
    {
        $this->speed = $int;
    }

    public function getAttackSpeed(): int
    {
        return $this->attack_speed;
    }

    public function getAttackRange(): int
    {
        return $this->attack_range;
    }

    public function getViewRange(): int
    {
        return $this->view_range;
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function isShooter(): bool
    {
        return $this->isShooter;
    }

    public function isTeleporter(): bool
    {
        return $this->isTeleporter;
    }

    public function isExplosive(): bool
    {
        return $this->isExplosive;
    }

    public function isInHunting(): bool
    {
        return $this->isInHunting;
    }

    public function setShooter(bool $value): void
    {
        $this->isShooter = $value;
    }

    public function setExplosive(bool $value): void
    {
        $this->isExplosive = $value;
    }

    public function setInHunting(bool $value): void
    {
        $this->isInHunting = $value;
    }


    public function getCombatStyle(): string
    {
        if($this->isShooter()){
            return "shooter";
        }elseif($this->isExplosive()){
            return "explosive";
        }elseif($this->isTeleporter()){
            return "teleporter";
        }else{
            return "melee";
        }
    }

    public function setTeleporter(bool $true): void
    {
        $this->isTeleporter = $true;
    }


}