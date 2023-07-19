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

class BazaarCommand extends Command implements PluginOwned{
    
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        
        parent::__construct("bazaar", "§r§fOpen menu sellui", "§cUse: /baazar", ["bazaarrui"]);
        $this->setPermission("bazaarui.command");
        $this->setAliases(["bazaarui"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(count($args) == 0){
            if($sender instanceof Player) {
                $this->plugin->getBazaarUI($sender);
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
