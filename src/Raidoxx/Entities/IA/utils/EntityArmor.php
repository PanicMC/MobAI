<?php

namespace Raidoxx\Entities\IA\Utils;

use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

trait EntityArmor
{


    public function generateArmor(): ?array
    {
        // Definir chances gerais
        $chance = mt_rand(0, 100);

        if ($chance >= 2 && $chance <= 15) {

            /*
             * Armaduras Peças	Probabilidade
                1 peça	100%
                2 peças	90%
                3 peças	81%
                Conjunto completo	72.9%
             */
            $armorPiecesChance = [
                0 => 100,
                1 => 90,
                2 => 81,
                3 => 72.9
            ];

           $picies = [];

           for ($i = 0; $i < count($armorPiecesChance) - 1; $i++) {
              if(mt_rand(0, 100) <= $armorPiecesChance[$i]){
                  $type = $this->getTypeArmorSlot($picies);
                  if($type == null){
                      break;
                  }
                  $picies[] = $type;
              }
            }

           if(empty($this->generateArmorPieces($picies))){
                return null;
           }
            return $this->generateArmorPieces($picies);
        }

        return null; // Caso não gere armadura, retorna null ou algum valor padrão
    }

    public function generateArmorPieces(array $types): ?array
    {
        $enchants = [
            VanillaEnchantments::PROTECTION(),
            VanillaEnchantments::UNBREAKING(),
            VanillaEnchantments::THORNS()
        ];

        $armors = [
            "leather" => 37.06,
            "gold" => 48.73,
            "chain" => 12.90,
            "iron" => 1.2,
            "diamond" => 0.04
        ];

        $mob_armor = [];
        for ($i = 0; $i < count($armors); $i++){
            if(floor(mt_rand(0, 100)) <= array_keys($armors)[$i]){
                for ($j = 0; $j < count($types); $j++){
                    $armor = $this->getArmor($types[$j], array_keys($armors)[$i]);
                    // Adicionar encantamentos 6.25%–50%
                    if(mt_rand(0, 100) <= 6.25){
                        $armor->addEnchantment(new EnchantmentInstance($enchants[mt_rand(0, count($enchants) - 1)], mt_rand(1, 3)));
                    }
                   $mob_armor[] = $armor;
                }
                break;
            }
        }
        
        return $mob_armor;
    }

    public function getArmor($type, $armor): Armor
    {
        $armors = [
            "helmet" => [
                "leather" => VanillaItems::LEATHER_CAP(),
                "gold" => VanillaItems::GOLDEN_HELMET(),
                "chain" => VanillaItems::CHAINMAIL_HELMET(),
                "iron" => VanillaItems::IRON_HELMET(),
                "diamond" => VanillaItems::DIAMOND_HELMET()
            ],
            "chestplate" => [
                "leather" => VanillaItems::LEATHER_TUNIC(),
                "gold" => VanillaItems::GOLDEN_CHESTPLATE(),
                "chain" => VanillaItems::CHAINMAIL_CHESTPLATE(),
                "iron" => VanillaItems::IRON_CHESTPLATE(),
                "diamond" => VanillaItems::DIAMOND_CHESTPLATE()
            ],
            "leggings" => [
                "leather" => VanillaItems::LEATHER_PANTS(),
                "gold" => VanillaItems::GOLDEN_LEGGINGS(),
                "chain" => VanillaItems::CHAINMAIL_LEGGINGS(),
                "iron" => VanillaItems::IRON_LEGGINGS(),
                "diamond" => VanillaItems::DIAMOND_LEGGINGS()
            ],
            "boots" => [
                "leather" => VanillaItems::LEATHER_BOOTS(),
                "gold" => VanillaItems::GOLDEN_BOOTS(),
                "chain" => VanillaItems::CHAINMAIL_BOOTS(),
                "iron" => VanillaItems::IRON_BOOTS(),
                "diamond" => VanillaItems::DIAMOND_BOOTS()
            ]
        ];

        return $armors
        [$type]
        [$armor];
    }

    public function getTypeArmorSlot(array $have = []): null|string
    {

        $pices = [
            0 => "helmet",
            1 => "chestplate",
            2 => "leggings",
            3 => "boots"
        ];
        if(empty($have)){
            return $pices[mt_rand(0, count($pices) - 1)];
        }else{
            $result = array_values(array_diff($pices, $have));

            if(empty($result)){
                return null;
            }

            return $result[mt_rand(0, count($result) - 1)];
        }
    }

    public function generateBow(): Bow
    {
        $enchants = [
            VanillaEnchantments::PUNCH(),
            VanillaEnchantments::POWER(),
            VanillaEnchantments::FLAME()
        ];

        $bow = VanillaItems::BOW();

        foreach($enchants as $enchant){
            if(mt_rand(0, 100) <= 96 || mt_rand(0, $enchant->getRarity())){
                $level = mt_rand($enchant->getMinLevel(), $enchant->getMaxLevel());
                $bow->addEnchantment($enchant->setLevel($level));
            }
        }

        return $bow;
    }
}