<?php

namespace Raidoxx\Entities\inventory;

use pocketmine\inventory\SimpleInventory;
use pocketmine\item\Item;
use Raidoxx\Entities\RDXBaseMob;

class RDXMobInventory extends SimpleInventory
{

    public function __construct(
        private readonly RDXBaseMob $holder,
        int                         $size = 1
    ){
        parent::__construct($size);
    }

    public function getHolder() : RDXBaseMob{ return $this->holder; }

    public function getItemInHand(): Item
    {
        return $this->getItem(0);
    }
}