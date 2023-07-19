<?php

namespace qxoap\task;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use qxoap\StaffUtils;

class FreezeTask extends Task {

    public function onRun(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $online) {
            if(in_array($online->getName(), StaffUtils::getInstance()->freeze)){
                $online->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), null, 2, false));
                $online->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), null, 2, false));
                $online->sendTip("§c(Has Sido Congelado No Te Muevas)");
                $online->setImmobile(true);
            }
        }
    }
}