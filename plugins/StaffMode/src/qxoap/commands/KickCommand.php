<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class KickCommand extends Command {

    public function __construct()
    {
        parent::__construct("kick", "Kick A Player", null, []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if (!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage(StaffUtils::ERROR . "No tienes permisos para usar este comando");
            return;
        }

        if(!isset($args[0])){
            $player->sendMessage(StaffUtils::ERROR."Usa /kick [player] [reason]");
            return;
        }
        if(!isset($args[1])){
            $player->sendMessage(StaffUtils::ERROR."Usa /kick [player] [reason]");
            return;
        }
        $target = Server::getInstance()->getPlayerByPrefix($args[0]);
        $s = array_shift($args);
        $reason = implode(" ", $args);
        if($target instanceof Player){
            $target->kick("§7You has been kicked from §l§aSurvi§bCraft \n§r§7Fuiste kikeado por §a".$player->getName()."\n§7Rason: §a".$reason);
            foreach (Server::getInstance()->getOnlinePlayers() as $online) {
                $online->sendMessage(StaffUtils::PREFIX."El Jugador §a".$target->getName()." §7Fue kikeado por el staff §a".$player->getName()." §7Rason: §a".$reason);
            }
        }else{
            $player->sendMessage(StaffUtils::ERROR."Este jugador no se encontro");
        }
    }
}