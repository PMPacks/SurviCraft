<?php

declare(strict_types=1);

namespace juqn\blockshop\utils;

use cqrl\Loader;
use cqrl\player\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use juqn\blockshop\BlockShop;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

final class Utils
{
    
    /**
     * @param Player $player
     * @return CompoundTag
     */
    public static function createBasicNBT(Player $player): CompoundTag
    {
        $nbt = CompoundTag::create()
                        ->setTag("Pos", new ListTag([
				new DoubleTag($player->getLocation()->x),
				new DoubleTag($player->getLocation()->y),
				new DoubleTag($player->getLocation()->z)
			]))
			->setTag("Motion", new ListTag([
				new DoubleTag($player->getMotion()->x),
				new DoubleTag($player->getMotion()->y),
				new DoubleTag($player->getMotion()->z)
			]))
			->setTag("Rotation", new ListTag([
				new FloatTag($player->getLocation()->yaw),
				new FloatTag($player->getLocation()->pitch)
			]));
        return $nbt;
    }

    /**
     * @param Player $player
     */
    public static function openBlockShop(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => ItemFactory::getInstance()->get(241, 1),
            1 => ItemFactory::getInstance()->get(241, 1),
            7 => ItemFactory::getInstance()->get(241, 1),
            8 => ItemFactory::getInstance()->get(241, 1),
            9 => ItemFactory::getInstance()->get(241, 1),
            17 => ItemFactory::getInstance()->get(241, 1),
            36 => ItemFactory::getInstance()->get(241, 1),
            44 => ItemFactory::getInstance()->get(241, 1),
            45 => ItemFactory::getInstance()->get(241, 1),
            46 => ItemFactory::getInstance()->get(241, 1),
            52 => ItemFactory::getInstance()->get(241, 1),
            53 => ItemFactory::getInstance()->get(241, 1)
        ]);
        
        $menu->getInventory()->setItem(12, ItemFactory::getInstance()->get(86)->setCustomName(TextFormat::colorize('&l&6Halloween Blocks')));
        $menu->getInventory()->setItem(13, ItemFactory::getInstance()->get(114)->setCustomName(TextFormat::colorize('&l&6Nether Blocks')));
        $menu->getInventory()->setItem(14, ItemFactory::getInstance()->get(79)->setCustomName(TextFormat::colorize('&l&6Winter Blocks')));
        
        $menu->getInventory()->setItem(21, ItemFactory::getInstance()->get(241, 8)->setCustomName(TextFormat::colorize('&l&6Stained Glass Blocks')));
        $menu->getInventory()->setItem(22, ItemFactory::getInstance()->get(155)->setCustomName(TextFormat::colorize('&l&6Quartz Blocks')));
        $menu->getInventory()->setItem(23, ItemFactory::getInstance()->get(32)->setCustomName(TextFormat::colorize('&l&6Bush Blocks')));
        
        $menu->getInventory()->setItem(30, ItemFactory::getInstance()->get(121)->setCustomName(TextFormat::colorize('&l&6End Blocks')));
        $menu->getInventory()->setItem(31, ItemFactory::getInstance()->get(108)->setCustomName(TextFormat::colorize('&l&6Stone Blocks')));
        $menu->getInventory()->setItem(32, ItemFactory::getInstance()->get(159)->setCustomName(TextFormat::colorize('&l&6Clay Blocks')));
        
        $menu->getInventory()->setItem(39, ItemFactory::getInstance()->get(37)->setCustomName(TextFormat::colorize('&l&6Flower Blocks')));
        $menu->getInventory()->setItem(40, ItemFactory::getInstance()->get(35)->setCustomName(TextFormat::colorize('&l&6Wool Blocks')));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $type = TextFormat::clean($item->getCustomName());
            
            if (self::getPage($type) !== null) {
                self::openPageBlockShop($player, $type);
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&7Block Shop'));
    }
    
    /**
     * @param Player $player
     * @param string $type
     */
    public static function openPageBlockShop(Player $player, string $type): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => ItemFactory::getInstance()->get(241, 1),
            1 => ItemFactory::getInstance()->get(241, 1),
            7 => ItemFactory::getInstance()->get(241, 1),
            8 => ItemFactory::getInstance()->get(241, 1),
            9 => ItemFactory::getInstance()->get(241, 1),
            17 => ItemFactory::getInstance()->get(241, 1),
            36 => ItemFactory::getInstance()->get(241, 1),
            44 => ItemFactory::getInstance()->get(241, 1),
            45 => ItemFactory::getInstance()->get(241, 1),
            46 => ItemFactory::getInstance()->get(241, 1),
            52 => ItemFactory::getInstance()->get(241, 1),
            53 => ItemFactory::getInstance()->get(241, 1)
        ] + self::getPage($type));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player $player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('price') !== null) {
                $newItem = ItemFactory::getInstance()->get($item->getId(), $item->getMeta(), $item->getCount());
                $newBalance = $player->getBalance() - $item->getNamedTag()->getInt('price');
                
                if ($newBalance < 0) {
                    $player->sendMessage(TextFormat::colorize('&cYou do not have money to buy this item'));
                    return $transaction->discard();
                }
                
                if ($player->getInventory()->canAddItem($newItem)) {
                    $player->getInventory()->addItem($newItem);
                    $player->setBalance($newBalance);
                } else $player->sendMessage(TextFormat::colorize('&cThis item cannot be stored in your inventory'));
            }
            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function (Player $player, $inventory) {
            BlockShop::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                if ($player->isOnline())
                    self::openBlockShop($player);
            }), 1);
        });
        $menu->send($player, TextFormat::colorize('&7' . $type));
    }
    
    /**
     * @param Item $item
     * @param int $price
     * @return Item
     */
    private static function prepareItem(Item $item, int $price): Item
    {
        $item->setLore([TextFormat::colorize('&fPrice: &6$' . $price)]);
        
        $namedtag = $item->getNamedTag();
        $namedtag->setInt('price', $price);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @param string $type
     * @return array|null
     */
    private static function getPage(string $type): ?array
    {
        switch ($type) {
            case 'Halloween Blocks':
                return [
                    22 => self::prepareItem(ItemFactory::getInstance()->get(86, 0, 16), 260),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(91, 0, 16), 260)
                ];
            
            case 'Nether Blocks':
                return [
                    12 => self::prepareItem(ItemFactory::getInstance()->get(405, 0, 16), 80),
                    13 => self::prepareItem(ItemFactory::getInstance()->get(113, 0, 16), 80),
                    14 => self::prepareItem(ItemFactory::getInstance()->get(114, 0, 16), 80)
                ];
                
            case 'Winter Blocks':
                return [
                    12 => self::prepareItem(ItemFactory::getInstance()->get(80, 0, 16), 125),
                    13 => self::prepareItem(ItemFactory::getInstance()->get(174, 0, 16), 125),
                    14 => self::prepareItem(ItemFactory::getInstance()->get(78, 0, 16), 125),
                    21 => self::prepareItem(ItemFactory::getInstance()->get(79, 0, 16), 125)
                ];
                
            case 'Stained Glass Blocks':
                return [
                    12 => self::prepareItem(ItemFactory::getInstance()->get(241, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::getInstance()->get(241, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::getInstance()->get(241, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::getInstance()->get(241, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::getInstance()->get(241, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::getInstance()->get(241, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::getInstance()->get(241, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::getInstance()->get(241, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::getInstance()->get(241, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::getInstance()->get(241, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(241, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::getInstance()->get(241, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::getInstance()->get(241, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::getInstance()->get(241, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::getInstance()->get(241, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::getInstance()->get(241, 15, 16), 300)
                ];
                
            case 'Quartz Blocks':
                return [
                    22 => self::prepareItem(ItemFactory::getInstance()->get(406, 0, 16), 225),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(156, 0, 16), 225)
                ];
                
            case 'Bush Blocks':
                return [
                    22 => self::prepareItem(ItemFactory::getInstance()->get(32, 0, 16), 200),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(2, 0, 16), 200)
                ];
                
            case 'End Blocks':
                return [
                    22 => self::prepareItem(ItemFactory::getInstance()->get(121, 0, 16), 5000)
                ];
                
            case 'Stone Blocks':
                return [
                    21 => self::prepareItem(ItemFactory::getInstance()->get(67, 0, 16), 250),
                    22 => self::prepareItem(ItemFactory::getInstance()->get(98, 0, 16), 250),
                    23 => self::prepareItem(ItemFactory::getInstance()->get(109, 0, 16), 250),
                    30 => self::prepareItem(ItemFactory::getInstance()->get(48, 0, 16), 250),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(1, 0, 16), 250)
                ];
                
            case 'Clay Blocks':
                return [
                    12 => self::prepareItem(ItemFactory::getInstance()->get(159, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::getInstance()->get(159, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::getInstance()->get(159, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::getInstance()->get(159, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::getInstance()->get(159, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::getInstance()->get(159, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::getInstance()->get(159, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::getInstance()->get(159, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::getInstance()->get(159, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::getInstance()->get(159, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(159, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::getInstance()->get(159, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::getInstance()->get(159, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::getInstance()->get(159, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::getInstance()->get(159, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::getInstance()->get(159, 15, 16), 300)
                ];
                
            case 'Flower Blocks':
                return [
                    21 => self::prepareItem(ItemFactory::getInstance()->get(37, 0, 16), 250),
                    22 => self::prepareItem(ItemFactory::getInstance()->get(38, 0, 16), 250),
                    23 => self::prepareItem(ItemFactory::getInstance()->get(37, 0, 16), 250)
                ];
                
            case 'Wool Blocks':
                return [
                    12 => self::prepareItem(ItemFactory::getInstance()->get(35, 0, 16), 300),
                    13 => self::prepareItem(ItemFactory::getInstance()->get(35, 1, 16), 300),
                    14 => self::prepareItem(ItemFactory::getInstance()->get(35, 2, 16), 300),
                    20 => self::prepareItem(ItemFactory::getInstance()->get(35, 3, 16), 300),
                    21 => self::prepareItem(ItemFactory::getInstance()->get(35, 4, 16), 300),
                    22 => self::prepareItem(ItemFactory::getInstance()->get(35, 5, 16), 300),
                    23 => self::prepareItem(ItemFactory::getInstance()->get(35, 6, 16), 300),
                    24 => self::prepareItem(ItemFactory::getInstance()->get(35, 7, 16), 300),
                    29 => self::prepareItem(ItemFactory::getInstance()->get(35, 8, 16), 300),
                    30 => self::prepareItem(ItemFactory::getInstance()->get(35, 9, 16), 300),
                    31 => self::prepareItem(ItemFactory::getInstance()->get(35, 10, 16), 300),
                    32 => self::prepareItem(ItemFactory::getInstance()->get(35, 11, 16), 300),
                    33 => self::prepareItem(ItemFactory::getInstance()->get(35, 12, 16), 300),
                    39 => self::prepareItem(ItemFactory::getInstance()->get(35, 13, 16), 300),
                    40 => self::prepareItem(ItemFactory::getInstance()->get(35, 14, 16), 300),
                    41 => self::prepareItem(ItemFactory::getInstance()->get(35, 15, 16), 300)
                ];
        }
        return null;
    }
}
