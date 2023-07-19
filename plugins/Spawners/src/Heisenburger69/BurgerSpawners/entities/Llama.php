<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Llama extends SpawnerEntity
{
    public const CREAMY = 0;
    public const WHITE = 1;
    public const BROWN = 2;
    public const GRAY = 3;

    public const TAG_VARIANT = "Variant";

    public function getName(): string
    {
        return "Llama";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(15);
        if ($this->getNamedTag()->getTag(self::TAG_VARIANT) == null) {
            $this->getNamedTag()->setInt(self::TAG_VARIANT, self::CREAMY);
        }
        parent::initEntity($nbt);
    }

    public function getRandomVariant(): int
    {
        $arr = [self::CREAMY, self::WHITE, self::BROWN, self::GRAY];

        return $arr[array_rand($arr)];
    }

    public function getLlamaVariant(): int
    {
        return $this->getNamedTag()->getInt(self::TAG_VARIANT);
    }

    public function setLlamaVariant(int $type): void
    {
        $this->getNamedTag()->setInt(self::TAG_VARIANT, $type);
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
            VanillaItems::LEATHER()->setCount(mt_rand(0, 2 * $lootingL)),
        ];
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.87, 0.9);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::LLAMA;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}
