<?php

declare(strict_types=1);

namespace phuongaz\boss\components\forms;

use jojoe77777\FormAPI\SimpleForm;
use phuongaz\boss\components\trait\BossTrait;
use phuongaz\boss\utils\Utils;
use pocketmine\player\Player;

class MainForm extends SimpleForm {
    use BossTrait;

    public function __construct() {
        parent::__construct($this->getCallable());
        $this->setTitle("Boss");
        $this->addButton("Create");
        $this->addButton("List");
        $this->addButton("spawn all");
        $this->addButton("despawn all");
    }

    public function getCallable(): ?callable {
        return function(Player $player, ?int $data) :void {
            if($data === null) return;
            if($data === 0){
                $player->sendForm(new Create());
            }
            if($data === 1){
                $player->sendForm(new ListBoss());
            }
            if($data === 2){
                $player->sendMessage("Spawn all");
                $this->spawnAllBoss();
            }
            if($data == 3) {
                $player->sendMessage("Despawn all");
                $this->despawnAllBoss();
            }
        };
    }

}