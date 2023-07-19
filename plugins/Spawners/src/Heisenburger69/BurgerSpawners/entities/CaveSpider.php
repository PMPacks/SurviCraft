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

class CaveSpider extends SpawnerEntity
{
    public function getName(): string
    {
        return "Cave Spider";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(12);
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
        $drops = [
            VanillaItems::STRING()->setCount(1 * $lootingL)
        ];

        if (mt_rand(1, 3) == 2) {
            $lastDamage = $this->getLastDamageCause();
            if ($lastDamage instanceof EntityDamageByEntityEvent) {
                $ent = $lastDamage->getDamager();
                if ($ent instanceof Player) {
                    $drops[] = VanillaItems::SPIDER_EYE()->setCount(mt_rand(0, 1 * $lootingL));
                }
            }
        }

        return $drops;
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.5, 1);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::CAVE_SPIDER;
    }

    public function getXpDropAmount(): int
    {
        return 5 + mt_rand(1, 3);
    }
}
