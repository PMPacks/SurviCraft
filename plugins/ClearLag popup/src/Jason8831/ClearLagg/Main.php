<?php

namespace Jason8831\ClearLagg;

use Jason8831\ClearLagg\Task\ClearLaggTask;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{

    public Config $config;

    /**
     * @var Main
     */
    private static $instance;

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->getLogger()->info("§f[§l§4ClearLagg§r§f]: Activée");
        $this->saveResource("config.yml");

        $this->getServer()->getCommandMap()->registerAll("AllCommands", [
            new Commands\Forceclear(name: "forceclear", description: "permet de forcer le clearlagg", usageMessage: "dorecclear", aliases: ["fc"])
        ]);

        $this->getScheduler()->scheduleRepeatingTask(new ClearLaggTask(), 20);
    }

    public static function getInstance(): self{
        return self::$instance;
    }
}