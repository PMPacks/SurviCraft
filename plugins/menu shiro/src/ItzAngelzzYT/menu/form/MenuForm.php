<?php

namespace ItzAngelzzYT\menu\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ItzAngelzzYT\menu\utils\Settings;

class MenuForm extends SimpleForm{

    public function __construct(){
        parent::__construct(null);
        $this->setTitle(TextFormat::colorize(Settings::getInstance()->getForm()['title']));
        foreach (Settings::getInstance()->getButtons() as $name => $button) {
            $this->addButton(TextFormat::colorize($button['title']), self::getImageType($button['image']['type'] ?? null), $button['image']['path'], $name);
        }
        $this->setCallable(function (Player $player, $data = null){
            if (!is_string($data)) return;
            if (Settings::getInstance()->getCommandByButton($data) === null) return;
            $command = str_replace("/", "", Settings::getInstance()->getCommandByButton($data));
            Server::getInstance()->getCommandMap()->dispatch($player, $command);
        });
    }

    private static function getImageType(?string $type): int{
        return match ($type){
          "url" => 1,
          "path" => 0,
          default => -1
        };
    }
}