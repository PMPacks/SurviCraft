<?php

namespace ItzAngelzzYT\menu\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ItzAngelzzYT\menu\form\MenuForm;

class MenuCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if ($sender instanceof Player){
            $sender->sendForm(new MenuForm());
        } else {
            $sender->sendMessage(TextFormat::RED . "Este commando solo se puede usar en el juego!");
        }
    }
}