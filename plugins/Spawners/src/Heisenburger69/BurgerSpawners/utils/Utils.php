<?php

declare(strict_types=1);

namespace Heisenburger69\BurgerSpawners\utils;

use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use Heisenburger69\BurgerSpawners\entities\Bat;
use Heisenburger69\BurgerSpawners\entities\Bee;
use Heisenburger69\BurgerSpawners\entities\Cow;
use Heisenburger69\BurgerSpawners\entities\Fox;
use Heisenburger69\BurgerSpawners\entities\Pig;
use Heisenburger69\BurgerSpawners\entities\Vex;
use Heisenburger69\BurgerSpawners\entities\Husk;
use Heisenburger69\BurgerSpawners\entities\Mule;
use Heisenburger69\BurgerSpawners\entities\Wolf;
use Heisenburger69\BurgerSpawners\entities\Blaze;
use Heisenburger69\BurgerSpawners\entities\Ghast;
use Heisenburger69\BurgerSpawners\entities\Horse;
use Heisenburger69\BurgerSpawners\entities\Llama;
use Heisenburger69\BurgerSpawners\entities\Panda;
use Heisenburger69\BurgerSpawners\entities\Sheep;
use Heisenburger69\BurgerSpawners\entities\Slime;
use Heisenburger69\BurgerSpawners\entities\Squid;
use Heisenburger69\BurgerSpawners\entities\Stray;
use Heisenburger69\BurgerSpawners\entities\Witch;
use Heisenburger69\BurgerSpawners\entities\Donkey;
use Heisenburger69\BurgerSpawners\entities\Evoker;
use Heisenburger69\BurgerSpawners\entities\Hoglin;
use Heisenburger69\BurgerSpawners\entities\Ocelot;
use Heisenburger69\BurgerSpawners\entities\Parrot;
use Heisenburger69\BurgerSpawners\entities\Piglin;
use Heisenburger69\BurgerSpawners\entities\Rabbit;
use Heisenburger69\BurgerSpawners\entities\Spider;
use Heisenburger69\BurgerSpawners\entities\Zoglin;
use Heisenburger69\BurgerSpawners\entities\Zombie;
use Heisenburger69\BurgerSpawners\utils\EntityIds;
use Heisenburger69\BurgerSpawners\entities\Chicken;
use Heisenburger69\BurgerSpawners\entities\Creeper;
use Heisenburger69\BurgerSpawners\entities\Dolphin;
use Heisenburger69\BurgerSpawners\entities\Ravager;
use Heisenburger69\BurgerSpawners\entities\Shulker;
use Heisenburger69\BurgerSpawners\entities\Enderman;
use Heisenburger69\BurgerSpawners\entities\Guardian;
use Heisenburger69\BurgerSpawners\entities\Pillager;
use Heisenburger69\BurgerSpawners\entities\Skeleton;
use Heisenburger69\BurgerSpawners\entities\Villager;
use Heisenburger69\BurgerSpawners\entities\Endermite;
use Heisenburger69\BurgerSpawners\entities\IronGolem;
use Heisenburger69\BurgerSpawners\entities\MagmaCube;
use Heisenburger69\BurgerSpawners\entities\Mooshroom;
use Heisenburger69\BurgerSpawners\entities\PolarBear;
use Heisenburger69\BurgerSpawners\entities\SnowGolem;
use Heisenburger69\BurgerSpawners\entities\CaveSpider;
use Heisenburger69\BurgerSpawners\entities\Silverfish;
use Heisenburger69\BurgerSpawners\entities\Vindicator;
use Heisenburger69\BurgerSpawners\entities\PiglinBrute;
use Heisenburger69\BurgerSpawners\entities\ZombieHorse;
use Heisenburger69\BurgerSpawners\entities\ElderGuardian;
use Heisenburger69\BurgerSpawners\entities\SkeletonHorse;
use Heisenburger69\BurgerSpawners\entities\SpawnerEntity;
use Heisenburger69\BurgerSpawners\entities\WitherSkeleton;
use Heisenburger69\BurgerSpawners\entities\ZombieVillager;
use Heisenburger69\BurgerSpawners\entities\ZombifiedPiglin;

