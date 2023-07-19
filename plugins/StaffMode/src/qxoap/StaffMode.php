<?php

namespace qxoap;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use qxoap\commands\FindPlayerCommand;
use qxoap\commands\FreezeCommand;
use qxoap\commands\KickCommand;
use qxoap\commands\PlayerInfoCommand;
use qxoap\commands\StaffChatCommand;
use qxoap\commands\StaffCommand;
use qxoap\commands\UnBanCommand;
use qxoap\commands\UnMuteCommand;
use qxoap\listeners\FreezeListener;
use qxoap\listeners\ItemListener;
use qxoap\listeners\PlayerListener;
use qxoap\listeners\StaffListener;
use qxoap\task\BanTask;
use qxoap\task\FreezeTask;
use qxoap\task\VanishTask;

class StaffMode extends PluginBase{
    use SingletonTrait;

    public function onLoad(): void
    {
        self::setInstance($this);
    }

    public function onEnable(): void
    {
        $this->ban = new \SQLite3(StaffMode::getInstance()->getDataFolder() . "PlayersBan.yml");
        $this->mute = new \SQLite3(StaffMode::getInstance()->getDataFolder() . "PlayersMute.yml");
        $this->mute->exec("CREATE TABLE IF NOT EXISTS PlayersMute(player TEXT PRIMARY KEY, muteTime INT, reason TEXT, staff TEXT);");
        $this->ban->exec("CREATE TABLE IF NOT EXISTS PlayersBan(player TEXT PRIMARY KEY, banTime INT, reason TEXT, staff TEXT);");
        $command =Server::getInstance()->getCommandMap();
        $command->register("freeze", new FreezeCommand());
        $command->register("pinfo", new PlayerInfoCommand());
        $command->register("findplayer", new FindPlayerCommand());
        $command->register("sc", new StaffChatCommand());
        $command->register("staff", new StaffCommand());
        $this->unregisterCommand("kick");
        $this->getScheduler()->scheduleRepeatingTask(new FreezeTask(), 1);
        $this->getScheduler()->scheduleRepeatingTask(new VanishTask(), 1);
        $command->register("kick", new KickCommand());
        $this->unregisterCommand("unban");
        $command->register("unban", new UnBanCommand());
        $command->register("unmute", new UnMuteCommand());
        $manager = Server::getInstance()->getPluginManager();
        $manager->registerEvents(new PlayerListener(), $this);
        $manager->registerEvents(new StaffListener(), $this);
        $manager->registerEvents(new ItemListener(), $this);
        $manager->registerEvents(new FreezeListener(), $this);
    }

    private function unregisterCommand(string $commandName) : void{
        $commandMap = $this->getServer()->getCommandMap();
        if(($cmd = $commandMap->getCommand($commandName)) !== null){
            $commandMap->unregister($cmd);
        }
    }
}