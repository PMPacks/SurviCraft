<?php

declare(strict_types=1);

namespace phuongaz\boss\components\forms;

use jojoe77777\FormAPI\SimpleForm;
use phuongaz\boss\components\trait\BossTrait;
use phuongaz\boss\utils\Utils;
use pocketmine\player\Player;

class ListBoss extends SimpleForm {

    use BossTrait;

    private array $bossData;

    public function __construct(){
        parent::__construct($this->getCallable());
        $this->parseListBoss();
        foreach($this->bossData as $bossId => $bossData){
            $this->addButton("ID: $bossId\nName: $bossData[1]");
        }
    }

    public function getCallable(): ?callable{
        return function(Player $player, ?int $data) :void {
            $boss = $this->bossData[$data] ?? null;
            if($boss === null) return;
            $this->createBossById((string)$boss[0]);
            Utils::sendToast($player, "&l&aWaterMelon Boss", "&fBoss $boss[0] has been spawned");
        };
    }

    public function parseListBoss() :void{
        $bossData = [];
        foreach(Utils::getListBoss() as $boss => $props) {
            $propsData = json_decode($props, true);
            $bossData[] = [$boss, $propsData['name']];
        }
        $this->bossData = $bossData;
    }

}