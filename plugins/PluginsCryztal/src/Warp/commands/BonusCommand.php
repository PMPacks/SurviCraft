<?php

namespace Warp\commands;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;

use Warp\Main;
use Warp\PluginUtils;

class BonusCommand extends Command implements PluginOwned{
    
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        
        parent::__construct("bonus", "§r§fOpen menu bonus", "§cUse: /bonus", ["bazaarrui"]);
        $this->setPermission("bonusui.command");
        $this->setAliases(["bonusui"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(count($args) == 0){
            if($sender instanceof Player) {
                $this->plugin->getBonusUI($sender);
                PluginUtils::PlaySound($sender, "random.screenshot", 1, 1);
            } else {
                  $sender->sendMessage("Use this command in-game");
            }
        }
        return true;
    }
    
    public function getPlugin(): Plugin{
        return $this->plugin;
    }

    public function getOwningPlugin(): Main {
        return $this->plugin;
    }
}
