<?php

namespace qxoap\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use qxoap\StaffMode;
use qxoap\StaffUtils;

class PlayerListener implements Listener
{

    public function onPlayerLogin(PlayerLoginEvent $ev)
    {
        $player = $ev->getPlayer();
        $name = $player->getName();
        $banInfo = StaffMode::getInstance()->ban->query("SELECT * FROM PlayersBan WHERE player = '$name';");
        $array = $banInfo->fetchArray(SQLITE3_ASSOC);

        if (!empty ($array)) {

            $banTime = $array['banTime'];
            $reason = $array['reason'];
            $staff = $array['staff'];
            $now = time();

            if ($banTime > $now) {

                $remainingTime = $banTime - $now;
                $day = floor($remainingTime / 86400);
                $hourSeconds = $remainingTime % 86400;
                $hour = floor($hourSeconds / 3600);
                $minuteSec = $hourSeconds % 3600;
                $minute = floor($minuteSec / 60);
                $remainingSec = $minuteSec % 60;
                $second = ceil($remainingSec);
                $player->kick("§7Has Sido Baneado De §l§aSurvi§bCraft\n§r§gRason: §a".$reason."§7 : §a".$staff."\n§cExpiracion: §a" . $day . "§7 Dias, §a" . $hour . "§7 Horas, §a" . $minute . "§7 Minutos, §a".$second."§7 Segundos");
            } else {
                StaffMode::getInstance()->ban->query("DELETE FROM PlayersBan WHERE player = '$name';");
            }
        }
    }

    public function onPlayerMove(PlayerMoveEvent $ev)
    {
        $player = $ev->getPlayer();
        $name = $player->getName();
        $banInfo = StaffMode::getInstance()->ban->query("SELECT * FROM PlayersBan WHERE player = '$name';");
        $array = $banInfo->fetchArray(SQLITE3_ASSOC);

        if (!empty ($array)) {

            $banTime = $array['banTime'];
            $reason = $array['reason'];
            $staff = $array['staff'];
            $now = time();

            if ($banTime > $now) {

                $remainingTime = $banTime - $now;
                $day = floor($remainingTime / 86400);
                $hourSeconds = $remainingTime % 86400;
                $hour = floor($hourSeconds / 3600);
                $minuteSec = $hourSeconds % 3600;
                $minute = floor($minuteSec / 60);
                $remainingSec = $minuteSec % 60;
                $second = ceil($remainingSec);
                $player->kick("§7Has Sido Baneado De §l§aSurvi§bCraft\n§r§7Rason: §a".$reason."§f : §a".$staff."\n§cExpiracion: §a" . $day . "§7 Dias, §a" . $hour . "§7 Horas, §a" . $minute . "§7 Minutos, §a".$second."§7 Segundos");
            }
        }
    }

    public function onPlayerChat(PlayerChatEvent $ev)
    {
        $message = $ev->getMessage();
        $pl = $ev->getPlayer();

        $muteplayer = $pl->getName();
        $muteInfo = StaffMode::getInstance()->mute->query("SELECT * FROM PlayersMute WHERE player = '$muteplayer';");
        $array = $muteInfo->fetchArray(SQLITE3_ASSOC);

        if (!empty ($array)) {

            $muteTime = $array['muteTime'];
            $reason = $array['reason'];
            $staff = $array['staff'];
            $now = time();

            if ($muteTime > $now) {

                $remainingTime = $muteTime - $now;
                $day = floor($remainingTime / 86400);
                $hourSeconds = $remainingTime % 86400;
                $hour = floor($hourSeconds / 3600);
                $minuteSec = $hourSeconds % 3600;
                $minute = floor($minuteSec / 60);
                $remainingSec = $minuteSec % 60;
                $second = ceil($remainingSec);
                $name = $pl->getName();
                $pl->sendMessage(StaffUtils::MUTE."Has Sido Muteado Por §a".$staff." §7Rason: §a".$reason."§7 Expiracion: §a" . $day . "§7 Dias, §a" . $hour . "§7 Horas, §a" . $minute . "§7 Minutos, §a".$second."§7 Segundos");

                $ev->cancel();

            } else {
                StaffMode::getInstance()->mute->query("DELETE FROM PlayersMute WHERE player = '$muteplayer';");
            }
        }
    }
}