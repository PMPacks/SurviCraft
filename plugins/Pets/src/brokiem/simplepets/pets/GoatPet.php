<?php

/*
 * Copyright (c) 2021 broki
 * brokiem/SimplePets is licensed under the MIT License
 */

declare(strict_types=1);

namespace brokiem\simplepets\pets;

use brokiem\simplepets\pets\base\BasePet;
use pocketmine\entity\EntitySizeInfo;

class GoatPet extends BasePet {

    public static function getNetworkTypeId(): string {
        return "minecraft:goat";
    }

    public function getPetType(): string {
        return "GoatPet";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo {
        return new EntitySizeInfo(0.7, 0.7);
    }
}