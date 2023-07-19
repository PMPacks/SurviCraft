<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Parrot extends SpawnerEntity
{
    public function getName(): string
    {
        return "Parrot";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(6);
        parent::initEntity($nbt);
    }

    public function getDrops(): array
    {
        $lootingL = 1;
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $dmg = $cause->getDamager();
            if ($dmg instanceof Player) {

                $looting = $dmg->getInventory()->getItemInHand()->getEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOOTING));
                if ($looting !== null) {
                    $lootingL = $looting->getLevel();
                } else {
                    $lootingL = 1;
                }
            }
        }
        return [
            VanillaItems::FEATHER()->setCount(mt_rand(1, 2 * $lootingL)),
        ];
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.0, 0.5);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::PARROT;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}
