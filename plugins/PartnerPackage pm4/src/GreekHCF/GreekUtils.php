<?php

namespace GreekHCF;


use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TE;

/**
 * Class GreekUtils
 * @package GreekHCF
 */
class GreekUtils
{

    /**
     * @param Player $player
     * @param int $int
     */
    public static function addPartner(Player $player, int $int)
    {
        $partner = ItemFactory::getInstance()->get(ItemIds::ENDER_CHEST, 0, $int);
        $partner->setCustomName(TE::BOLD . TE::LIGHT_PURPLE . "Partner Packages");
        //$partner->setLore([TE::GRAY.""]);
        $partner->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
        $player->getInventory()->addItem($partner);
        $player->sendMessage(TE::GOLD . "§r§6You have revived§r: " . TE::LIGHT_PURPLE . "§d§l§oPartner Packages");;
    }

    /**
     * @param $config
     * @return bool|mixed
     */
    public static function getConfiguration($config)
    {
        return Greek::getInstance()->getConfig()->get($config);
    }

    /**
     * @param Item $item
     * @return array
     */
    public static function itemSerialize(Item $item): array
    {
        $data = $item->jsonSerialize();
        return $data;
    }

    /**
     * @param array $items
     * @return Item
     */
    public static function itemDeserialize(array $items): Item
    {
        $item = Item::jsonDeserialize($items);
        return $item;
    }



}