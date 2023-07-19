<?php

namespace supercrafter333\theSpawn\Commands;

use pocketmine\command\Command;
use supercrafter333\theSpawn\Commands\theSpawnOwnedCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\Others\TpaInfo;
use supercrafter333\theSpawn\theSpawn;

/**
 *
 */
class TpacceptCommand extends theSpawnOwnedCommand
{

    /**
     * @var theSpawn
     */
    private theSpawn $pl;

    /**
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "Accept a tpa.", string $usageMessage = "§4Usage: §r/tpaccept <player>", array $aliases = [])
    {
        $this->pl = theSpawn::getInstance();
        $this->setPermission("theSpawn.tpaccept.cmd");
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender|Player $s
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $pl = $this->pl;

        if (!$this->canUse($s)) return;

        if (count($args) < 1) {
            $s->sendMessage($this->usageMessage);
            return;
        }
        if (!$pl->hasTpaOf($s->getName(), $args[0])) {
            $s->sendMessage(str_replace("{target}", (string)$args[0], MsgMgr::getMsg("no-pending-tpa")));
            return;
        }
        $tpaInfo = new TpaInfo($args[0]);
        if (!$tpaInfo->getTargetAsPlayer() instanceof Player) {
            $s->sendMessage(str_replace("{target}", (string)$args[0], theSpawn::$prefix . MsgMgr::getMsg("player-not-found")));
            return;
        }
        $target = $tpaInfo->getTargetAsPlayer();
        $name = $target->getName();
        $tpaInfo->complete();
        $s->sendMessage(str_replace("{target}", $name, theSpawn::$prefix . MsgMgr::getMsg("tpa-accepted-source")));
        $target->sendMessage(str_replace("{source}", $s->getName(), theSpawn::$prefix . MsgMgr::getMsg("tpa-accepted-target")));
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->pl;
    }
}