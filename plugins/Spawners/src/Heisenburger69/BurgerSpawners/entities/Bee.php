<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bee extends SpawnerEntity
{
    public function getName(): string
    {
        return "Bee";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(10);
        parent::initEntity($nbt);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::BLAZE;
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.6, 0.6);
    }

    public function getXpDropAmount(): int
    {
        return mt_rand(1, 3);
    }
}
