<?php

namespace qxoap\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class FreezeCommand extends Command {

    public function __construct()
    {
        parent::__construct("freeze", "Freeze A Player", null, []);
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!$player instanceof Player)return;

        if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->sendMessage(StaffUtils::ERROR."No tienes permisos para usar esto!");
            return;
        }

        if(!isset($args[0])){
            $player->sendMessage(StaffUtils::ERROR."Usa /freeze (player)");
            return;
        }

        $target = Server::getInstance()->getPlayerByPrefix($args[0]);

        if(!$target instanceof Player){
            $player->sendMessage(StaffUtils::ERROR."Este Jugador No Se Encontro");
            return;
        }

        if(!in_array($target->getName(), StaffUtils::getInstance()->freeze)){
            StaffUtils::getInstance()->freeze[] = $target->getName();
            $target->sendMessage(StaffUtils::FREEZE."Has sido congelado por §a".$player->getName());
            foreach (Server::getInstance()->getOnlinePlayers() as $players){
                $players->sendMessage(StaffUtils::FREEZE."El Jugador §a".$target->getName()." §7Fue congelado por §c".$player->getName());
            }
            return;
        }
        if(in_array($target->getName(), StaffUtils::getInstance()->freeze)){
            unset(StaffUtils::getInstance()->freeze[array_search($target->getName(), StaffUtils::getInstance()->freeze)]);
            $target->sendTip(StaffUtils::FREEZE."Has sido desongelado, evita esto para la proxima");
            $target->getEffects()->clear();
            $target->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 40, 2, false));
            $target->setImmobile(false);
            foreach (Server::getInstance()->getOnlinePlayers() as $players){
                $players->sendTip(StaffUtils::FREEZE."El jugador §c".$target->getName()." §7fue descongelado por §a".$player->getName());
            }
        }
    }
}