class Utils
{
    public static function getNamesID(): array
    {
        $names = [
            EntityIds::BAT => "Bat",
            EntityIds::BEE => "Bee",
            EntityIds::BLAZE => "Blaze",
            EntityIds::CAVE_SPIDER => "Cave Spider",
            EntityIds::CHICKEN => "Chicken",
            EntityIds::COW => "Cow",
            EntityIds::CREEPER => "Creeper",
            EntityIds::DOLPHIN => "Dolphin",
            EntityIds::DONKEY => "Donkey",
            EntityIds::ELDER_GUARDIAN => "Elder Guardian",
            EntityIds::ENDERMAN => "Enderman",
            EntityIds::ENDERMITE => "Endermite",
            EntityIds::EVOKER => "Evoker",
            EntityIds::FOX => "Fox",
            EntityIds::GHAST => "Ghast",
            EntityIds::GUARDIAN => "Guardian",
            EntityIds::HOGLIN => "Hoglin",
            EntityIds::HORSE => "Horse",
            EntityIds::HUSK => "Husk",
            EntityIds::IRON_GOLEM => "Iron Golem",
            EntityIds::LLAMA => "Llama",
            EntityIds::MAGMA_CUBE => "Magma Cube",
            EntityIds::MOOSHROOM => "Mooshroom",
            EntityIds::MULE => "Mule",
            EntityIds::OCELOT => "Ocelot",
            EntityIds::PANDA => "Panda",
            EntityIds::PARROT => "Parrot",
            EntityIds::PIG => "Pig",
            EntityIds::PIGLIN => "Piglin",
            EntityIds::PIGLIN_BRUTE => "Piglin Brute",
            EntityIds::PILLAGER => "Pillager",
            EntityIds::POLAR_BEAR => "Polar Bear",
            EntityIds::RABBIT => "Rabbit",
            EntityIds::RAVAGER => "Ravager",
            EntityIds::SHEEP => "Sheep",
            EntityIds::SHULKER => "Shulker",
            EntityIds::SILVERFISH => "Silverfish",
            EntityIds::SKELETON => "Skeleton",
            EntityIds::SKELETON_HORSE => "Skeleton Horse",
            EntityIds::SLIME => "Slime",
            EntityIds::SNOW_GOLEM => "Snow Golem",
            EntityIds::SPIDER => "Spider",
            EntityIds::SQUID => "Squid",
            EntityIds::STRAY => "Stray",
            EntityIds::VEX => "Vex",
            EntityIds::VILLAGER => "Villager",
            EntityIds::VINDICATOR => "Vindicator",
            EntityIds::WITCH => "Witch",
            EntityIds::WITHER_SKELETON => "Wither Skeleton",
            EntityIds::WOLF => "Wolf",
            EntityIds::ZOGLIN => "Zoglin",
            EntityIds::ZOMBIE => "Zombie",
            EntityIds::ZOMBIE_HORSE => "Zombie Horse",
            EntityIds::ZOMBIE_PIGMAN => "Zombified Piglin",
            EntityIds::ZOMBIE_VILLAGER => "Zombie Villager"
        ];

        return $names;
    }

    public static function getEntityNameFromID(string $entityID): string
    {
        $names = [
            EntityIds::BAT => "Bat",
            EntityIds::BEE => "Bee",
            EntityIds::BLAZE => "Blaze",
            EntityIds::CAVE_SPIDER => "Cave Spider",
            EntityIds::CHICKEN => "Chicken",
            EntityIds::COW => "Cow",
            EntityIds::CREEPER => "Creeper",
            EntityIds::DOLPHIN => "Dolphin",
            EntityIds::DONKEY => "Donkey",
            EntityIds::ELDER_GUARDIAN => "Elder Guardian",
            EntityIds::ENDERMAN => "Enderman",
            EntityIds::ENDERMITE => "Endermite",
            EntityIds::EVOKER => "Evoker",
            EntityIds::FOX => "Fox",
            EntityIds::GHAST => "Ghast",
            EntityIds::GUARDIAN => "Guardian",
            EntityIds::HOGLIN => "Hoglin",
            EntityIds::HORSE => "Horse",
            EntityIds::HUSK => "Husk",
            EntityIds::IRON_GOLEM => "Iron Golem",
            EntityIds::LLAMA => "Llama",
            EntityIds::MAGMA_CUBE => "Magma Cube",
            EntityIds::MOOSHROOM => "Mooshroom",
            EntityIds::MULE => "Mule",
            EntityIds::OCELOT => "Ocelot",
            EntityIds::PANDA => "Panda",
            EntityIds::PARROT => "Parrot",
            EntityIds::PIG => "Pig",
            EntityIds::PIGLIN => "Piglin",
            EntityIds::PIGLIN_BRUTE => "Piglin Brute",
            EntityIds::PILLAGER => "Pillager",
            EntityIds::POLAR_BEAR => "Polar Bear",
            EntityIds::RABBIT => "Rabbit",
            EntityIds::RAVAGER => "Ravager",
            EntityIds::SHEEP => "Sheep",
            EntityIds::SHULKER => "Shulker",
            EntityIds::SILVERFISH => "Silverfish",
            EntityIds::SKELETON => "Skeleton",
            EntityIds::SKELETON_HORSE => "Skeleton Horse",
            EntityIds::SLIME => "Slime",
            EntityIds::SNOW_GOLEM => "Snow Golem",
            EntityIds::SPIDER => "Spider",
            EntityIds::SQUID => "Squid",
            EntityIds::STRAY => "Stray",
            EntityIds::VEX => "Vex",
            EntityIds::VILLAGER => "Villager",
            EntityIds::VINDICATOR => "Vindicator",
            EntityIds::WITCH => "Witch",
            EntityIds::WITHER_SKELETON => "Wither Skeleton",
            EntityIds::WOLF => "Wolf",
            EntityIds::ZOGLIN => "Zoglin",
            EntityIds::ZOMBIE => "Zombie",
            EntityIds::ZOMBIE_HORSE => "Zombie Horse",
            EntityIds::ZOMBIE_PIGMAN => "Zombified Piglin",
            EntityIds::ZOMBIE_VILLAGER => "Zombie Villager"
        ];

        return $names[$entityID] ?? "Monster";
    }

