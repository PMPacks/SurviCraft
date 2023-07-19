<?php

namespace qxoap\listeners;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use qxoap\StaffUtils;

class ItemListener implements Listener {

    public function onPlayerUse(PlayerItemUseEvent $ev)
    {
        $player = $ev->getPlayer();
        $item = $ev->getItem()->getCustomName();
        if($item === "§l§3Teleport"){
            if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                $ev->cancel();
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $player->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            StaffUtils::getInstance()->sendTeleportForm($player);
            $ev->cancel();
        }
        if($item === "§l§3Vanish"){
            if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                $ev->cancel();
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $player->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->vanish)){
                StaffUtils::getInstance()->vanish[] = $player->getName();
                $player->sendMessage(StaffUtils::PREFIX."Has Entrado En El VanishMode");
                foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                    $players->hidePlayer($player);
                }
                $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(351, 8)->setCustomName("§l§cUnvanish"));
                $ev->cancel();
            }
        }
        if($item === "§l§cUnvanish"){
            if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $player->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            if(in_array($player->getName(), StaffUtils::getInstance()->vanish)){
                unset(StaffUtils::getInstance()->vanish[array_search($player->getName(), StaffUtils::getInstance()->vanish)]);
                $player->sendMessage(StaffUtils::PREFIX."Has Salido Del VanishMode");
                StaffUtils::getInstance()->sendKit($player);
                foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                    $players->showPlayer($player);
                }
            }
            $ev->cancel();
        }
        if($item === "§l§3Ban Player"){
            if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $player->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            StaffUtils::getInstance()->sendBanForm($player);
        }
        if($item === "§l§3Mute Player"){
            if(!$player->hasPermission("cryztal.staff") && !$player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $player->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            StaffUtils::getInstance()->sendMuteForm($player);
        }
    }

    public function onPlayerHit(EntityDamageByEntityEvent $ev)
    {
        $player = $ev->getEntity();
        if (!$player instanceof Player) return;
        $damager = $ev->getDamager();
        if (!$damager instanceof Player) return;
        $item = $damager->getInventory()->getItemInHand()->getCustomName();
        if($item === "§l§3Freeze"){
            if(!$damager->hasPermission("cryztal.staff") && !$damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $damager->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                $ev->cancel();
                return;
            }
            if(!in_array($damager->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            if(in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."No Lo Puedes Congelar!, Tambien Esta En StaffMode!");
                $ev->cancel();
                return;
            }
            if(!in_array($player->getName(), StaffUtils::getInstance()->freeze)){
                StaffUtils::getInstance()->freeze[] = $player->getName();
                $player->sendMessage(StaffUtils::FREEZE."You're frozen, do what the staff say");
                Server::getInstance()->broadcastMessage(StaffUtils::FREEZE."The player §a".$player->getName()." §7Fue Congelado Por §c".$damager->getName());
                $ev->cancel();
            }else if(in_array($player->getName(), StaffUtils::getInstance()->freeze)){
                unset(StaffUtils::getInstance()->freeze[array_search($player->getName(), StaffUtils::getInstance()->freeze)]);
                Server::getInstance()->broadcastMessage(StaffUtils::FREEZE."The player §a".$player->getName()." §7Fue Descongelado Por §c".$damager->getName());
                $player->getEffects()->clear();
                $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 40, 2, false));
                $player->sendMessage(StaffUtils::FREEZE."You've been Thawed, prevent it from happening again");
                $player->setImmobile(false);
                $ev->cancel();
            }
        }
        if($item === "§l§3Kick Player"){
            if(!$damager->hasPermission("cryztal.staff") && !$damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $damager->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                $ev->cancel();
                return;
            }
            if(!in_array($damager->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            if(in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."No Puedes Hacer Esto, Tambien esta en staffmode!");
                $ev->cancel();
                return;
            }
            $player->kick("§7You Has Been Kicked By §a".$damager->getName());
            $ev->cancel();
        }
        if($item === "§l§3Player Info"){
            if(!$damager->hasPermission("cryztal.staff") && !$damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                $player->sendMessage(StaffUtils::PREFIX."No Tienes Permisos Suficientes Para Usar Esto!!!");
                $ev->cancel();
                return;
            }
            if(!in_array($damager->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."Solo Puedes Usar Esto En StaffMode!");
                $ev->cancel();
                return;
            }
            if(in_array($player->getName(), StaffUtils::getInstance()->staff)){
                $damager->sendMessage(StaffUtils::PREFIX."No Puedes Hacer Esto, Tambien esta en staffmode!");
                $ev->cancel();
                return;
            }

            $fact = StaffUtils::getInstance()->getPlayerFaction($player);
            $power = StaffUtils::getInstance()->getFactionPower($player);
            $life = $player->getHealth();
            $food = $player->getHungerManager()->getFood();
            $ip = $player->getNetworkSession()->getIp();
            $ping = $player->getNetworkSession()->getPing();
            $name = $player->getName();
            $country = StaffUtils::getInstance()->getCountry($player);
            $damager->sendMessage("    §l§aSurvi§bCraft PLAYERS    \n §r§7Name: §a".$name."\n §fFaction: §a".$fact."\n §fFac Power: §a".$power."\n §fPlayer Ip: §a".$ip."\n §fPlayer Ping: §a".$ping."\n §fVida: §a".$life."\n §fBarra De Comida: §a".$food."\n §fPlayer Country: §a".$country."\n\n");
            $ev->cancel();
        }
    }
}