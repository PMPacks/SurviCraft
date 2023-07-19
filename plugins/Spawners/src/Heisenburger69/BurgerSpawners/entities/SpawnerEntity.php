<?php

namespace Heisenburger69\BurgerSpawners\entities;

use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class SpawnerEntity extends Living
{
    public CompoundTag $namedTag;

    public int $stack = 0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        if ($nbt === null) {
            $nbt = new CompoundTag();
        }
        $this->namedTag = $nbt;
        parent::__construct($location, $nbt);
    }

    protected function onDeath(): void
    {
        $ev = new EntityDeathEvent($this, $this->getDrops(), $this->getXpDropAmount());
        $ev->call();
        foreach ($ev->getDrops() as $item) {
            $this->getWorld()->dropItem($this->location, $item);
        }

        //TODO: check death conditions (must have been damaged by player < 5 seconds from death)
        if ($this->lastDamageCause instanceof EntityDamageByEntityEvent) {
            $damager = $this->lastDamageCause->getDamager();
            if ($damager instanceof Player) {
                $damager->getXpManager()->addXp($ev->getXpDropAmount());
            }
        } else {
            $this->getWorld()->dropExperience($this->location, $ev->getXpDropAmount());
        }

        $this->startDeathAnimation();
    }

    public function getNamedTag(): CompoundTag
    {
        return $this->namedTag;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.8, 1);
    }

    public static function getNetworkTypeId(): string
    {
        return "burgerspawners:default";
    }

    public function getName(): string
    {
        return "BurgerMob";
    }

    public function canBeMovedByCurrents(): bool
    {
        return true;
    }
}
