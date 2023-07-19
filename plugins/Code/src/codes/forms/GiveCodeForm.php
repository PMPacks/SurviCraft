<?php

namespace codes\forms;

use codes\code\CodeFactory;
use codes\libs\formapi\types\CustomForm;
use codes\sessions\SessionFactory;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GiveCodeForm extends CustomForm {

    public function __construct(Player $player) {
        $code = [];
        foreach (CodeFactory::getInstance()->getCodes() as $codes) {
            $code[] = $codes->getName();
        }
        parent::__construct(function (Player $player, ?array $data) use ($code) {
            if (!is_null($data)) {
                $target = Server::getInstance()->getPlayerExact($data[0]);
                if (is_null($target)) {
                    $player->sendMessage(TextFormat::RED . "The player is not currently active.");
                    return;
                }
                $session = SessionFactory::getInstance()->get($target->getName());
                if ($session->hasCode($code[$data[1]])) {
                    $player->sendMessage(TextFormat::RED . "This user already has this code.");
                    return;
                }
                $session->addCode($code[$data[1]]);
                $target->sendMessage(TextFormat::GREEN . "You received the code " . $code[$data[1]] . " use it.");
                $player->sendMessage(TextFormat::GREEN . "You submitted the code correctly to the player " . $target->getName() . ".");
            }
        });
        $this->setTitle("Give Code");
        $this->addInput("Write a player name", "Example: Pepito2343");
        $this->addDropdown("Select a code to give", $code);
    }
}