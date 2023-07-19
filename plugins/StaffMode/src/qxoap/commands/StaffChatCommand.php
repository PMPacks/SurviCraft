<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class StaffChatCommand extends Command {

    public function __construct()
    {
        parent::__construct("sc", "Escribe en el chat de los Staffs", null, ["staffchat"]);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->sendMessage(StaffUtils::ERROR."No tienes permisos para usar esto!");
            return;
        }

        if(!isset($args[0])){
            $player->sendMessage(StaffUtils::ERROR."Usa /sc (message)");
            return;
        }

        $message = implode(" ", $args);

        foreach (Server::getInstance()->getOnlinePlayers() as $online){
            if(!$online->hasPermission("cryztal.staff") && !$online->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                return;
            }
            $online->sendMessage(StaffUtils::SC.$online->getName()." : §7".$message);
        }
    }
}