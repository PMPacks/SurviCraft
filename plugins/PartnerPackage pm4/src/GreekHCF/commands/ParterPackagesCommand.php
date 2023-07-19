<?php

namespace GreekHCF\commands;

use GreekHCF\backup\ItemsBackup;
use GreekHCF\Greek;
use GreekHCF\GreekUtils;

use GreekHCF\modules\PartnerPackage;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TE;
use pocketmine\player\Player;

class ParterPackagesCommand extends Command
{

    /**
     * ParterPackagesCommand constructor.
     */
    public function __construct()
    {
        parent::__construct("pp");
        $this->setPermission("pp.use");

        $this->setAliases(["partnerpackage"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     */
    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (count($args) === 0) {
            $sender->sendMessage(
                TE::GRAY . ("----------------------------------------------------------\n") .
                TE::colorize("\n") .
                TE::DARK_RED . TE:: BOLD . ("LegacyDevs Partner Packages\n") . TE::RESET .
                TE::DARK_RED . ("Version: ") . TE::WHITE . ("1.0.0\n") .
                TE::DARK_RED . ("Author: ") . TE::WHITE . ("LegacyDevs\n") .
                TE::colorize("\n") .
                TE::WHITE . "/pp -" . TE::DARK_RED . ("use this command to get plugin information\n") .
                TE::colorize("\n") .
                TE::GRAY . ("----------------------------------------------------------\n")
            );
            return;
        }
        switch ($args[0]) {
            case "edit":
                if (!$sender->hasPermission("pp.use")) {
                    $sender->sendMessage(TE::RED . "You don't have permissions");
                    return;
                }
                if (!$sender instanceof Player) {
                    $sender->sendMessage(TE::RED . "This message can only be executed in game!");
                    return;
                }
                $player = Greek::getInstance()->getServer()->getPlayerExact($sender->getName());
                Greek::$items = new PartnerPackage($player->getInventory()->getContents());
                $sender->sendMessage(TE::GREEN . "The content has been edited correctly");
                break;
            case "give":
                if (!$sender->hasPermission("pp.use")) {
                    $sender->sendMessage(TE::RED . "You don't have permissions");
                    return;
                }
                if (empty($args[1])) {
                    $sender->sendMessage(TE::RED . "Usage: /{$label} give <player|all> <amount>");
                    return;
                }
                if (empty($args[2])) {
                    $sender->sendMessage(TE::RED . "Usage: /{$label} give <player|all> <amount>");
                    return;
                }
                $player = Greek::getInstance()->getServer()->getPlayerExact($args[1]);
                if ($player !== null) {
                    GreekUtils::addPartner($player, $args[2]);
                    return;
                }
                foreach (Greek::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    GreekUtils::addPartner($player, $args[2]);
                }
                break;
        }
    }
}