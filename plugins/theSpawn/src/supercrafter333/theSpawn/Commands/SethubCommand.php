<?php

namespace supercrafter333\theSpawn\Commands;

use pocketmine\command\Command;
use supercrafter333\theSpawn\Commands\theSpawnOwnedCommand;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\DoorBumpSound;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;

/**
 * Class SethubCommand
 * @package supercrafter333\theSpawn\Commands
 */
class SethubCommand extends theSpawnOwnedCommand
{

    /**
     * @var theSpawn
     */
    private theSpawn $plugin;

    /**
     * SethubCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->plugin = theSpawn::getInstance();
        $this->setPermission("theSpawn.sethub.cmd");
        parent::__construct("sethub", "Set the lobby of this server!", "§4Usage: §r/sethub [randomHubs: number|int]", ["setlobby", "setthehub", "setthelobby"]);
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

        $x = $s->getPosition()->getX();
        $y = $s->getPosition()->getY();
        $z = $s->getPosition()->getZ();
        $yaw = $s->getLocation()->getYaw();
        $pitch = $s->getLocation()->getPitch();
        $levelname = $s->getWorld()->getFolderName();
        $level = $s->getWorld();
        if ($pl->getUseHubServer() == false) {
            if ($level === null) {
                $s->sendMessage($prefix . MsgMgr::getErrorMsg());
                return;
            }
            if (count($args) >= 1 && $pl->getUseRandomHubs()) {
                if (!is_numeric($args[0])) {
                    $s->sendMessage($this->usageMessage);
                    return;
                }
                if ($pl->checkSetRandomHub($args[0])) {
                    $pl->setHub($x, $y, $z, $level, $yaw, $pitch, $args[0]);
                    $s->sendMessage($prefix . str_replace(["{world}"], [$levelname], MsgMgr::getMsg("hub-set")));
                    $s->getWorld()->addSound($s->getPosition(), new DoorBumpSound());
                } else {
                    $s->sendMessage($prefix . MsgMgr::getMsg("set-random-hub-before"));
                }
            } else {
                if (!$hub->exists("hub")) {
                    if ($level === null) {
                        $s->sendMessage($prefix . MsgMgr::getErrorMsg());
                        return;
                    }
                    $pl->setHub($x, $y, $z, $level, $yaw, $pitch);
                    $s->sendMessage($prefix . str_replace(["{world}"], [$levelname], MsgMgr::getMsg("hub-set")));
                } else {
                    $pl->setHub($x, $y, $z, $level, $yaw, $pitch);
                    $s->sendMessage($prefix . str_replace(["{world}"], [$levelname], MsgMgr::getMsg("hub-changed")));
                }
                $s->getWorld()->addSound($s->getPosition(), new DoorBumpSound());
            }
            return;
        } elseif ($pl->getUseHubServer() == true) {
            $s->sendMessage($prefix . MsgMgr::getMsg("hub-server-is-enabled"));
            return;
        } else {
            $s->sendMessage($prefix . MsgMgr::getMsg("false-config-setting"));
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