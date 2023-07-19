<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffMode;
use qxoap\StaffUtils;

class PlayerInfoCommand extends Command {

    public function __construct()
    {
        parent::__construct("pinfo", "Check Player Information", null, ["playerinfo"]);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->sendMessage(StaffUtils::ERROR."No tienes permisos para usar esto!");
            return;
        }

        if(!isset($args[0])){
            $player->sendMessage(StaffUtils::ERROR."Usa /pinfo (player)");
            return;
        }

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);

        if(!$target instanceof Player){
            $player->sendMessage(StaffUtils::ERROR."Este jugador no se encontro!");
            return;
        }

        $fac = StaffUtils::getInstance()->getPlayerFaction($target);
        $power = StaffUtils::getInstance()->getFactionPower($target);
        $life = $target->getHealth();
        $food = $target->getHungerManager()->getFood();
        $ip = $target->getNetworkSession()->getIp();
        $ping = $target->getNetworkSession()->getPing();
        $name = $target->getName();
        $country = StaffUtils::getInstance()->getCountry($target);
        $player->sendMessage("    §l§aSurvi§bCraft PLAYERS    \n §r§7Name: §a".$name."\n §7Faction: §a".$fac."\n §7Fac Power: §a".$power."\n §7Player Ip: §a".$ip."\n §7Player Ping: §a".$ping."\n §7Vida: §a".$life."\n §7Barra De Comida: §a".$food."\n §7Country: §a".$country."\n\n");
    }
}