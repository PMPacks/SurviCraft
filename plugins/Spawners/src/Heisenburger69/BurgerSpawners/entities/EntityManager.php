<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerSpawners\entities;

use Exception;
use ReflectionException;
use pocketmine\world\World;
use pocketmine\item\ItemIds;
use ReflectionClassConstant;
use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;
use pocketmine\item\ItemIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityFactory;
use Heisenburger69\BurgerSpawners\Main;
use pocketmine\entity\EntityDataHelper;
use pocketmine\data\bedrock\EntityLegacyIds;
use Heisenburger69\BurgerSpawners\utils\Utils;
use Heisenburger69\BurgerSpawners\items\SpawnEgg;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EntityManager
{
    public static function init(): void
    {
        self::registerEntity(Bat::class, ['Bat', EntityIds::BAT]);
        foreach (Utils::getNamesID() as $entityId => $entityName) {
            $class = Utils::getClassFromId($entityId);
            if ($class === null) {
                Main::getInstance()->getLogger()->warning("Unable to register $entityName entity");
                continue;
            }

            self::registerEntity($class, [$entityName, $entityId]);
            self::registerSpawnEgg($entityId, $entityName);
        }
    }

    private static function registerEntity(string $className, array $saveNames): void
    {
        /** @phpstan-ignore-next-line */
        EntityFactory::getInstance()->register($className, function (World $world, CompoundTag $nbt) use ($className): Entity {
            $entity = new $className(EntityDataHelper::parseLocation($nbt, $world), $nbt);;
            if (!$entity instanceof Entity) {
                throw new Exception("$className is not an instance of Entity");
            }
            return $entity;
        }, $saveNames);
        Main::getInstance()->getLogger()->debug("Registered $className entity");
    }

    public static function registerSpawnEgg(string $entityId, string $entityName): bool
    {
        $name = str_replace(" ", "_", $entityName);

        try {
            $reflectionConstant = new ReflectionClassConstant(EntityLegacyIds::class, strtoupper($name));
            $meta = $reflectionConstant->getValue();
        } catch (ReflectionException $ex) {
            return false;
        }

        $eggName = Utils::getEntityNameFromID($entityId) . " Spawn Egg";
        $egg = new SpawnEgg(new ItemIdentifier(ItemIds::SPAWN_EGG, $meta), $eggName);
        
        $egg->setEntityId($entityId);
        $egg->setCustomName(TextFormat::RESET . $eggName);

        ItemFactory::getInstance()->register($egg, true);
        return true;
    }
}
