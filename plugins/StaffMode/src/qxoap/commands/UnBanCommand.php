<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use qxoap\StaffMode;
use qxoap\StaffUtils;

class UnBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("unban", "Unban A Player", null, []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if (!$player instanceof Player) return;

        if (!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage(StaffUtils::ERROR . "No tienes permisos para usar esto!");
            return;
        }

        if (!isset($args[0])) {
            $player->sendMessage(StaffUtils::ERROR . "Usa /unban (player)");
            return;
        }

        $target = $args[0];
        $banInfo = StaffMode::getInstance()->ban->query("SELECT * FROM PlayersBan WHERE player = '$target';");
        $array = $banInfo->fetchArray(SQLITE3_ASSOC);

        if (empty($array)) {
            $player->sendMessage(StaffUtils::ERROR . "Este jugador no se encontro!");
        } else {
            StaffMode::getInstance()->ban->query("DELETE FROM PlayersBan WHERE player = '$target';");
            $player->sendMessage(StaffUtils::PREFIX . "El jugador " . $target . " fue Desbaneado");
        }
    }
}