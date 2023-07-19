<?php

namespace GreekHCF\modules;

use GreekHCF\Greek;
use GreekHCF\GreekUtils;

use pocketmine\utils\Config;
use pocketmine\item\Item;

/**
 * Class PartnerPackage
 * @package GreekHCF\modules
 */
class PartnerPackage
{

    /** @var array|null */
    public static $items = [];

    /**
     * PartnerPackage constructor.
     * @param array|null $items
     */
    public function __construct(?array $items = [])
    {
        self::$items = $items;
        $itemData = [];
        $file = new Config(Greek::getInstance()->getDataFolder() . "backup" . DIRECTORY_SEPARATOR . "items.yml", Config::YAML);
        foreach (self::getItems() as $slot => $item) {
            $itemData[$slot] = GreekUtils::itemSerialize($item);
        }
        $file->set("items", $itemData);
        $file->save();
    }

    /**
     * @return array
     */
    public static function getItems(): array
    {
        return self::$items;
    }

    public static function getRandomItem(): Item
    {
        return self::$items[array_rand(self::$items)];
    }
}