<?php

namespace Heisenburger69\BurgerSpawners\items;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat as C;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EnchantmentIdMap;
use Heisenburger69\BurgerSpawners\utils\Utils;
use pocketmine\block\MonsterSpawner as PMSpawner;
use Heisenburger69\BurgerSpawners\utils\ConfigManager;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;
use Heisenburger69\BurgerSpawners\events\SpawnerBreakEvent;
use Heisenburger69\BurgerSpawners\events\SpawnerPlaceEvent;

class SpawnerBlock extends PMSpawner
{
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if ($item->getId() !== ItemIds::SPAWN_EGG) {
            return false;
        }

        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        if ($tile instanceof MobSpawnerTile) {
            if ($tile->entityId == "0" && $item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID) !== null) {
                $tile->copyDataFromItem($item);
                if ($player instanceof Player) {
                    (new SpawnerPlaceEvent($player, $tile))->call();
                }
            }
        }
        return parent::onInteract($item, $face, $clickVector, $player);
    }

    public function getDrops(Item $item): array
    {
        return [];
    }

    public function getSilkTouchDrops(Item $item): array
    {
        return [];
    }

    public function onBreak(Item $item, Player $player = null): bool
    {
        $parent = parent::onBreak($item, $player);
        if ($player instanceof Player) {
            if (ConfigManager::getToggle("enable-silk-touch") && !$item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::SILK_TOUCH)) && !$player->hasPermission("burgerspawners.nosilktouch")) {
                return $parent;
            }
            if (ConfigManager::getToggle("enable-silk-touch-permission") && !$player->hasPermission("burgerspawners.silktouch")) {
                return $parent;
            }
            $tile = $this->getPosition()->getWorld()->getTile($this->getPosition()->asVector3());
            if ($tile instanceof MobSpawnerTile) {
                $nbt = new CompoundTag();
                $count = $tile->spawnCount;
                $spawner = ItemFactory::getInstance()->get(ItemIds::MOB_SPAWNER, 0, $count, $nbt);
                $spawner->setCustomName(C::RESET . Utils::getEntityNameFromID($tile->entityId) . " Spawner");
                $this->getPosition()->getWorld()->dropItem($this->getPosition()->add(0.5, 0.5, 0.5), $spawner);
    
                (new SpawnerBreakEvent($player, $tile))->call();
            }
        }
        return $parent;
    }

    public function onScheduledUpdate(): void
    {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition()->asVector3());

        if (!$tile instanceof MobSpawnerTile)
            return;

        $tile->onUpdate();

        parent::onScheduledUpdate();
    }

    public function explode(): bool
    {
        if (!ConfigManager::getToggle("enable-explosion-drop")) {
            return false;
        }
        if (mt_rand(0, 100) > (int)ConfigManager::getValue("explosion-drop-chance")) {
            return false;
        }
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition()->asVector3());
        if ($tile instanceof MobSpawnerTile) {
            $nbt = new CompoundTag();
            $nbt->setInt(MobSpawnerTile::ENTITY_ID, (int) $tile->entityId);
            $count = $tile->spawnCount;
            $spawner = ItemFactory::getInstance()->get(ItemIds::MOB_SPAWNER, 0, $count, $nbt);
            $spawner->setCustomName(C::RESET . Utils::getEntityNameFromID($tile->entityId) . " Spawner");
            $this->getPosition()->getWorld()->dropItem($this->getPosition()->add(0.5, 8, 0.5), $spawner);
            return true;
        }
        return false;
    }
}
