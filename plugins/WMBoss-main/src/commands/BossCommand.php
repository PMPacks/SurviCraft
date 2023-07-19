<?php

declare(strict_types=1);

namespace phuongaz\boss\commands;

use phuongaz\boss\components\forms\MainForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BossCommand extends Command {

    public function __construct(){
        parent::__construct("boss", "", "");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if($sender instanceof Player && Server::getInstance()->isOp($sender->getName())){
            $sender->sendForm(new MainForm());
        }
        return false;
    }
}