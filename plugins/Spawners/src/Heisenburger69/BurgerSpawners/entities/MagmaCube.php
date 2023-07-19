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

class MagmaCube extends SpawnerEntity
{
    public const SMALL_SIZE = 0;
    public const MEDIUM_SIZE = 1;
    public const LARGE_SIZE = 3;

    public const TAG_SIZE = "Size";

    public function getName(): string
    {
        return "Magma Cube";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(16);
        if ($this->getNamedTag()->getTag(self::TAG_SIZE) == null) {
            $this->getNamedTag()->setInt(self::TAG_SIZE, self::LARGE_SIZE);
        }

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
        return [VanillaItems::MAGMA_CREAM()->setCount(mt_rand(0, 1 * $lootingL))];
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(2.04, 2.04);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::MAGMA_CUBE;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 4);
    }
}
