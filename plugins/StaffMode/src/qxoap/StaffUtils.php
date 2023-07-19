<?php

namespace qxoap;

use Ayzrix\SimpleFaction\API\FactionsAPI;
use Forms\FormAPI\CustomForm;
use Forms\FormAPI\SimpleForm;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class StaffUtils
{
    use SingletonTrait;

    public const PREFIX = "§l§c【§bStaff §aSurvi§bCrafty§c】 §c";

    public const FREEZE = "§l§c【§aSurvi§bCraft §bFreeze§c】 §c";

    public const ERROR = "§l§c【§aSurvi§bCraft §cError§c】 §c";

    public const SC = "§l§c【§aSurvi§bCraft §cStaffChat§c】 §c";

    public const MUTE = "§l§c【§aSurvi§bCraft §6Mute§c】 §c";

    public $target;

    public $freeze = [];

    public $items_backup = [];

    public $armor_backup = [];

    public $offhand_backup = [];

    public $staff = [];

    public $vanish = [];

    public function __construct()
    {
        self::setInstance($this);
    }

    public function sendTeleportForm(Player $player): void
    {
        $menu = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            $target = Server::getInstance()->getPlayerExact($data);
            
            if(!$target instanceof Player){
                return;
            }
            
            if ($target->getName() == $player->getName()) {
                $player->sendMessage(self::ERROR . "Por que intentas teletransportarte a ti mismo?");
                return;
            }
            $player->teleport($target->getPosition());
            $player->sendMessage(self::PREFIX . "Te has teletransportado hacia §a" . $target->getName());

        });
        $menu->setTitle("§l§aSurvi§bCraft Players");
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $menu->addButton($onlinePlayer->getName() . "\n§8Tap To Teleport", -1, "", $onlinePlayer->getName());
        }
        $player->sendForm($menu);
    }

    public function sendKit(Player $player): void
    {
        $inv = $player->getInventory();
        $freeze = ItemFactory::getInstance()->get(79, 0)->setCustomName("§l§3Freeze");
        $teleport = VanillaItems::COMPASS()->setCustomName("§l§3Teleport");
        $playerinfo = VanillaItems::STICK()->setCustomName("§l§3Player Info");
        $kick = VanillaItems::DIAMOND_SWORD()->setCustomName("§l§3Kick Player");
        $vanish = ItemFactory::getInstance()->get(351, 10)->setCustomName("§l§3Vanish");
        $ban = ItemFactory::getInstance()->get(ItemIds::ANVIL, 0)->setCustomName("§l§3Ban Player");
        $mute = VanillaItems::BLAZE_POWDER()->setCustomName("§l§3Mute Player");
        $inv->setItem(1, $teleport);
        $inv->setItem(2, $freeze);
        $inv->setItem(3, $playerinfo);
        $inv->setItem(4, $kick);
        $inv->setItem(5, $vanish);
        $inv->setItem(6, $ban);
        $inv->setItem(7, $mute);
    }

    public function sendBanForm($player): void
    {
        $list = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $list[] = $onlinePlayer->getName();
        }
        $this->target[$player->getName()] = $list;
        $menu = new CustomForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            $target = $this->target[$player->getName()][$data[0]];
            if (isset($target)) {
                if($target == $player->getName()){
                    $player->sendMessage(self::ERROR."Por que intentas hacer esto contigo?");
                    return;
                }
                $now = time();
                $day = ($data[1] * 86400);
                $hour = ($data[2] * 3600);
                if ($data[3] > 1) {
                    $min = ($data[3] * 60);
                } else {
                    $min = 60;
                }
                $banTime = $now + $day + $hour + $min;
                $banInfo = StaffMode::getInstance()->ban->prepare("INSERT OR REPLACE INTO PlayersBan (player, banTime, reason, staff) VALUES (:player, :banTime, :reason, :staff);");
                $banInfo->bindValue(":player", $target);
                $banInfo->bindValue(":banTime", $banTime);
                $banInfo->bindValue(":reason", $data[4]);
                $banInfo->bindValue(":staff", $player->getName());
                $banInfo->execute();
                Server::getInstance()->broadcastMessage("§§aSurvi§bCraft \n §r§fBanned: §a".$target."\n §fBanned By: §a".$player->getName()."\n §cReason: §a".$data[4]."\n §7Expiry in §a" . $data[1] . "§7 Days, §a". $data[2] . " §7Hours, §a". $data[3] . " §7Minutes");
                unset($this->target[$player->getName()]);
            }
        });
        $menu->setTitle("§l§aSurvi§bCraft Players");
        $menu->addDropdown("Select Player", $this->target[$player->getName()]);
        $menu->addSlider("Dias", 0, 30);
        $menu->addSlider("Horas", 0, 24);
        $menu->addSlider("Minutos", 0, 60);
        $menu->addInput("Rason del Baneo (Obligatorio)", "Ejemplo: Hacks");
        $player->sendForm($menu);
    }

    public function sendMuteForm($player): void
    {
        $list = [];
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $list[] = $onlinePlayer->getName();
        }
        $this->target[$player->getName()] = $list;
        $menu = new CustomForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            $target = $this->target[$player->getName()][$data[0]];
            if (isset($target)) {
                if($target == $player->getName()){
                    $player->sendMessage(self::ERROR."Por que intentas hacer esto contigo?");
                    return;
                }
                $now = time();
                $day = ($data[1] * 86400);
                $hour = ($data[2] * 3600);
                if ($data[3] > 1) {
                    $min = ($data[3] * 60);
                } else {
                    $min = 60;
                }
                $banTime = $now + $day + $hour + $min;
                $banInfo = StaffMode::getInstance()->mute->prepare("INSERT OR REPLACE INTO PlayersMute (player, muteTime, reason, staff) VALUES (:player, :muteTime, :reason, :staff);");
                $banInfo->bindValue(":player", $target);
                $banInfo->bindValue(":muteTime", $banTime);
                $banInfo->bindValue(":reason", $data[4]);
                $banInfo->bindValue(":staff", $player->getName());
                $banInfo->execute();
                Server::getInstance()->broadcastMessage("§l§4Arley§fMC \n §r§fMuted: §a".$target."\n §gMuted By: §a".$player->getName()."\n §fReason: §a".$data[4]."\n §cExpiry in §a" . $data[1] . "§7 Days, §a". $data[2] . " §7Hours, §a". $data[3] . " §7Minutes");
                unset($this->target[$player->getName()]);
            }
        });
        $menu->setTitle("§l§aSurvi§bCraft Players");
        $menu->addDropdown("Select Player", $this->target[$player->getName()]);
        $menu->addSlider("Dias", 0, 30);
        $menu->addSlider("Horas", 0, 24);
        $menu->addSlider("Minutos", 0, 60);
        $menu->addInput("Rason del Muteo (Obligatorio)", "Ejemplo: Spam");
        $player->sendForm($menu);
    }

    public function getPlayerFaction(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getFaction($player->getName());
        } else return "§7N/F";
    }

    public function getFactionPower(Player $player): int {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getPower(self::getPlayerFaction($player));
        } else return 0;
    }

    public function getCountry(Player $player): string
    {
        $ip = $player->getNetworkSession()->getIp();
        $http = file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip);
        $handle = json_decode($http);
        return is_null($handle->geoplugin_countryName) ? "Unknown" : $handle->geoplugin_countryName;
    }
}