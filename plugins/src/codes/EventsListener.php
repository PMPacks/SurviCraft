<?php

namespace codes;

use codes\code\CodeFactory;
use codes\sessions\SessionFactory;
use codes\time\Time;
use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventsListener implements Listener {

    public function onLogin(PlayerLoginEvent $event): void {
        $player = $event->getPlayer();
        if (SessionFactory::getInstance()->existFile($player->getName())) {
            SessionFactory::getInstance()->load($player->getName());
        }
    }

    /**
     * @throws JsonException
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if (!SessionFactory::getInstance()->existFile($player->getName())) {
            SessionFactory::getInstance()->create($player->getName());
        }
        $session = SessionFactory::getInstance()->get($player->getName());
        if ($session->hasCodes()) {
            foreach ($session->getCodes() as $codes) {
                if (!CodeFactory::getInstance()->existCode($codes)) {
                    $session->removeCode($codes);
                }
            }
            $player->sendMessage("Remember that you still have these codes to claim:");
            foreach ($session->getCodes() as $codes) {
                $code = CodeFactory::getInstance()->getCode($codes);
                $player->sendMessage("- " . $code->getName() . " | Expires in: " . Time::getInstance()->getTimeToFullString($code->getTime()));
            }
            $player->sendMessage("Usage /code to claim it.");
        }
    }

    /**
     * @throws JsonException
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        if (SessionFactory::getInstance()->exist($player->getName())) {
            $session = SessionFactory::getInstance()->get($player->getName());
            $session->save();
            SessionFactory::getInstance()->remove($session->getName());
        }
    }
}