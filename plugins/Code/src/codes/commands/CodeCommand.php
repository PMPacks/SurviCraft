<?php

namespace codes\commands;

use codes\code\CodeFactory;
use codes\forms\ClaimCodeForm;
use codes\forms\CreateCodeForm;
use codes\forms\GiveCodeForm;
use codes\provider\ConfigProvider;
use codes\time\Time;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CodeCommand extends Command {

    public function __construct() {
        parent::__construct("code", "Use a code you have to receive gifts.", null, ["survicode"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if ($sender instanceof Player) {
            if (empty($args[0])) {
                $sender->sendForm(new ClaimCodeForm($sender));
                return;
            }
            switch ($args[0]) {
                case "create":
                    if ($sender->hasPermission("codes.create")) {
                        $sender->sendForm(new CreateCodeForm($sender));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "You do not have permissions to create a code.");
                    }
                    break;
                case "delete":
                    if ($sender->hasPermission("codes.delete")) {
                         if (empty($args[1])) {
                             $sender->sendMessage(TextFormat::RED . "You have to enter the name of the code to remove it.");
                             return;
                         }
                         if (CodeFactory::getInstance()->existCode($args[1])) {
                             CodeFactory::getInstance()->forceRemoveCode($args[1]);
                             $sender->sendMessage(TextFormat::GREEN . "You deleted the code " . $args[1] . " correctly.");
                         } else {
                             $sender->sendMessage(str_replace(["&", "{code}"], ["§", $args[1]], ConfigProvider::getInstance()->getData("no-code-exist")));
                         }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "You do not have permissions to delete a code.");
                    }
                    break;
                case "mycodes":
                    if ($sender->hasPermission("codes.mycodes")) {
                        $sender->sendMessage("These are your created codes:");
                        foreach (CodeFactory::getInstance()->getCodes() as $codes) {
                            if ($codes->getAuthor() === $sender->getName()) {
                                $sender->sendMessage("Name: " . $codes->getName() . ", Expire in: " . Time::getInstance()->getTimeToFullString($codes->getTime()));
                            }
                        }
                    } else {
                        $sender->sendMessage(TextFormat::RED . "You do not have permissions to see your created codes.");
                    }
                    break;
                case "give":
                    if ($sender->hasPermission("codes.give")) {
                        if (empty(CodeFactory::getInstance()->getCodes())) {
                            $sender->sendMessage(TextFormat::RED . "There are no codes to give, create one.");
                            return;
                        }
                        $sender->sendForm(new GiveCodeForm($sender));
                    } else {
                        $sender->sendMessage(TextFormat::RED . "You do not have permissions to give a code to player.");
                    }
                    break;
                default:
                    $sender->sendMessage(TextFormat::RED . "Use: /code to claim a code.");
                    break;
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You can only execute this command in-game.");
        }
    }
}