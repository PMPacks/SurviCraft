<?php

declare(strict_types=1);

namespace phuongaz\boss;

use phuongaz\boss\entity\BossEntity;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;

class EventHandler implements Listener {

    /**
    * @priority HIGH
     */
    public function onEntityDeath(EntityDeathEvent $event) :void {
        $entity = $event->getEntity();
        if($entity instanceof BossEntity){
            $entity->cooldownRespawn();
        }
    }
}