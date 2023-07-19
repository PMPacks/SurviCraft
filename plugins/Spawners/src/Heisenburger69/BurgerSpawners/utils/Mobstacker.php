<?php

namespace Heisenburger69\BurgerSpawners\utils;

use pocketmine\entity\Living;
use pocketmine\player\Player;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat as C;
use pocketmine\event\entity\EntityDeathEvent;
use Heisenburger69\BurgerSpawners\entities\SpawnerEntity;

/*
Modified version of MobStacker by UnknownOre
Modified to fix:
    1. Mobs instantly dying and taking no kb when in a stack
    2. Mobs not dropping experience orbs
TODO: Submit a PR to the plugin.
*/

class Mobstacker
{
    public const STACK = 'stack';

    private SpawnerEntity $entity;

    public function __construct(SpawnerEntity $entity)
    {
        $this->entity = $entity;
    }

    public function stack(): void
    {
        if ($this->isStacked()) {
            $this->updateNameTag();
            return;
        }
        $mob = $this->findNearStack();
        if ($mob == null) {
            $this->entity->namedTag->setInt(self::STACK, 1);
            $mobstack = $this;
        } else {
            $this->entity->flagForDespawn();
            $mobstack = new Mobstacker($mob);
            $count = $mob->namedTag->getInt(self::STACK);
            $mob->namedTag->setInt(self::STACK, ++$count);
        }
        $mobstack->updateNameTag();
    }

    public function isStacked(): bool
    {
        if ($this->entity->namedTag->getTag(self::STACK) !== null)
            return true;
        return false;
    }

    public function updateNameTag(): void
    {
        $nbt = $this->entity->namedTag;
        $this->entity->setNameTagVisible(true);
        $this->entity->setNameTagAlwaysVisible(false);
        if (ConfigManager::getToggle("enable-nametags")) $this->entity->setNameTag(C::colorize(str_replace(["{count}", "{name}"], [$nbt->getInt(self::STACK), $this->entity->getName()], ConfigManager::getMessage("mob-nametag"))));
        $this->entity->sendData($this->entity->getViewers());
    }

    public function findNearStack(int $range = 16): ?SpawnerEntity
    {
        $entity = $this->entity;
        if ($entity->isFlaggedForDespawn() or $entity->isClosed()) return null;
        $boundingBox = $entity->getBoundingBox()->expandedCopy($range, $range, $range);
        foreach ($entity->getWorld()->getNearbyEntities($boundingBox) as $nearbyEntity) {
            if (!$nearbyEntity instanceof SpawnerEntity)
                continue;
            if ($entity->getPosition()->distance($nearbyEntity->getPosition()) <= $range and $entity::getNetworkTypeId() === $nearbyEntity::getNetworkTypeId()) {
                $ae = new Mobstacker($nearbyEntity);
                if ($ae->isStacked() && $ae->getStackAmount() < ConfigManager::getValue("max-stack") && !$this->isStacked()) return $nearbyEntity;
            }
        }
        return null;
    }

    public function removeStack(Player $player = null): bool
    {
        $entity = $this->entity;
        $nbt = $entity->namedTag;
        if (!$this->isStacked() or ($c = $this->getStackAmount()) <= 1) {
            return false;
        }
        $nbt->setInt(self::STACK, --$c);
        $event = new EntityDeathEvent($entity, $drops = $this->getDrops($entity));
        $event->call();
        $this->updateNameTag();

        if (ConfigManager::getToggle("mobs-drop-items")) {
            foreach ($drops as $drop) {
                if ($player instanceof Player && ConfigManager::getToggle("auto-inv")) {
                    $player->getInventory()->addItem($drop);
                } else {
                    $entity->getWorld()->dropItem($entity->getPosition(), $drop);
                }
            }
        }
        if (ConfigManager::getToggle("mobs-drop-exp")) {
            $exp = $entity->getXpDropAmount();
            if ($exp > 0) {
                if ($player instanceof Player && ConfigManager::getToggle("auto-xp")) {
                    $player->getXpManager()->addXp($exp);
                } else {
                    $entity->getWorld()->dropExperience($entity->getPosition()->asVector3(), $exp);
                }
            }
        }
        return true;
    }

    public function getStackAmount(): int
    {
        return $this->entity->namedTag->getInt(self::STACK);
    }

    public function hasCustomDrops(Living $entity): bool
    {
        $mobDrops = ConfigManager::getArray("custom-mob-drops");
        return is_array($mobDrops) && isset($mobDrops[strtolower($entity->getName())]);
    }

    public function getDrops(Living $entity): array
    {
        if ($this->hasCustomDrops($entity)) {
            $mobDrops = ConfigManager::getArray("custom-mob-drops");
            if (!is_array($mobDrops)) {
                return $entity->getDrops();
            }

            $dropData = $mobDrops[strtolower($entity->getName())];
            $drops = [];
            foreach ($dropData as $dropString) {
                $drops[] = ItemFactory::getInstance()->get($dropString[0] ?? 0, $dropString[1] ?? 0)->setCount($dropString[2] ?? 1);
            }
            return $drops;
        } else {
            return $entity->getDrops();
        }
    }
}
