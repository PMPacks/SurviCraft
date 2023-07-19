<?php

namespace codes\time;

use pocketmine\utils\SingletonTrait;

class Time {
    use SingletonTrait;

    public function getTimeToFullString(int $time): string {
        return gmdate("H:i:s", $time);
    }

    public function convertHoursToSeconds(string $time): int {
        $separate = explode(":", $time);
        $h = $separate[0]; $m = $separate[1]; $s = $separate[2];
        return ((int)$h * 3600) + ((int)$m * 60) + (int)$s;
    }

    public function getTime($time): string {
        $remaining = $time - time();
        $seconds = $remaining % 60;
        $minutes = null;
        $hours = null;
        $days = null;
        if ($remaining >= 60) {
            $minutes = floor(($remaining % 3600) / 60);
            if ($remaining >= 3600) {
                $hours = floor(($remaining % 86400) / 3600);
                if ($remaining >= 3600 * 24) {
                    $days = floor($remaining / 86400);
                }
            }
        }
        return ($minutes !== null ? ($hours !== null ? ($days !== null ? "$days days " : "")."$hours hours " : "")."$minutes minutes " : "")."$seconds seconds";
    }
}