<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Villager extends SpawnerEntity
{
    public function getName(): string
    {
        return "Villager";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.9, 0.6);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::VILLAGER;
    }
}
