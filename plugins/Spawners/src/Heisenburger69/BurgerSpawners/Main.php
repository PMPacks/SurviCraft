<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerSpawners;

use ReflectionProperty;
use ReflectionException;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use ReflectionClassConstant;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\utils\TextFormat as C;
use pocketmine\block\tile\TileFactory;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\data\bedrock\EnchantmentIdMap;
use Heisenburger69\BurgerSpawners\utils\Utils;
use Heisenburger69\BurgerSpawners\items\SpawnEgg;
use Heisenburger69\BurgerSpawners\items\SpawnerBlock;
use Heisenburger69\BurgerSpawners\utils\ConfigManager;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;
use Heisenburger69\BurgerSpawners\utils\EntityLegacyIds;
use Heisenburger69\BurgerSpawners\entities\EntityManager;
use Heisenburger69\BurgerSpawners\commands\SpawnerCommand;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use Heisenburger69\BurgerSpawners\commands\SpawnEggCommand;

class Main extends PluginBase
{
    public const PREFIX = "§d(§bSpawner§d)§e > §b";

    public static Main $instance;

    public array $exemptedEntities = [];

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("BurgerSpawners", new SpawnerCommand($this));
        $this->getServer()->getCommandMap()->register("BurgerSpawners", new SpawnEggCommand($this));

        TileFactory::getInstance()->register(MobSpawnerTile::class, ['MobSpawner', "BurgerSpawners"]);
        $oldSpawner = VanillaBlocks::MONSTER_SPAWNER();
        BlockFactory::getInstance()->register(new SpawnerBlock(new BlockIdentifier($oldSpawner->getId(), 0, ItemIds::MONSTER_SPAWNER, MobSpawnerTile::class), $oldSpawner->getName(), $oldSpawner->getBreakInfo()), true);

        EnchantmentIdMap::getInstance()->register(EnchantmentIds::LOOTING, new Enchantment('Looting', Rarity::COMMON, ItemFlags::SWORD, ItemFlags::NONE, 5));
        /** @phpstan-ignore-next-line */
        StringToEnchantmentParser::getInstance()->register('looting', fn () => EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOOTING));

        if (ConfigManager::getToggle("register-mobs")) {
            EntityManager::init();
        }

        if (is_array(ConfigManager::getArray("exempted-entities"))) {
            foreach (ConfigManager::getArray("exempted-entities") as $entityName) {
                $this->exemptEntityFromStackingByName($entityName);
            }
        }
    }

    public function getRegisteredEntities(): ?array
    {
        $reflectionProperty = new ReflectionProperty(EntityFactory::class, 'saveNames');
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue(EntityFactory::getInstance());
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function getSpawner(string $name, int $amount): Item
    {
        $name = strtolower($name);
        $name = str_replace(" ", "_", $name);
        $entityID = Utils::getEntityIDFromName($name);
        if (!is_string($entityID)) {
            return VanillaItems::AIR();
        }

        $nbt = new CompoundTag();
        $nbt->setString(MobSpawnerTile::ENTITY_ID, $entityID);

        $spawner = VanillaBlocks::MONSTER_SPAWNER()->asItem();
        $spawner->setCount($amount);
        $spawner->setNamedTag($nbt);
        $spawnerName = Utils::getEntityNameFromID($entityID) . " Spawner";
        $spawner->setCustomName(C::RESET . $spawnerName);

        return $spawner;
    }

    public function getSpawnEgg(string $name, int $amount): Item
    {
        $name = strtolower($name);
        $entityId = Utils::getEntityIDFromName($name);
        if (!is_string($entityId)) {
            return VanillaItems::AIR();
        }

        try {
            $reflectionConstant = new ReflectionClassConstant(EntityLegacyIds::class, strtoupper($name));
            $meta = $reflectionConstant->getValue();
        } catch (ReflectionException $ex) {
            return VanillaItems::AIR();
        }

        $eggName = Utils::getEntityNameFromID($entityId) . " Spawn Egg";
        $egg = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, $meta, $amount);
        if (!$egg instanceof SpawnEgg) {
            return VanillaItems::AIR();
        }
        
        $egg->setEntityId($entityId);
        $egg->setCustomName(C::RESET . $eggName);

        return $egg;
    }

    public function exemptEntityFromStackingByName(string $entityName): void
    {
        $this->exemptedEntities[] = $entityName;
    }
}
