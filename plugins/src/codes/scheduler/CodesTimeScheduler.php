<?php

namespace codes\scheduler;

use codes\code\Code;
use codes\code\CodeFactory;
use codes\provider\ConfigProvider;
use codes\sessions\SessionFactory;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class CodesTimeScheduler extends Task {

    private Code $code;

    public function __construct(Code $code) {
        $this->code = $code;
    }

    public function getCode(): Code {
        return $this->code;
    }

    public function onRun(): void {
        $code = $this->getCode();
        if ($code->getTime() === 0) {
            $author = Server::getInstance()->getPlayerExact($code->getAuthor());
            if (!is_null($author)) {
                $author->sendMessage(str_replace(["&", "{code}"], ["§", $code->getName()], ConfigProvider::getInstance()->getData("code-expired")));
            }
            foreach (SessionFactory::getInstance()->getSessions() as $session) {
                if ($session->hasCode($code->getName())) {
                    $session->removeCode($code->getName());
                }
            }
            if (CodeFactory::getInstance()->existCode($code->getName())) {
                CodeFactory::getInstance()->removeCode($code->getName());
            }
            $this->getHandler()->cancel();
        } else {
            $code->setTime($code->getTime() - 1);
        }
    }
}