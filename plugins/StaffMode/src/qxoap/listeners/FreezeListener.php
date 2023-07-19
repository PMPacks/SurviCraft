<?php

namespace qxoap\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\player\Player;
use qxoap\StaffUtils;

class FreezeListener implements Listener
{

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }
    }

    public function onDrop(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }

    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();

        if(!$entity instanceof Player)return;

        if (in_array($entity->getName(), StaffUtils::getInstance()->freeze)) {
            $event->cancel();
        }
    }

    public function onPickup(EntityItemPickupEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (in_array($entity->getName(), StaffUtils::getInstance()->freeze)) {
                $event->cancel();
            }
        }

    }

    public function onCombust(EntityCombustEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (in_array($entity->getName(), StaffUtils::getInstance()->freeze)) {
                $event->cancel();
            }
        }

    }

    public function onTransaction(InventoryTransactionEvent $event)
    {
        $player = $event->getTransaction()->getSource();
        if ($player instanceof Player) {
            if (in_array($player->getName(), StaffUtils::getInstance()->freeze)) {
                $event->cancel();
            }
        }
    }
}