<?php

namespace crates\commands;

use crates\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\Translatable;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;

class BoxCommand extends Command
{
    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->setPermission("box.op");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender->hasPermission("box.op")) {
            $sender->sendMessage(C::DARK_RED . "No permissions!");
            return;
        }

        if (count($args) !== 3) {
            $sender->sendMessage(C::RED . "Do /box <player:giveall> <name> <amount = 1>");
            return;
        }

        if (!is_numeric($args[2]) || $args[2] <= 0) {
            $sender->sendMessage(TextFormat::RED . "You must write a valid number.");
            return;
        }

        $crate = $this->checkCrate($args[1], $args[2]);
        if ($crate === null) {
            $sender->sendMessage(TextFormat::RED . "Cannot find a crate called {$args[1]}");
            return;
        }

        $server = Server::getInstance();
        if (($player = $server->getPlayerExact($args[0])) === null) {
            if ($args[0] === "giveall") {
                foreach ($server->getOnlinePlayers() as $player) {
                    $player->getInventory()->addItem($crate);
                }
            } else {
                $sender->sendMessage(C::RED . "Player not found!");
            }
            return;
        }

        $player->getInventory()->addItem($crate);
        $sender->sendMessage(C::GREEN . "Successfully given {$player->getName()} the {$args[1]} Crate!");
    }

    private function checkCrate(string $crateName, int $amount): ?Item
    {
        $crate = Utils::constructCrateItem($crateName);
        if ($crate?->getId() !== ItemIds::AIR) {
            $crate?->setCount($amount);

            return $crate;
        }

        return null;
    }
}