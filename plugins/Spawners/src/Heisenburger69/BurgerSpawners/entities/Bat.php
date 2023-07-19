<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Bat extends SpawnerEntity
{
    public function getName(): string
    {
        return "Bat";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(6);
        parent::initEntity($nbt);
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.9, 0.5);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::BLAZE;
    }
}
