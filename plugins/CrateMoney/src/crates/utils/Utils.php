<?php

namespace crates\utils;

use crates\Main;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat as C;

class Utils
{
    public static function constructCrateItem(string $crateName): ?Item
    {
        $config = Main::getInstance()->getConfig();
        $item = ItemFactory::getInstance()->get($config->getNested("crates.$crateName.item.id", 0));

        if ($item->getId() === ItemIds::AIR) {
            return $item;
        }

        $item->setCustomName(C::RESET . C::colorize($config->getNested("crates.$crateName.item.name", "")));
        $lore = [];

        foreach ($config->getNested("crates.$crateName.item.lore") as $line) {
            $lore[] = C::RESET . C::colorize($line);
        }

        $item->setLore($lore);
        $item->getNamedTag()->setString('monthlycrate', $crateName);

        return $item;
    }

    /**
     * @param string $crateName
     *
     * @return Item[]
     */
    public static function constructCrateRewards(string $crateName): array
    {
        $config = Main::getInstance()->getConfig();
        $itemData = $config->getNested("crates.$crateName.rewards");
        return self::extracted($itemData);
    }

    public static function extracted(mixed $itemData): array
    {
        $items = [];
        foreach ($itemData as $itemDatum) {
            $item = ItemFactory::getInstance()->get($itemDatum["id"]);
            $item->setCustomName(C::RESET . C::colorize($itemDatum["name"]));
            $item->getNamedTag()->setString('cratecommand', $itemDatum["command"]);
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @param string $crateName
     *
     * @return Item[]
     */
    public static function constructBonusRewards(string $crateName): array
    {
        $config = Main::getInstance()->getConfig();
        $itemData = $config->getNested("crates.$crateName.bonus-rewards");
        return self::extracted($itemData);
    }

    /**
     * @param float $centerX
     * @param float $centerZ
     * @param float $radius
     * @param int   $numberOfTurns
     *
     * @return Point[]
     */
    public static function getPointsOfRotation(float $centerX, float $centerZ, float $radius, int $numberOfTurns): array
    {
        $points = [];
        $angleDiff = 360 / $numberOfTurns;
        for ($i = 0; $i < $numberOfTurns; $i++) {
            $degreeTurn = deg2rad($i * $angleDiff);
            $points[] = new Point($centerX + ($radius * cos($degreeTurn)), $centerZ + ($radius * sin($degreeTurn)));
        }
        return $points;
    }
}