    public static function getEntityArrayList(): array
    {
        $names = [
            "bat",
            "bee",
            "blaze",
            "cave_spider",
            "chicken",
            "cow",
            "creeper",
            "dolphin",
            "donkey",
            "elder_guardian",
            "enderman",
            "endermite",
            "evoker",
            "fox",
            "ghast",
            "guardian",
            "hoglin",
            "horse",
            "husk",
            "iron_golem",
            "llama",
            "magma_cube",
            "mooshroom",
            "mule",
            "ocelot",
            "panda",
            "parrot",
            "pig",
            "piglin",
            "piglin_brute",
            "pillager",
            "polar_bear",
            "rabbit",
            "ravager",
            "sheep",
            "shulker",
            "silverfish",
            "skeleton",
            "skeleton_horse",
            "slime",
            "snow_golem",
            "spider",
            "squid",
            "stray",
            "vex",
            "villager",
            "vindicator",
            "witch",
            "wither_skeleton",
            "wolf",
            "zoglin",
            "zombie",
            "zombie_horse",
            "zombified_piglin",
            "zombie_villager"
        ];
        return $names;
    }

    public static function getEntityIDFromName(string $entityName): ?string
    {
        $names = [
            "bat" => EntityIds::BAT,
            "bee" => EntityIds::BEE,
            "blaze" => EntityIds::BLAZE,
            "cave_spider" => EntityIds::CAVE_SPIDER,
            "chicken" => EntityIds::CHICKEN,
            "cow" => EntityIds::COW,
            "creeper" => EntityIds::CREEPER,
            "dolphin" => EntityIds::DOLPHIN,
            "donkey" => EntityIds::DONKEY,
            "elder_guardian" => EntityIds::ELDER_GUARDIAN,
            "enderman" => EntityIds::ENDERMAN,
            "endermite" => EntityIds::ENDERMITE,
            "evoker" => EntityIds::EVOKER,
            "fox" => EntityIds::FOX,
            "ghast" => EntityIds::GHAST,
            "guardian" => EntityIds::GUARDIAN,
            "hoglin" => EntityIds::HOGLIN,
            "horse" => EntityIds::HORSE,
            "husk" => EntityIds::HUSK,
            "iron_golem" => EntityIds::IRON_GOLEM,
            "llama" => EntityIds::LLAMA,
            "magma_cube" => EntityIds::MAGMA_CUBE,
            "mooshroom" => EntityIds::MOOSHROOM,
            "mule" => EntityIds::MULE,
            "ocelot" => EntityIds::OCELOT,
            "panda" => EntityIds::PANDA,
            "parrot" => EntityIds::PARROT,
            "pig" => EntityIds::PIG,
            "piglin" => EntityIds::PIGLIN,
            "piglin_brute" => EntityIds::PIGLIN_BRUTE,
            "pillager" => EntityIds::PILLAGER,
            "polar_bear" => EntityIds::POLAR_BEAR,
            "rabbit" => EntityIds::RABBIT,
            "ravager" => EntityIds::RAVAGER,
            "sheep" => EntityIds::SHEEP,
            "shulker" => EntityIds::SHULKER,
            "silverfish" => EntityIds::SILVERFISH,
            "skeleton" => EntityIds::SKELETON,
            "skeleton_horse" => EntityIds::SKELETON_HORSE,
            "slime" => EntityIds::SLIME,
            "snow_golem" => EntityIds::SNOW_GOLEM,
            "spider" => EntityIds::SPIDER,
            "squid" => EntityIds::SQUID,
            "stray" => EntityIds::STRAY,
            "vex" => EntityIds::VEX,
            "villager" => EntityIds::VILLAGER,
            "vindicator" => EntityIds::VINDICATOR,
            "witch" => EntityIds::WITCH,
            "wither_skeleton" => EntityIds::WITHER_SKELETON,
            "wolf" => EntityIds::WOLF,
            "zoglin" => EntityIds::ZOGLIN,
            "zombie" => EntityIds::ZOMBIE,
            "zombie_horse" => EntityIds::ZOMBIE_HORSE,
            "zombified_piglin" => EntityIds::ZOMBIE_PIGMAN,
            "zombie_villager" => EntityIds::ZOMBIE_VILLAGER,
        ];

        return $names[$entityName] ?? null;
    }

