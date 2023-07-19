<?php

/*
 * Copyright (c) 2021 broki
 * brokiem/SimplePets is licensed under the MIT License
 */

declare(strict_types=1);

namespace brokiem\simplepets;

use brokiem\simplepets\database\Database;
use brokiem\simplepets\manager\PetManager;
use brokiem\simplepets\pets\base\BasePet;
use brokiem\simplepets\pets\base\CustomPet;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\player\Player;

final class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        SimplePets::getInstance()->addPlayer($player);
        Database::getInstance()->respawnPet($player);
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();

        if (isset(PetManager::getInstance()->getActivePets()[$player->getName()])) {
            foreach (PetManager::getInstance()->getActivePets()[$player->getName()] as $petName => $petId) {
                $pet = $player->getServer()->getWorldManager()->findEntity($petId);

                if ($pet instanceof BasePet || $pet instanceof CustomPet) {
                    $pet->despawn();
                    PetManager::getInstance()->removeActivePet($player, $petName);
                }
            }
        }

        SimplePets::getInstance()->removePlayer($player);
    }

    public function onDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();

        if ($entity instanceof BasePet || $entity instanceof CustomPet) {
            $event->cancel();
        }
    }

    public function onDataPacket(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();

        if ($player === null) {
            return;
        }

        if ($packet instanceof PlayerAuthInputPacket) {
            if ($packet->getMoveVecX() !== 0.0 or $packet->getMoveVecZ() !== 0.0) {
                $pet = PetManager::getInstance()->getRiddenPet($player);
                $pet?->walk($packet->getMoveVecX(), $packet->getMoveVecZ(), $player);
            }
        } elseif ($packet instanceof PlayerInputPacket) {
            if ($packet->motionX !== 0.0 or $packet->motionY !== 0.0) {
                $pet = PetManager::getInstance()->getRiddenPet($player);
                $pet?->walk($packet->motionX, $packet->motionY, $player);
            }
        } elseif ($packet instanceof InteractPacket) {
            if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
                $entity = $player->getServer()->getWorldManager()->findEntity($packet->targetActorRuntimeId);

                if ($entity instanceof BasePet || $entity instanceof CustomPet) {
                    if ($entity->getRider()->getXuid() === $player->getXuid()) {
                        $entity->unlink();
                    }
                }
            }
        }
    }

    public function onDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        PetManager::getInstance()->removeRiddenPet($player);
    }

    public function onTeleport(EntityTeleportEvent $event): void {
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            PetManager::getInstance()->removeRiddenPet($entity);

            if (isset(PetManager::getInstance()->getActivePets()[$entity->getName()])) {
                foreach (PetManager::getInstance()->getActivePets()[$entity->getName()] as $petName => $petId) {
                    $pet = $entity->getServer()->getWorldManager()->findEntity($petId);

                    if ($pet instanceof BasePet || $pet instanceof CustomPet) {
                        $location = clone $entity->getLocation();
                        ++$location->y;

                        $pet->teleport($location);
                    }
                }
            }
        }
    }
}