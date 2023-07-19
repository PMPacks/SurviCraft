<?php

namespace qxoap\task;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use qxoap\StaffUtils;

class VanishTask extends Task {

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $online) {
            if(in_array($online->getName(), StaffUtils::getInstance()->vanish)){
                $online->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), null, 2, false));
                $online->sendTip("§b(You Hare in Vanish)");
            }
        }
    }
}