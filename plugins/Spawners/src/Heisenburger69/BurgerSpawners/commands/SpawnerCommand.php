<?php

namespace Heisenburger69\BurgerSpawners\commands;

use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as C;
use Heisenburger69\BurgerSpawners\Main;
use Heisenburger69\BurgerSpawners\utils\Utils;
use Heisenburger69\BurgerSpawners\utils\ConfigManager;

class SpawnerCommand extends Command implements PluginOwned
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct("spawner", "Spawner Command of Scepter Network");
        $this->setUsage("/spawner <string:spawner> [int:count] [string:player]");
        $this->setDescription("Burger Spawners Base Command");
        $this->setPermission("burgerspawners.command.spawner");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if (!$sender->hasPermission("burgerspawners.command.spawner")) {
            $sender->sendMessage(Main::PREFIX . C::DARK_RED . "Insufficient Permission.");
            return false;
        }
        if (count($args) < 1) {
            $sender->sendMessage(Main::PREFIX . C::RED . "/spawner <spawner/list> <count> <player>");
            return false;
        }
        $entities = Utils::getEntityArrayList();

        if (isset($args[0]) && $args[0] === "list") {
            $list = implode(", ", $entities);
            $sender->sendMessage(Main::PREFIX . C::GOLD . "List of Available Spawners:\n" . C::YELLOW . $list);
            return true;
        }

        $entityName = strtolower($args[0]);

        $count = 1;
        if (isset($args[1]) && (int)$args[1] >= 1) {
            $count = (int)$args[1];
        }

        $player = $sender;
        if (isset($args[2])) {
            $player = $this->plugin->getServer()->getPlayerByPrefix($args[2]);
            if ($player === null) {
                $sender->sendMessage(Main::PREFIX . C::RED . "Player " . C::DARK_AQUA . $args[2] . C::RED . " not found!");
                return false;
            }
        }

        $spawner = Main::$instance->getSpawner($entityName, $count);
        $spawnerName = $spawner->getCustomName();

        if ($player instanceof Player) {
            $message = ConfigManager::getMessage("player-given-spawner");
            $message = str_replace("{player}", $player->getName(), $message);
            $message = str_replace("{spawner}", $spawnerName, $message);

            $sender->sendMessage(Main::PREFIX . $message);
            $player->getInventory()->addItem($spawner);
            return true;
        } else {
            $sender->sendMessage(Main::PREFIX . C::RED . "Player not found!");
        }

        return false;
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
}