    public static function getEntityFromId(string $id, Location $location): ?SpawnerEntity
    {
        $nbt = new CompoundTag();
        return match ($id) {
            EntityIds::BAT => new Bat($location, $nbt),
            EntityIds::BEE => new Bee($location, $nbt),
            EntityIds::BLAZE => new Blaze($location, $nbt),
            EntityIds::CAVE_SPIDER => new CaveSpider($location, $nbt),
            EntityIds::CHICKEN => new Chicken($location, $nbt),
            EntityIds::COW => new Cow($location, $nbt),
            EntityIds::CREEPER => new Creeper($location, $nbt),
            EntityIds::DOLPHIN => new Dolphin($location, $nbt),
            EntityIds::DONKEY => new Donkey($location, $nbt),
            EntityIds::ELDER_GUARDIAN => new ElderGuardian($location, $nbt),
            EntityIds::ENDERMAN => new Enderman($location, $nbt),
            EntityIds::ENDERMITE => new Endermite($location, $nbt),
            EntityIds::EVOKER => new Evoker($location, $nbt),
            EntityIds::FOX => new Fox($location, $nbt),
            EntityIds::GHAST => new Ghast($location, $nbt),
            EntityIds::GUARDIAN => new Guardian($location, $nbt),
            EntityIds::HOGLIN => new Hoglin($location, $nbt),
            EntityIds::HORSE => new Horse($location, $nbt),
            EntityIds::HUSK => new Husk($location, $nbt),
            EntityIds::IRON_GOLEM => new IronGolem($location, $nbt),
            EntityIds::LLAMA => new Llama($location, $nbt),
            EntityIds::MAGMA_CUBE => new MagmaCube($location, $nbt),
            EntityIds::MOOSHROOM => new Mooshroom($location, $nbt),
            EntityIds::MULE => new Mule($location, $nbt),
            EntityIds::OCELOT => new Ocelot($location, $nbt),
            EntityIds::PANDA => new Panda($location, $nbt),
            EntityIds::PARROT => new Parrot($location, $nbt),
            EntityIds::PIG => new Pig($location, $nbt),
            EntityIds::PIGLIN => new Piglin($location, $nbt),
            EntityIds::PIGLIN_BRUTE => new PiglinBrute($location, $nbt),
            EntityIds::PILLAGER => new Pillager($location, $nbt),
            EntityIds::POLAR_BEAR => new PolarBear($location, $nbt),
            EntityIds::RABBIT => new Rabbit($location, $nbt),
            EntityIds::RAVAGER => new Ravager($location, $nbt),
            EntityIds::SHEEP => new Sheep($location, $nbt),
            EntityIds::SHULKER => new Shulker($location, $nbt),
            EntityIds::SILVERFISH => new Silverfish($location, $nbt),
            EntityIds::SKELETON => new Skeleton($location, $nbt),
            EntityIds::SKELETON_HORSE => new SkeletonHorse($location, $nbt),
            EntityIds::SLIME => new Slime($location, $nbt),
            EntityIds::SNOW_GOLEM => new SnowGolem($location, $nbt),
            EntityIds::SPIDER => new Spider($location, $nbt),
            EntityIds::SQUID => new Squid($location, $nbt),
            EntityIds::STRAY => new Stray($location, $nbt),
            EntityIds::VEX => new Vex($location, $nbt),
            EntityIds::VILLAGER => new Villager($location, $nbt),
            EntityIds::VINDICATOR => new Vindicator($location, $nbt),
            EntityIds::WITCH => new Witch($location, $nbt),
            EntityIds::WITHER_SKELETON => new WitherSkeleton($location, $nbt),
            EntityIds::WOLF => new Wolf($location, $nbt),
            EntityIds::ZOGLIN => new Zoglin($location, $nbt),
            EntityIds::ZOMBIE => new Zombie($location, $nbt),
            EntityIds::ZOMBIE_HORSE => new ZombieHorse($location, $nbt),
            EntityIds::ZOMBIE_PIGMAN => new ZombifiedPiglin($location, $nbt),
            EntityIds::ZOMBIE_VILLAGER => new ZombieVillager($location, $nbt),
            default => null
        };
    }

