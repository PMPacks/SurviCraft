<?php
declare(strict_types=1);

namespace crates;

use crates\animation\CrateItemEntity;
use crates\animation\FloatingTextEntity;
use crates\animation\FloatingTextTwoEntity;
use crates\commands\BoxCommand;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Main extends PluginBase
{
    private static Main $instance;

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register('cmoney', new BoxCommand('cmoney', 'Give a player a cmoney!'));

        EntityFactory::getInstance()->register(CrateItemEntity::class, function (World $world, CompoundTag $nbt): CrateItemEntity {
            $itemTag = $nbt->getCompoundTag("Item");
            if ($itemTag === null) {
                throw new SavedDataLoadingException("Expected \"Item\" NBT tag not found");
            }

            $item = Item::nbtDeserialize($itemTag);
            if ($item->isNull()) {
                throw new SavedDataLoadingException("Item is invalid");
            }

            return new CrateItemEntity(EntityDataHelper::parseLocation($nbt, $world), null, $item, $nbt);
        }, ['Thrown Block', 'ThrownBlock'], EntityLegacyIds::ITEM);

        EntityFactory::getInstance()->register(FloatingTextEntity::class, function (World $world, CompoundTag $tag): FloatingTextEntity {
            return new FloatingTextEntity(EntityDataHelper::parseLocation($tag, $world), $tag);
        }, ["FloatingTextEntity"]);

        EntityFactory::getInstance()->register(FloatingTextTwoEntity::class, function (World $world, CompoundTag $tag): FloatingTextTwoEntity {
            return new FloatingTextTwoEntity(EntityDataHelper::parseLocation($tag, $world), $tag);
        }, ["FloatingTextTwoEntity"]);
    }

    public function onDisable(): void
    {
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $level) {
            if ($level !== null) {
                foreach ($level->getEntities() as $entity) {
                    if ($entity instanceof CrateItemEntity || $entity instanceof FloatingTextEntity) {
                        $entity->flagForDespawn();
                    }
                }
            }
        }
    }
}
