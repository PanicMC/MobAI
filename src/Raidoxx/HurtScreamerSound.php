<?php

namespace Raidoxx;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\world\sound\Sound;

class HurtScreamerSound implements Sound
{

    public function encode(Vector3 $pos) : array{
        return [LevelSoundEventPacket::create(
            LevelSoundEvent::AMBIENT_SCREAMER,
            $pos,
            0,
            EntityIds::PLAYER,
            false,
            false
        )];
    }
}