<?php

namespace crates;

use crates\animation\Animation;
use crates\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class EventListener implements Listener
{
    protected int $timeout = 0;

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority HIGHEST
     */
    public function onTap(PlayerInteractEvent $event): void
    {
        /*
        if(time() < $this->timeout){
            return;
        }
        */

        if ($event->getAction() === $event::RIGHT_CLICK_BLOCK) {
            //$this->timeout = time() + 2;
            $item = $event->getItem();
            if (($nbt = $item->getNamedTag()->getString("monthlycrate", "")) === "") {
                return;
            }

            new Animation($event->getPlayer(), $event->getBlock()->getPosition(), Utils::constructBonusRewards($nbt), Utils::constructCrateRewards($nbt));
            self::pop($event->getPlayer());
            $event->cancel();
        }
    }

    public static function pop(Player $player): void
    {
        $index = $player->getInventory()->getHeldItemIndex();
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItem($index, $item->setCount($item->getCount() - 0.5));
    }
}