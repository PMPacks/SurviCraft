<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use qxoap\StaffMode;
use qxoap\StaffUtils;

class UnMuteCommand extends Command {

    public function __construct()
    {
        parent::__construct("unmute", "Unmute A Player", null , []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if (!$player instanceof Player) return;

        if (!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
            $player->sendMessage(StaffUtils::ERROR . "No tienes permisos para usar esto!");
            return;
        }

        if (!isset($args[0])) {
            $player->sendMessage(StaffUtils::ERROR . "Usa /unmute (player)");
            return;
        }

        $banned = $args[0];
        $banInfo = StaffMode::getInstance()->mute->query("SELECT * FROM PlayersMute WHERE player = '$banned';");
        $array = $banInfo->fetchArray(SQLITE3_ASSOC);

        if (empty($array)) {
            $player->sendMessage(StaffUtils::ERROR . "Este jugador no se encontro!");
        } else {
            StaffMode::getInstance()->mute->query("DELETE FROM PlayersMute WHERE player = '$banned';");
            $player->sendMessage(StaffUtils::MUTE . "El jugador " . $banned . " fue desmuteado");
        }
    }
}