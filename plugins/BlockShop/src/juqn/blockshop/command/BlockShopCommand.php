<?php

declare(strict_types=1);

namespace juqn\blockshop\command;

use cqrl\player\Player;
use cqrl\Loader;
use juqn\blockshop\entity\BlockShopEntity;
use juqn\blockshop\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class BlockShopCommand extends Command
{

    /**
     * BlockShopCommand construct.
     */
    public function __construct()
    {
        parent::__construct('blockshop', 'Command for blockshop');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (isset($args[0]) && $sender->hasPermission('npc.blockshop.command')) {
            if ($args[0] === 'npc') {
                $entity = new BlockShopEntity($sender->getLocation(), $sender->getSkin(), Utils::createBasicNBT($sender));
                $entity->spawnToAll();
                return;
            }
        }
        Utils::openBlockShop($sender);
    }
}