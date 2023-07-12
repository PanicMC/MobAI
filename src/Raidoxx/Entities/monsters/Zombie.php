<?php

namespace Raidoxx\Entities\monsters;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use Raidoxx\Entities\Atributes;
use Raidoxx\Entities\IA\types\ComplexIA;
use Raidoxx\Entities\RDXBaseMob;
use Raidoxx\Entities\Temperament;
use Raidoxx\Libs\pathfinder\setting\rule\DefaultPathRules;
use Raidoxx\Libs\pathfinder\setting\Settings;

class Zombie extends RDXBaseMob
{
    public static string $networkId = EntityIds::ZOMBIE;
    public float $width = 0.6;
    public float $height = 1.95;

    public function getName(): string
    {
        return "Zombie";
    }

    public function __construct(Location $location, CompoundTag $nbt = null)
    {
        parent::__construct(
            $location,
            new EntitySizeInfo($this->height, $this->width),
            self::$networkId,
            $this->getName(),
            new ComplexIA($this),
            new Temperament(Temperament::AGGRESSIVE),
            new Atributes(5, 6, 20, 2,20, 5, 20),
            Settings::get()->setPathRules(new DefaultPathRules()),
            new CompoundTag()
        );
        $this->getEnemyManager()->addEnmies(Player::class);
    }

    public function getDrops() : array{
        $drops = [
            VanillaItems::ROTTEN_FLESH()->setCount(mt_rand(0, 2))
        ];

        if(mt_rand(0, 199) < 5){
            switch(mt_rand(0, 2)){
                case 0:
                    $drops[] = VanillaItems::IRON_INGOT();
                    break;
                case 1:
                    $drops[] = VanillaItems::CARROT();
                    break;
                case 2:
                    $drops[] = VanillaItems::POTATO();
                    break;
            }
        }

        return $drops;
    }
}