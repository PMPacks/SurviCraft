<?php

namespace GreekHCF;

use GreekHCF\backup\ItemsBackup;
use GreekHCF\commands\ParterPackagesCommand;

use MercuryMC\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TE;

class Greek extends PluginBase {

    public static $instance;
    
    public static $items;

    public function onLoad() : void {
        self::$instance = $this;
    }

    public function onEnable(): void {
        $this->getLogger()->info(TE::LIGHT_PURPLE."Partner Packages ".TE::GRAY."by ".TE::colorize("§6Koralop, §7Updated by §6Fayuth "));
        $this->getServer()->getCommandMap()->register("/pp", new ParterPackagesCommand());
        $this->getServer()->getPluginManager()->registerEvents(new GreekListener($this), $this);
        if(!is_dir($this->getDataFolder()."backup")){
        	@mkdir($this->getDataFolder()."backup");
        }
        ItemsBackup::init();
    }
    
    public function onDisable(): void{
        ItemsBackup::save();
    }

    public static function getInstance():Greek{
        return self::$instance;
    }

    public function getPermission(Player $player){
        if(!isset($this->permission[$player->getName()])){
            $this->permission[$player->getName()] = $player->addAttachment($this);
        }
        return $this->permission[$player->getName()];
    }


}

