<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Endermite extends SpawnerEntity
{
    public function getName(): string
    {
        return "Endermite";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(8);
        parent::initEntity($nbt);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ENDERMITE;
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.3, 0.4);
    }

    public function getXpDropAmount(): int
    {
        return 3;
    }
}
