<?php

namespace codes;

use codes\code\CodeFactory;
use codes\commands\CodeCommand;
use codes\provider\ConfigProvider;
use codes\sessions\SessionFactory;
use JsonException;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Codes extends PluginBase {
    use SingletonTrait;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        ConfigProvider::getInstance()->start();
        CodeFactory::getInstance()->start();
        SessionFactory::getInstance()->start();
        $this->getServer()->getPluginManager()->registerEvents(new EventsListener(), $this);
        $this->getServer()->getCommandMap()->register("code", new CodeCommand());
    }

    /**
     * @throws JsonException
     */
    protected function onDisable(): void {
        CodeFactory::getInstance()->close();
        foreach (SessionFactory::getInstance()->getSessions() as $sessions) {
            $sessions->save();
        }
    }
}