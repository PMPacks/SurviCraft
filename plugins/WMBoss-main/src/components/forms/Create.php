<?php

declare(strict_types=1);

namespace phuongaz\boss\components\forms;

use jojoe77777\FormAPI\CustomForm;
use phuongaz\boss\utils\Utils;
use pocketmine\player\Player;

class Create extends CustomForm {

    public function __construct() {
        parent::__construct($this->getCallable());
        $this->setTitle("Create Boss");
        $this->addInput("Please enter the id:");
        $this->addInput("Please enter the name:", "Boss", "Boss");
        $this->addInput("Please enter the skin:", "", "ID01");
        $this->addInput("Please enter the health:", "", "20");
        $this->addInput("Please enter the speed:", "", "0.6");
        $this->addInput("Please enter the damage:", "", "1");
        $this->addInput("Please enter the scale:", "", "1");
        $this->addInput("Please enter the attack range:", "", "2");
        $this->addInput("Please enter the attack interval:", "", "16");
        $this->addInput("Please enter cooldown respawn:", "", "0");
    }

    public function getCallable(): ?callable{
        return function(Player $player, ?array $data) :void {
            if(is_null($data)) return;
            $bossId = $data[0];
            $skinType = $data[2] ?? "default";
            $props = [
                'name' => $data[1],
                'skin' => $skinType,
                'hp' => $data[3],
                'speed' => $data[4],
                'damage' => $data[5],
                'scale' => $data[6],
                'attack_range' => $data[7],
                'attack_interval' => $data[8],
                'respawn_cooldown' => $data[9]
            ];
            Utils::createSimpleConfig($bossId, $player->getLocation(), $props);
            Utils::sendToast($player, "&l&aWaterMelon Boss", "&fBoss {$bossId} has been created");
        };
    }

}