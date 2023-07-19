<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Husk extends SpawnerEntity
{
    public function getName(): string
    {
        return "Husk";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(20);
        parent::initEntity($nbt);
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(2.01875, 0.6375);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::HUSK;
    }

    public function getXpDropAmount(): int
    {
        return 5 + mt_rand(1, 3);
    }
}
