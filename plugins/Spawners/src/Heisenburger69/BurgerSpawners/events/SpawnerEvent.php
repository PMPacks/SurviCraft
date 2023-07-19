<?php

namespace Heisenburger69\BurgerSpawners\events;

use pocketmine\player\Player;
use Heisenburger69\BurgerSpawners\Main;
use pocketmine\event\plugin\PluginEvent;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;

class SpawnerEvent extends PluginEvent
{
    private MobSpawnerTile $spawnerTile;
    private Player $player;

    public function __construct(Player $player, MobSpawnerTile $spawnerTile)
    {
        parent::__construct(Main::getInstance());
        
        $this->player = $player;
        $this->spawnerTile = $spawnerTile;
    }

    public function getSpawnerTile(): MobSpawnerTile
    {
        return $this->spawnerTile;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }
}
