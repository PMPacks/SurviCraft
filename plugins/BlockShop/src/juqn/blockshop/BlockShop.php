<?php

declare(strict_types=1);

namespace juqn\blockshop;

use juqn\blockshop\command\BlockShopCommand;
use juqn\blockshop\entity\BlockShopEntity;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\utils\SingletonTrait;

/**
 * Class BlockShop
 * @package juqn\blockshop
 */
class BlockShop extends PluginBase
{
    use SingletonTrait;
    
    protected function onLoad(): void
    {
        self::setInstance($this);
    }
    
    protected function onEnable(): void
    {
        # InvMenu
        if (!InvMenuHandler::isRegistered())
            InvMenuHandler::register($this);

        # Register command

        # Register entity
        EntityFactory::getInstance()->register(BlockShopEntity::class, function (World $world, CompoundTag $nbt): BlockShopEntity {
            return new BlockShopEntity(EntityDataHelper::parseLocation($nbt, $world), BlockShopEntity::parseSkinNBT($nbt), $nbt);
        }, ['BlockShopEntity']);
    }
}