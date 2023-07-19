<?php

namespace FactionExtensionUI\commands;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;

use FactionExtensionUI\Main;

class FactionExtensionUICommand extends Command implements PluginOwned{
    
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        
        parent::__construct("factionui", "Open menu factionui/Abrir el menu de factionsUI", "usage: /factionui", ["fui"]);
        $this->setPermission("factionui.command");
        $this->setAliases(["fui"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(count($args) == 0){
            if($sender instanceof Player) {
                $this->plugin->FactionExtensionUI($sender);
            } else {
                  $sender->sendMessage("Use this command in-game");
            }
        }
        return true;
    }
    
    public function getPlugin(): Plugin{
        return $this->plugin;
    }

    public function getOwningPlugin(): Main{
        return $this->plugin;
    }
}
