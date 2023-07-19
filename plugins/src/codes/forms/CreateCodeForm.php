<?php

namespace codes\forms;

use codes\code\CodeFactory;
use codes\libs\formapi\types\CustomForm;
use codes\time\Time;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CreateCodeForm extends CustomForm {

    private array $numbers = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60];

    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?array $data) {
           if (!is_null($data)) {
               if (empty($data[0])) {
                   $random = $data[1];
                   if ($random === true) {
                       $name = bin2hex(random_bytes(mt_rand(3, 5)));
                   } else {
                       $player->sendMessage(TextFormat::RED . "You didn't enter any code name and you didn't choose to have it be automated either.");
                       return;
                   }
               } else {
                   $name = $data[0];
               }
               if (CodeFactory::getInstance()->existCode($name)) {
                   $player->sendMessage(TextFormat::RED . "That code already exists.");
                   return;
               }
               if (empty($data[2])) {
                   $player->sendMessage(TextFormat::RED . "You didn't put any time format.");
                   return;
               }
               $format = explode(":", $data[2]);
               if (!in_array($format[0], $this->getNumbers()) or !in_array($format[1], $this->getNumbers()) or !in_array($format[2], $this->getNumbers())) {
                   $player->sendMessage(TextFormat::RED . "The received time format is not supported.");
                   return;
               }
               $commands = explode(",", $data[3]);
               if (empty($data[3])) {
                   $player->sendMessage(TextFormat::RED . "Hey, hey, hey, why are you trying to create a code without a reward? Don't be a bastard.");
                   return;
               }
               CodeFactory::getInstance()->addCode($player->getName(), $name, Time::getInstance()->convertHoursToSeconds($data[2]), $commands);
               $player->sendMessage(TextFormat::GREEN . "You created the code " . $name . " correctly, share it with someone to claim it.");
               $player->sendMessage(TextFormat::GREEN . "Name: " . $name . ", Expire in: " . Time::getInstance()->getTimeToFullString(CodeFactory::getInstance()->getCode($name)->getTime()));
           }
        });
        $this->setTitle("Create Code");
        $this->addInput("Enter a code name", "Example: GiftServer");
        $this->addToggle("Random code name?", false);
        $this->addInput("Write a time of code. (h:m:s)", "Example: 0:10:0 (10 Minutes)");
        $this->addInput("Commands to receive (Separate them with a ,)", "Example: give {player} apple,give {player} diamond");
    }

    public function getNumbers(): array {
        return $this->numbers;
    }
}