<?php

namespace supercrafter333\theSpawn\Commands;

use pocketmine\command\Command;
use supercrafter333\theSpawn\Commands\theSpawnOwnedCommand;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\GhastShootSound;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;

/**
 * Class DelspawnCommand
 * @package supercrafter333\theSpawn\Commands
 */
class DelspawnCommand extends theSpawnOwnedCommand
{

    /**
     * @var theSpawn
     */
    private theSpawn $plugin;

    /**
     * DelspawnCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->plugin = theSpawn::getInstance();
        $this->setPermission("theSpawn.delspawn.cmd");
        parent::__construct("delspawn", "Delete to the spawn of this world!", $usageMessage, ["rmspawn", "deletespawn", "delthespawn"]);
    }

    /**
     * @param CommandSender|Player $s
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $prefix = theSpawn::$prefix;
        $pl = theSpawn::getInstance();
        $spawn = new Config($pl->getDataFolder() . "theSpawns.yml", Config::YAML);
        $hub = new Config($pl->getDataFolder() . "theHub.yml", Config::YAML);
        $msgs = MsgMgr::getMsgs();
        $config = $pl->getConfig();
        #########################

        if (!$this->canUse($s)) return;

        $levelname = $s->getWorld()->getFolderName();
        $level = $pl->getServer()->getWorldManager()->getWorldByName($levelname);
        if ($spawn->exists($levelname)) {
            $pl->removeSpawn($level);
            $s->sendMessage($prefix . MsgMgr::getMsg("spawn-removed"));
            $s->getWorld()->addSound($s->getPosition(), new GhastShootSound());
        } else {
            $s->sendMessage($prefix . MsgMgr::getMsg("no-spawn-set-in-this-world"));
        }
        return;
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}