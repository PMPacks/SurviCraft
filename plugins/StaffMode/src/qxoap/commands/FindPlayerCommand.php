<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class FindPlayerCommand extends Command {

    public function __construct()
    {
        parent::__construct("findplayer", "Find A Player", null, []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->sendMessage(StaffUtils::ERROR."No tienes permisos para usar esto!");
            return;
        }

        if(!isset($args[0])){
            $player->sendMessage(StaffUtils::ERROR."Usa /findplayer (player)");
            return;
        }

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);

        if(!$target instanceof Player){
            $player->sendMessage(StaffUtils::ERROR."Este jugador no se encontro");
            return;
        }

        $x = number_format($target->getPosition()->getX(), 1);
        $y = number_format($target->getPosition()->getY(), 1);
        $z = number_format($target->getPosition()->getZ(), 1);
        $world = $target->getWorld()->getDisplayName();
        $player->sendMessage(StaffUtils::PREFIX."\n §7Jugador: §a".$target->getName()."\n §7Mundo: §a".$world."\n §7Coords: \n    §7X: §a".$x."\n    §7Y".$y."\n    §7Z: §a".$z."\n");
    }
}