<?php

namespace codes\forms;

use codes\code\Code;
use codes\code\CodeFactory;
use codes\libs\formapi\types\CustomForm;
use codes\provider\ConfigProvider;
use codes\sessions\SessionFactory;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\Server;

class ClaimCodeForm extends CustomForm {

    public function __construct(Player $player) {
        parent::__construct(function (Player $player, ?array $data) {
            if (!is_null($data)) {
                $code = $data[0];
                if (empty($code)) {
                    $player->sendMessage(str_replace(["&"], ["§"], ConfigProvider::getInstance()->getData("enter-a-code-to-claim")));
                    return;
                }
                $codeFactory = CodeFactory::getInstance();
                if ($codeFactory->existCode($code)) {
                    $code = $codeFactory->getCode($code);
                    if ($code->canClaim($player->getName())) {
                        foreach ($code->getRewards() as $rewards) {
                            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), str_replace(["{player}"], [$player->getName()], $rewards));
                        }
                        if (!$code->hasReclamed($player->getName())) {
                            $code->addReclamed($player->getName());
                        }
                        $session = SessionFactory::getInstance()->get($player->getName());
                        if ($session->hasCode($code->getName())) {
                            $session->removeCode($code->getName());
                        }
                        $this->extracted($player, $code);
                    } else {
                        $session = SessionFactory::getInstance()->get($player->getName());
                        if ($session->hasCode($code->getName())) {
                            foreach ($code->getRewards() as $rewards) {
                                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), new Language("eng")), str_replace(["{player}"], [$player->getName()], $rewards));
                            }
                            $code->addReclamed($player->getName());
                            $session->removeCode($code->getName());
                            $this->extracted($player, $code);
                        } else {
                            $player->sendMessage(str_replace(["&"], ["§"], ConfigProvider::getInstance()->getData("code-already-reclamed")));
                        }
                    }
                } else {
                    $player->sendMessage(str_replace(["&", "{code}"], ["§", $code], ConfigProvider::getInstance()->getData("no-code-exist")));
                }
            }
        });
        $session = SessionFactory::getInstance()->get($player->getName());
        $this->setTitle("§l§6PASE DE LIVE");
        $this->addInput("Escribe tu código para reclamarlo.", "Ejemplo: HDK293sn30");
        $this->addLabel("Tienes estos códigos disponibles: " . "(" . count($session->getCodes()) . ")" . ":");
        foreach ($session->getCodes() as $codeName) {
            if (CodeFactory::getInstance()->existCode($codeName)) {
                $code = CodeFactory::getInstance()->getCode($codeName);
                $this->addLabel("-" . " " . $code->getName());
            }
        }
    }

    public function extracted(Player $player, ?Code $code): void {
        $player->sendMessage(str_replace(["&", "{code}"], ["§", $code->getName()], ConfigProvider::getInstance()->getData("code-reclamed")));
        $author = Server::getInstance()->getPlayerExact($code->getAuthor());
        if (!is_null($author)) {
            $author->sendMessage(str_replace(["&", "{player}", "{code}"], ["§", $player->getName(), $code->getName()], ConfigProvider::getInstance()->getData("author-code-claimed")));
        }
    }
}