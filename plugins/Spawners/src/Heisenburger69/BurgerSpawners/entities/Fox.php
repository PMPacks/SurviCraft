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

class Fox extends SpawnerEntity
{
    public function getName(): string
    {
        return "Fox";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(20);
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
        $drops = [VanillaItems::RABBIT_HIDE()->setCount(mt_rand(0, 1 * $lootingL))];
        if (mt_rand(1, 200) <= (5 + 2 * $lootingL)) {
            $drops[] = VanillaItems::RABBIT_FOOT()->setCount(1 * $lootingL);
        }
        if (mt_rand(1, 200) <= (5 + 2 * $lootingL)) {
            $drops[] = VanillaItems::EMERALD()->setCount(1 * $lootingL);
        }

        return $drops;
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.7, 0.6);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::FOX;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}
