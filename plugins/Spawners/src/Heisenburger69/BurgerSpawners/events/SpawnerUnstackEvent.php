<?php

namespace Heisenburger69\BurgerSpawners\events;

use pocketmine\player\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;

class SpawnerUnstackEvent extends SpawnerEvent implements Cancellable
{
    use CancellableTrait;

    public int $count;

    public function __construct(Player $player, MobSpawnerTile $spawnerTile, int $count)
    {
        $this->count = $count;
        parent::__construct($player, $spawnerTile);
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
