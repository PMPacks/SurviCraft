<?php

namespace zeyroz\nv;

use pocketmine\scheduler\Task;
use pocketmine\Server;

class XYZTask extends Task{

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            if(isset(Main::$xyz[$player->getName()])){
                $x = round($player->getPosition()->getX());
                $y = round($player->getPosition()->getY());
                $z = round($player->getPosition()->getZ());
                $player->sendPopup("§6-§f X:§e {$x}§f Y:§e {$y} §fZ:§e {$z} §6-");
            }
        }
    }
}