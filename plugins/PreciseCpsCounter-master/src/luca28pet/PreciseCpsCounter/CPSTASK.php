<?php

namespace luca28pet\PreciseCpsCounter;

use JsonException;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use zeyroz\lobby\Main;

class CPSTASK extends Task
{

    private $player;

    public function __construct(Player $player){
        $this->player = $player;
    }

    public function onRun(): void
    {
        $player = $this->player;
        if($player instanceof Player){
            $cps = \luca28pet\PreciseCpsCounter\Main::getInstance()->getCps($player);
            $player->sendTip("§l§9»§r§f " . $cps);
        }
    }
}