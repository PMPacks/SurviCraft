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

class SpawnEggCommand extends Command implements PluginOwned
{
    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("spawnegg", "Gives a spawn egg of the desired Entity to change the Spawner entity to the newer entity");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): mixed
    {
        if (!$sender->hasPermission("burgerspawners.command.spawner")) {
            $sender->sendMessage(Main::PREFIX . C::DARK_RED . "Insufficient Permission.");
            return false;
        }
        if (count($args) < 1) {
            $sender->sendMessage(Main::PREFIX . C::RED . "/spawnegg <egg> <count> <player>");
            return false;
        }
        $entities = Utils::getEntityArrayList();

        if (isset($args[0]) && $args[0] === "list") {
            $list = implode(", ", $entities);
            $sender->sendMessage(Main::PREFIX . C::GOLD . "List of Available Eggs:\n" . C::YELLOW . $list);
            return true;
        }

        $entities = $this->plugin->getRegisteredEntities();
        $entityName = strtolower($args[0]);
        if ($entities === null) {
            $sender->sendMessage(Main::PREFIX . C::RED . "No registered entities!");
            return false;
        }

        $entities = array_change_key_case(array_flip($entities), CASE_LOWER);
        if (!array_key_exists(str_replace("_", " ", $entityName), $entities)) {
            $sender->sendMessage(Main::PREFIX . C::RED . "Given entity " . C::DARK_AQUA . $entityName . C::RED . " not registered!");
            return false;
        }

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

        $egg = Main::$instance->getSpawnEgg($entityName, $count);
        $eggName = $egg->getCustomName();

        if ($player instanceof Player) {
            $sender->sendMessage(Main::PREFIX . "Given $eggName to " . $player->getName());
            $player->getInventory()->addItem($egg);
            return true;
        } else {
            $sender->sendMessage(Main::PREFIX . C::RED . "Player not found!");
        }
        return false;
    }

    public function getOwningPlugin(): Plugin
    {
        return Main::getInstance();
    }
}
