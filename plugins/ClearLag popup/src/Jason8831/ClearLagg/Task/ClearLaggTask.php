<?php

namespace Jason8831\ClearLagg\Task;

use Jason8831\ClearLagg\Main;
use pocketmine\entity\object\ItemEntity;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

class ClearLaggTask extends Task
{

    public int $timeToClear = 60 * 2;
    public int $count = 0;


public function onRun(): void
{

    $config = new Config(Main::getInstance()->getDataFolder()."config.yml", Config::YAML);

    --$this->timeToClear;
    if($this->timeToClear <= 0) {
        foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world){
            foreach ($world->getEntities() as $entity){
                if($entity instanceof ItemEntity){
                    $entity->flagForDespawn();
                    $this->count++;
                }
            }
        }
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $sound = PlaySoundPacket::create("random.levelup", $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), 0.5, 1);
            $player->getNetworkSession()->sendDataPacket($sound);
        }
        $message = str_replace("{count}" , $this->count , $config->get("chat"));
        Server::getInstance()->broadcastMessage($message);
        $this->timeToClear = 60 * 2;
        $this->count = 0;
    }elseif (in_array($this->timeToClear, ([1,2,3,4,5,10,15,30,60]))){
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $sound = PlaySoundPacket::create("random.orb", $player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), 0.5, 1);
            $player->getNetworkSession()->sendDataPacket($sound);
            $message = str_replace("{time}", $this->timeToClear , $config->get("popup"));
            $player->sendPopup($message);
        }
    }
}
}