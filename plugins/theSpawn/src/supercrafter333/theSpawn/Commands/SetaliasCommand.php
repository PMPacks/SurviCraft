<?php

namespace supercrafter333\theSpawn\Commands;

use supercrafter333\theSpawn\Commands\theSpawnOwnedCommand;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\DoorBumpSound;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;

/**
 * Class SetaliasCommand
 * @package supercrafter333\theSpawn\Commands
 */
class SetaliasCommand extends theSpawnOwnedCommand
{

    /**
     * @var theSpawn
     */
    private theSpawn $plugin;

    /**
     * SetaliasCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->plugin = theSpawn::getInstance();
        $this->setPermission("theSpawn.setalias.cmd");
        parent::__construct("setalias", "Register a new alias!", "§4Use: §r/setalias <alias> <worldname>", ["addalias"]);
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

        if (!$this->canUse($s)) return;

        if (count($args) < 2) {
            $s->sendMessage($this->usageMessage);
            return;
        }
        if (!is_string($args[0]) || !is_string($args[1])) {
            $s->sendMessage($this->usageMessage);
            return;
        }
        if ($pl->existsLevel($args[1]) == false) {
            $s->sendMessage($prefix . MsgMgr::getMsg("world-not-found"));
            return;
        }
        if ($pl->getSpawn($pl->levelCheck($args[1])) == false) {
            $s->sendMessage($prefix . str_replace(["{world}"], [$args[1]], MsgMgr::getMsg("no-spawn-set-for-world")));
            return;
        }
        $pl->addAlias($args[0], $args[1]);
        $s->sendMessage($prefix . str_replace(["{alias}"], [$args[0]], str_replace(["{world}"], [$args[1]], MsgMgr::getMsg("alias-set"))));
        $s->getWorld()->addSound($s->getPosition(), new DoorBumpSound());
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