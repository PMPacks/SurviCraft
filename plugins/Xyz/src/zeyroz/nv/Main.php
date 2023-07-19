<?php

namespace zeyroz\nv;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

    public static array $xyz = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new XYZTask(), 70);
        self::$xyz = [];
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()){
            case "nv":
                if($sender instanceof Player){
                    if($sender->getEffects()->has(VanillaEffects::NIGHT_VISION())){
                        $sender->getEffects()->remove(VanillaEffects::NIGHT_VISION());
                        $sender->sendMessage("§6§l»§r§f Vous avez bien retiré l'effet de night vision");
                    }else{
                        $sender->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 10000*20, 1, false));
                        $sender->sendMessage("§6§l»§r§f Vous avez bien ajouté l'effet de night vision");
                    }
                }
                break;
            case "xyz":
                if($sender instanceof Player){
                    if(isset(self::$xyz[$sender->getName()])){
                        unset(self::$xyz[$sender->getName()]);
                        $sender->sendMessage("§6§l»§r§f Vous avez désactivé les coordonnés");
                    }else{
                        self::$xyz[$sender->getName()] = true;
                        $sender->sendMessage("§6§l»§r§f Vous avez activé les coordonnés");
                    }
                }
                break;
        }return true;
    }
}