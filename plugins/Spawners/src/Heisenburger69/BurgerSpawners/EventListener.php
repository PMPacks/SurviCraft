<?php

namespace Heisenburger69\BurgerSpawners;

use pocketmine\item\Pickaxe;
use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\world\format\Chunk;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use Heisenburger69\BurgerSpawners\utils\Forms;
use Heisenburger69\BurgerSpawners\utils\Utils;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use Heisenburger69\BurgerSpawners\items\SpawnEgg;
use Heisenburger69\BurgerSpawners\utils\Mobstacker;
use Heisenburger69\BurgerSpawners\items\SpawnerBlock;
use Heisenburger69\BurgerSpawners\utils\ConfigManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;
use Heisenburger69\BurgerSpawners\entities\SpawnerEntity;
use Heisenburger69\BurgerSpawners\events\SpawnerStackEvent;

class EventListener implements Listener
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();

        if (!$entity instanceof SpawnerEntity) {
            return;
        }

        if (in_array(strtolower((string) $entity->getId()), $this->plugin->exemptedEntities)) return;

        $mobStacker = new Mobstacker($entity);
        if ($entity->getHealth() - $event->getFinalDamage() <= 0) {
            $cause = null;
            if ($event instanceof EntityDamageByEntityEvent) {
                $player = $event->getDamager();
                if ($player instanceof Player) {
                    $cause = $player;
                }
            }
            if ($mobStacker->removeStack($cause)) {
                if (!ConfigManager::getToggle("one-shot-mobs")) $entity->setHealth($entity->getMaxHealth());
                $event->cancel();
            }
        }
    }

    /**
     * @param EntitySpawnEvent $event
     */
    public function onSpawn(EntitySpawnEvent $event): void
    {
        $entity = $event->getEntity();
        $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity): void {
            if (!$entity instanceof Living) return;
            if (!in_array(str_replace(" ", "_", strtolower($entity->getName())), Utils::getEntityArrayList())) return;
            if (in_array(strtolower((string) $entity->getId()), $this->plugin->exemptedEntities)) return;
            if (in_array(Utils::getEntityNameFromID((string) $entity->getId()), $this->plugin->exemptedEntities)) return;
            if (!$entity->getPosition()->isValid()) return;
            $disabledWorlds = ConfigManager::getArray("mob-stacking-disabled-worlds");
            if (is_array($disabledWorlds)) {
                if (in_array($entity->getWorld()->getFolderName(), $disabledWorlds)) {
                    return;
                }
            }

            if (ConfigManager::getToggle("allow-mob-stacking")) {
                if ($entity instanceof SpawnerEntity) {
                    $mobStacker = new Mobstacker($entity);
                    $mobStacker->stack();
                }
            }
        }), 1);
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlaceSpawner(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        $chunk = $block->getPosition()->getWorld()->getChunk($block->getPosition()->getX() >> 4, $block->getPosition()->getZ() >> 4);
        if (!$chunk instanceof Chunk) {
            return;
        }

        $tiles = $chunk->getTiles();
        $disabledWorlds = ConfigManager::getArray("spawner-stacking-disabled-worlds");
        if (is_array($disabledWorlds)) {
            if (in_array($player->getWorld()->getFolderName(), $disabledWorlds)) {
                return;
            }
        }

        foreach ($tiles as $tile) {
            if (!$tile instanceof MobSpawnerTile) {
                return;
            }
            if (ConfigManager::getToggle("allow-spawner-stacking")) {
                if ($item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID) !== null && $item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID)->getValue() === $tile->entityId) {
                    ($spawnerEvent = new SpawnerStackEvent($player, $tile, 1))->call();
                    if ($spawnerEvent->isCancelled()) return;
                    $tile->setSpawnCount($tile->spawnCount + 1);
                    $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
                    $event->cancel();
                }
            }
        }
    }


    /**
     * @param PlayerInteractEvent $event
     * @priority MONITOR
     */
    public function onInteractSpawner(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        if ($item instanceof Pickaxe) {
            return;
        }

        $player = $event->getPlayer();
        $vec3 = $event->getBlock()->getPosition()->asVector3();
        $level = $player->getWorld();
        $tile = $level->getTile($vec3);

        if (!$tile instanceof MobSpawnerTile) {
            if ($item instanceof SpawnEgg) {
                $item->pop();
            }
            return;
        }

        if ($event->isCancelled()) {
            $message = ConfigManager::getMessage("cannot-use-spawners-here");
            if ($message === "") {
                $message = C::colorize("&4You cannot use Spawners here!");
            }
            $player->sendMessage($message);
            return;
        }

        $disabledWorlds = ConfigManager::getArray("spawner-stacking-disabled-worlds");
        if (is_array($disabledWorlds)) {
            if (in_array($level->getFolderName(), $disabledWorlds)) {
                return;
            }
        }
        if (ConfigManager::getToggle("allow-spawner-stacking")) {
            Forms::sendSpawnerForm($tile, $player);
            $event->cancel();
        }
    }

    /**
     * @param EntityExplodeEvent $event
     */
    public function onExplode(EntityExplodeEvent $event): void
    {
        $blocks = $event->getBlockList();
        foreach ($blocks as $block) {
            if ($block instanceof SpawnerBlock) {
                $block->explode();
            }
        }
    }

    /**
     * @param EntityDeathEvent $event
     */
    public function onDeath(EntityDeathEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) return;
        if (in_array(strtolower((string) $entity->getId()), $this->plugin->exemptedEntities)) {
            $key = array_search($entity->getId(), $this->plugin->exemptedEntities);
            unset($key);
        }
    }
}
