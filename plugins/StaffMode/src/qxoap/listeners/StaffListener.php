<?php

namespace qxoap\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class StaffListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if ($player->hasPermission("cryztal.staff")) {
            Server::getInstance()->broadcastMessage(StaffUtils::PREFIX . "§6El Staff llamado §a" . $player->getName() . " §6entro al servidor §aSurvi§bCraft");
        }
        foreach (Server::getInstance()->getOnlinePlayers() as $players){
            if(in_array($players->getName(), StaffUtils::getInstance()->vanish)){
                $player->hidePlayer($players);
            }
        }
        if(in_array($player->getName(), StaffUtils::getInstance()->vanish)){
            unset(StaffUtils::getInstance()->vanish[array_search($player->getName(), StaffUtils::getInstance()->vanish)]);
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if ($player->hasPermission("cryztal.staff")) {
            Server::getInstance()->broadcastMessage(StaffUtils::PREFIX . "El Staff Llamado §a" . $player->getName() . " §7Ah Salido del Servidor §aSurvi§bCraft");
        }

        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->getEffects()->clear();
            $player->setFlying(false);
            $player->setAllowFlight(false);
            $player->setSilent(false);

            unset(StaffUtils::getInstance()->staff[array_search($player->getName(), StaffUtils::getInstance()->staff)]);
            $player->getArmorInventory()->setContents(StaffUtils::getInstance()->armor_backup[$player->getName()]);
            $player->getInventory()->setContents(StaffUtils::getInstance()->items_backup[$player->getName()]);
            $player->getOffHandInventory()->setContents(StaffUtils::getInstance()->offhand_backup[$player->getName()]);
            unset(StaffUtils::getInstance()->vanish[array_search($player->getName(), StaffUtils::getInstance()->vanish)]);
            foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                $players->showPlayer($player);
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->setDrops([]);
        }
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            StaffUtils::getInstance()->sendKit($player);
        }
    }

    public function onKick(PlayerKickEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->getEffects()->clear();
            $player->extinguish();
            $player->setFlying(false);
            $player->setAllowFlight(false);

            unset(StaffUtils::getInstance()->staff[array_search($player->getName(), StaffUtils::getInstance()->staff)]);
            $player->getArmorInventory()->setContents(StaffUtils::getInstance()->armor_backup[$player->getName()]);
            $player->getInventory()->setContents(StaffUtils::getInstance()->items_backup[$player->getName()]);
            unset(StaffUtils::getInstance()->vanish[array_search($player->getName(), StaffUtils::getInstance()->vanish)]);
            $player->getOffHandInventory()->setContents(StaffUtils::getInstance()->offhand_backup[$player->getName()]);
        }
    }

    public function onDrop(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }

    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();

        if(!$entity instanceof Player)return;

        if (in_array($entity->getName(), StaffUtils::getInstance()->staff)) {
            $event->cancel();
        }
    }

    public function onPickup(EntityItemPickupEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (in_array($entity->getName(), StaffUtils::getInstance()->staff)) {
                $event->cancel();
            }
        }

    }

    public function onCombust(EntityCombustEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (in_array($entity->getName(), StaffUtils::getInstance()->staff)) {
                $event->cancel();
            }
        }

    }

    public function onTransaction(InventoryTransactionEvent $event)
    {
        $player = $event->getTransaction()->getSource();
        if ($player instanceof Player) {
            if (in_array($player->getName(), StaffUtils::getInstance()->staff)) {
                $event->cancel();
            }
        }
    }
}