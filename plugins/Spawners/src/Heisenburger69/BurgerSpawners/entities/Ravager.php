<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Ravager extends SpawnerEntity
{
    public function getName(): string
    {
        return "Ravager";
    }

    public function initEntity(CompoundTag $nbt): void
    {
        $this->setMaxHealth(100);
        parent::initEntity($nbt);
    }

    public function getDrops(): array
    {
        //Ravager drops aren't affected by Looting
        return [ItemFactory::getInstance()->get(329)];
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.9, 1.2);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::RAVAGER;
    }

    public function getXpDropAmount(): int
    {
        return 20;
    }
}
