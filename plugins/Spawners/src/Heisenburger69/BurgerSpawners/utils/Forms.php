<?php

namespace Heisenburger69\BurgerSpawners\utils;

use pocketmine\nbt\tag\Tag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\TextFormat as C;
use Heisenburger69\BurgerSpawners\Main;
use Heisenburger69\BurgerSpawners\tiles\MobSpawnerTile;
use Heisenburger69\BurgerSpawners\events\SpawnerStackEvent;
use Heisenburger69\BurgerSpawners\events\SpawnerUnstackEvent;

class Forms
{
    public static array $usingSpawner = [];

    public static function sendSpawnerForm(MobSpawnerTile $spawner, Player $player): void
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }
            switch ($data) {
                case 0:
                    $spawner = Forms::$usingSpawner[$player->getName()];
                    if ($spawner instanceof MobSpawnerTile) {
                        $spawner->sendAddSpawnersForm($player);
                    }
                    break;
                case 1:
                    $spawner = Forms::$usingSpawner[$player->getName()];
                    if ($spawner instanceof MobSpawnerTile) {
                        $spawner->sendRemoveSpawnersForm($player);
                    }
                    break;
                case 2:
                    break;
            }
        });
        Forms::$usingSpawner[$player->getName()] = $spawner;

        $spawnerName = $spawner->getName();
        $count = $spawner->spawnCount;

        $form->setTitle(C::BOLD . C::DARK_BLUE . $spawnerName);
        $form->setContent(C::BOLD . C::AQUA . "Count: " . C::RESET . $count);
        $form->addButton(C::BOLD . C::GOLD . "Add Spawners");
        $form->addButton(C::BOLD . C::GOLD . "Remove Spawners");
        $form->addButton(C::BOLD . C::RED . "Close");
        $player->sendForm($form);
    }

    public static function sendAddSpawnerForm(Player $player, MobSpawnerTile $spawner): void
    {
        $form = new CustomForm(function (Player $player, array $response = null) {
            if ($response === null) {
                return;
            }
            if (isset($response[1])) {
                $spawner = Forms::$usingSpawner[$player->getName()];
                if ($spawner instanceof MobSpawnerTile) {

                    $entityId = $spawner->entityId;
                    $count = (int)$response[1];
                    if ($count < 0) return;

                    $item = $player->getInventory()->getItemInHand();

                    if ($item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID) !== null && $item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID)->getValue() === $entityId) {
                        $stackCount = $item->getCount();
                        $max = $stackCount;
                    } else {
                        $message = ConfigManager::getMessage("no-available-spawners");
                        $player->sendMessage(Main::PREFIX . $message);
                        return;
                    }

                    if ($count > $max) {
                        $count = $max;
                        $message = ConfigManager::getMessage("all-spawners-stacked");
                        $player->sendMessage(Main::PREFIX . $message);
                    }

                    $item = $player->getInventory()->getItemInHand();
                    if ($item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID) !== null && $item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID)->getValue() === $entityId) {
                        ($event = new SpawnerStackEvent($player, $spawner, $count))->call();
                        if ($event->isCancelled()) return;
                        $stackCount = $item->getCount();
                        $leftover = $stackCount - $count;
                        if ($leftover > 0) {
                            $item->setCount($leftover);
                            $player->getInventory()->setItemInHand($item);
                        } else {
                            $player->getInventory()->setItemInHand(VanillaBlocks::AIR()->asItem());
                        }
                        $spawner->setSpawnCount($spawner->spawnCount + $count);
                    }
                }
            }
        });
        Forms::$usingSpawner[$player->getName()] = $spawner;

        $spawnerName = $spawner->getName();
        $count = $spawner->spawnCount;
        $entityId = $spawner->entityId;

        $max = 1;

        $item = $player->getInventory()->getItemInHand();
        if ($item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID) instanceof Tag && $item->getNamedTag()->getTag(MobSpawnerTile::ENTITY_ID)->getValue() === $entityId) {
            $stackCount = $item->getCount();
            $max = $stackCount;
        }

        $form->setTitle(C::BOLD . C::DARK_BLUE . $spawnerName);
        $form->addLabel(C::BOLD . C::AQUA . "Count: " . C::RESET . $count);
        $form->addSlider(C::BOLD . C::GOLD . "Number of spawners to add" . C::YELLOW, 1, $max, 1);
        $player->sendForm($form);
    }

    public static function sendRemoveSpawnersForm(Player $player, MobSpawnerTile $spawner): void
    {
        $form = new CustomForm(function (Player $player, array $response = null) {
            if ($response === null) {
                return;
            }
            if (isset($response[1])) {
                $spawner = Forms::$usingSpawner[$player->getName()];
                if ($spawner instanceof MobSpawnerTile) {

                    $entityId = $spawner->entityId;
                    $count = (int)$response[1];
                    if ($count <= 0) return;
                    $max = $spawner->spawnCount;

                    $message = TextFormat::GREEN . "Removed spawners!";
                    if ($count > $max) {
                        $count = $max;
                        $message = ConfigManager::getMessage("all-spawners-removed");
                        if ($message === "") {
                            $message = C::colorize("&aAll available Spawners removed");
                        }
                    }
                    ($event = new SpawnerUnstackEvent($player, $spawner, $count))->call();
                    if ($event->isCancelled()) return;
                    if ($spawner->isClosed()) return;
                    $spawner->setSpawnCount($spawner->spawnCount - $count);
                    if ($spawner->spawnCount <= 0) {
                        $spawner->getPosition()->getWorld()->setBlock($spawner->getPosition(), VanillaBlocks::AIR());
                        $spawner->close();
                    }

                    $entityName = Utils::getEntityNameFromID($entityId);
                    $spawnerItem = Main::$instance->getSpawner($entityName, $count);
                    $player->getInventory()->addItem($spawnerItem);

                    $player->sendMessage(Main::PREFIX . $message);
                }
            }
        });
        Forms::$usingSpawner[$player->getName()] = $spawner;

        $spawnerName = $spawner->getName();
        $count = $spawner->spawnCount;

        $form->setTitle(C::BOLD . C::DARK_BLUE . $spawnerName);
        $form->addLabel(C::BOLD . C::AQUA . "Count: " . C::RESET . $count);
        if ($count > 64) $count = 64;
        $form->addSlider(C::BOLD . C::GOLD . "Number of spawners to remove" . C::YELLOW, 1, $count, 1);
        $player->sendForm($form);
    }
}
