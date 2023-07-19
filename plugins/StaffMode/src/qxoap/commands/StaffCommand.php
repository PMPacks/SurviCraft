<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class StaffCommand extends Command {

    public function __construct()
    {
        parent::__construct("staff", "Enter In StaffMode", null, []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->sendMessage(StaffUtils::ERROR."No tienes permisos para usar esto!");
            return;
        }

        if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
            StaffUtils::getInstance()->staff[] = $player->getName();
            StaffUtils::getInstance()->items_backup[$player->getName()] = $player->getInventory()->getContents();
            StaffUtils::getInstance()->armor_backup[$player->getName()] = $player->getArmorInventory()->getContents();
            StaffUtils::getInstance()->offhand_backup[$player->getName()] = $player->getOffHandInventory()->getContents();
            $player->getArmorInventory()->clearAll();
            $player->getInventory()->clearAll();
            $player->getOffHandInventory()->clearAll();
            $player->sendTip(StaffUtils::PREFIX."Has entrado al modo Staff de §aSurvi§bCraft");
            StaffUtils::getInstance()->sendKit($player);
            $player->setAllowFlight(true);
            $player->setFlying(true);
            return;
        }
        if(in_array($player->getName(), StaffUtils::getInstance()->staff)){
            unset(StaffUtils::getInstance()->staff[array_search($player->getName(), StaffUtils::getInstance()->staff)]);
            $player->sendTip(StaffUtils::PREFIX."Has Salido del Modo Staff!");
            $player->getOffHandInventory()->setContents(StaffUtils::getInstance()->offhand_backup[$player->getName()]);
            $player->getInventory()->setContents(StaffUtils::getInstance()->items_backup[$player->getName()]);
            $player->getArmorInventory()->setContents(StaffUtils::getInstance()->armor_backup[$player->getName()]);
            $player->setAllowFlight(false);
            $player->setFlying(false);
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                $onlinePlayer->showPlayer($player);
            }
        }
    }
}