    public static function getClassFromId(string $id): ?string
    {
        $nbt = new CompoundTag();
        return match ($id) {
            EntityIds::BAT => Bat::class,
            EntityIds::BEE => Bee::class,
            EntityIds::BLAZE => Blaze::class,
            EntityIds::CAVE_SPIDER => CaveSpider::class,
            EntityIds::CHICKEN => Chicken::class,
            EntityIds::COW => Cow::class,
            EntityIds::CREEPER => Creeper::class,
            EntityIds::DOLPHIN => Dolphin::class,
            EntityIds::DONKEY => Donkey::class,
            EntityIds::ELDER_GUARDIAN => ElderGuardian::class,
            EntityIds::ENDERMAN => Enderman::class,
            EntityIds::ENDERMITE => Endermite::class,
            EntityIds::EVOKER => Evoker::class,
            EntityIds::FOX => Fox::class,
            EntityIds::GHAST => Ghast::class,
            EntityIds::GUARDIAN => Guardian::class,
            EntityIds::HOGLIN => Hoglin::class,
            EntityIds::HORSE => Horse::class,
            EntityIds::HUSK => Husk::class,
            EntityIds::IRON_GOLEM => IronGolem::class,
            EntityIds::LLAMA => Llama::class,
            EntityIds::MAGMA_CUBE => MagmaCube::class,
            EntityIds::MOOSHROOM => Mooshroom::class,
            EntityIds::MULE => Mule::class,
            EntityIds::OCELOT => Ocelot::class,
            EntityIds::PANDA => Panda::class,
            EntityIds::PARROT => Parrot::class,
            EntityIds::PIG => Pig::class,
            EntityIds::PIGLIN => Piglin::class,
            EntityIds::PIGLIN_BRUTE => PiglinBrute::class,
            EntityIds::PILLAGER => Pillager::class,
            EntityIds::POLAR_BEAR => PolarBear::class,
            EntityIds::RABBIT => Rabbit::class,
            EntityIds::RAVAGER => Ravager::class,
            EntityIds::SHEEP => Sheep::class,
            EntityIds::SHULKER => Shulker::class,
            EntityIds::SILVERFISH => Silverfish::class,
            EntityIds::SKELETON => Skeleton::class,
            EntityIds::SKELETON_HORSE => SkeletonHorse::class,
            EntityIds::SLIME => Slime::class,
            EntityIds::SNOW_GOLEM => SnowGolem::class,
            EntityIds::SPIDER => Spider::class,
            EntityIds::SQUID => Squid::class,
            EntityIds::STRAY => Stray::class,
            EntityIds::VEX => Vex::class,
            EntityIds::VILLAGER => Villager::class,
            EntityIds::VINDICATOR => Vindicator::class,
            EntityIds::WITCH => Witch::class,
            EntityIds::WITHER_SKELETON => WitherSkeleton::class,
            EntityIds::WOLF => Wolf::class,
            EntityIds::ZOGLIN => Zoglin::class,
            EntityIds::ZOMBIE => Zombie::class,
            EntityIds::ZOMBIE_HORSE => ZombieHorse::class,
            EntityIds::ZOMBIE_PIGMAN => ZombifiedPiglin::class,
            EntityIds::ZOMBIE_VILLAGER => ZombieVillager::class,
            default => null
        };
    }
}
