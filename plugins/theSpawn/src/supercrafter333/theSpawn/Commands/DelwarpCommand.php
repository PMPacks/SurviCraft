<?php

namespace supercrafter333\theSpawn\Commands;

use pocketmine\command\Command;
use supercrafter333\theSpawn\Commands\theSpawnOwnedCommand;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\GhastShootSound;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use supercrafter333\theSpawn\Forms\WarpForms;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;

/**
 * Class DelwarpCommand
 * @package supercrafter333\theSpawn\Commands
 */
class DelwarpCommand extends theSpawnOwnedCommand
{

    /**
     * @var theSpawn
     */
    private theSpawn $plugin;

    /**
     * DelwarpCommand constructor.
     * @param string $name
     * @param string $description
     * @param string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(string $name, string $description = "", string $usageMessage = null, array $aliases = [])
    {
        $this->plugin = theSpawn::getInstance();
        $this->setPermission("theSpawn.delwarp.cmd");
        parent::__construct("delwarp", "Delete a warp!", "§4Use:§r /delwarp <warpname>", ["rmwarp", "deletewarp", "removewarp"]);
    }

    /**
     * @param CommandSender|Player $s
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $s, string $commandLabel, array $args): void
    {
        $prefix = theSpawn::$prefix;
        $pl = theSpawn::getInstance();

        if (!$this->canUse($s)) return;

        if (count($args) < 1) {
            if ($pl->useForms()) {
                if ($pl->listWarps() == null) {
                    $s->sendMessage($prefix . MsgMgr::getMsg("no-warps-set"));
                    return;
                }
                $warpForms = new WarpForms();
                $warpForms->openRmWarp($s);
            } else {
                $s->sendMessage($this->usageMessage);
            }
            return;
        }
        if (!$pl->existsWarp($args[0])) {
            $s->sendMessage($prefix . str_replace(["{warpname}"], [(string)$args[0]], MsgMgr::getMsg("warp-not-exists")));
            return;
        }
        $pl->removeWarp($args[0]);
        $s->sendMessage($prefix . str_replace(["{warpname}"], [(string)$args[0]], MsgMgr::getMsg("warp-deleted")));
        $s->getWorld()->addSound($s->getPosition(), new GhastShootSound());
    }

    public static function simpleExecute(Player $s, array $args): void
    {
        $prefix = theSpawn::$prefix;
        $pl = theSpawn::getInstance();

        if (!self::testPermissionX($s, "theSpawn.delwarp.cmd", "delwarp")) return;

        if (!$pl->existsWarp($args[0])) {
            $s->sendMessage($prefix . str_replace(["{warpname}"], [(string)$args[0]], MsgMgr::getMsg("warp-not-exists")));
            return;
        }
        $pl->removeWarp($args[0]);
        $s->sendMessage($prefix . str_replace(["{warpname}"], [(string)$args[0]], MsgMgr::getMsg("warp-deleted")));
        $s->getWorld()->addSound($s->getPosition(), new GhastShootSound());
    }

    /**
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}