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

class Spider extends SpawnerEntity
{
    public function getName(): string
    {
        return "Spider";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(16);
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
        $drops = [VanillaItems::STRING()->setCount(1 * $lootingL)];
        if (mt_rand(0, 199) < 5) {
            switch (mt_rand(0, 2)) {
                case 0:
                    $drops[] = VanillaItems::IRON_INGOT()->setCount(1 * $lootingL);
                    break;
                case 1:
                    $drops[] = VanillaItems::CARROT()->setCount(1 * $lootingL);
                    break;
                case 2:
                    $drops[] = VanillaItems::POTATO()->setCount(1 * $lootingL);
                    break;
            }
        }

        return $drops;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.9, 1.4);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::SPIDER;
    }

    public function getXpDropAmount(): int
    {
        return 5 + mt_rand(1, 3);
    }
}
