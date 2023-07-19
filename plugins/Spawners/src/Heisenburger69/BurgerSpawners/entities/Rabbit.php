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

class Rabbit extends SpawnerEntity
{
    public const DATA_RABBIT_TYPE = 18;

    public const TAG_RABBIT_TYPE = "RabbitType";

    public function initEntity(CompoundTag $nbt): void
    {
        $type = $this->getRandomRabbitType();
        if ($this->getNamedTag()->getTag(self::TAG_RABBIT_TYPE) == null) {
            $this->getNamedTag()->setInt(self::TAG_RABBIT_TYPE, $type);
        }

        $this->setMaxHealth(3);
        $this->getNetworkProperties()->setByte(self::DATA_RABBIT_TYPE, $type);
        parent::initEntity($nbt);
    }

    public function getRandomRabbitType(): int
    {
        $arr = [0, 1, 2, 3, 4, 5, 99];

        return $arr[array_rand($arr)];
    }

    public function getRabbitType(): int
    {
        return $this->getNamedTag()->getInt(self::TAG_RABBIT_TYPE);
    }

    public function getName(): string
    {
        return "Rabbit";
    }
    
    public function setRabbitType(int $type): void
    {
        $this->getNamedTag()->setInt(self::TAG_RABBIT_TYPE, $type);
    }

    public function getDrops(): array
    {
        $lootingL = 1;
        $cause = $this->lastDamageCause;
        if ($cause instanceof EntityDamageByEntityEvent) {
            $damager = $cause->getDamager();
            if ($damager instanceof Player) {

                $looting = $damager->getInventory()->getItemInHand()->getEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOOTING));
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

        return $drops;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.5, 0.4);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::RABBIT;
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}
