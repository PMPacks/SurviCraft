<?php

namespace Heisenburger69\BurgerSpawners\utils;

use pocketmine\utils\TextFormat as C;
use Heisenburger69\BurgerSpawners\Main;

class ConfigManager
{
    public static function getMessage(string $messageTag): string
    {
        $message = (string) Main::getInstance()->getConfig()->get($messageTag);
        $message = C::colorize($message);
        return $message;
    }

    public static function getToggle(string $toggleTag): bool
    {
        $toggle = (bool) Main::getInstance()->getConfig()->get($toggleTag);
        return $toggle;
    }

    public static function getValue(string $valueTag): float
    {
        $float = (float)Main::getInstance()->getConfig()->get($valueTag);
        return $float;
    }

    /**
     * @return array|bool
     */
    public static function getArray(string $arrayTag)
    {
        $array = Main::getInstance()->getConfig()->get($arrayTag);
        return $array;
    }
}
