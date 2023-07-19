<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;

class PiglinBrute extends SpawnerEntity
{
    public function getName(): string
    {
        return "Piglin Brute";
    }

    public function initEntity(CompoundTag  $nbt): void
    {
        $this->setMaxHealth(50);
        parent::initEntity($nbt);
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.9, 0.6);
    }

    public static function getNetworkTypeId(): string
    {
        return "minecraft:piglin_brute";
    }

    public function getXpDropAmount(): int
    {
        return 20;
    }
}
