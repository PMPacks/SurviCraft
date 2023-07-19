<?php

namespace Jason8831\ClearLagg\Commands;


use Jason8831\ClearLagg\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class Forceclear extends Command
{

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $config = new Config(Main::getInstance()->getDataFolder()."config.yml", Config::YAML);
        if($sender instanceof Player){
            if($sender->hasPermission("forceclear.admin")){
                $entityCount = 0;
                foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world){
                    foreach ($world->getEntities() as $entity){
                        if(!$entity instanceof Player){
                            ++$entityCount;
                            $entity->flagForDespawn();
                        }
                    }
                }
                $message = str_replace( "{player}", $sender->getName() , $config->get("forceclear"));
                Server::getInstance()->broadcastMessage($message);
            }else{
                $sender->sendMessage("§f[§l§4ERROR§r§f]: vous n'avez pas la permission d'éxécuter cette commande");
            }
        }else{
            $sender->sendMessage("§f[§l§5ERROR§r§f]: vous ne pouvez pas éxécuter cette commande dans la console");
        }
    }
}