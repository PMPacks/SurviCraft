<?php

namespace GreekHCF;

use BlockHorizons\Fireworks\entity\FireworksRocket;
use BlockHorizons\Fireworks\item\Fireworks;
use greek\items\custom\CustomItem;
use GreekHCF\modules\PartnerPackage;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TE;

/**
 * Class GreekListener
 * @package GreekHCF
 */
class GreekListener implements Listener
{

    public function pop(Player $p)
    {
        $item = $p->getInventory()->getItemInHand();
        if ($item->getCount() > 1) {
            $item->setCount($item->getCount() - 1);
            $p->getInventory()->setItemInHand($item);
        } else $p->getInventory()->setItemInHand(ItemFactory::getInstance()->get(ItemIds::AIR));
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function PlayerInteractEvent(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item->getId() === 130) {
            if ($item->getCustomName() === TE::BOLD . TE::LIGHT_PURPLE . "Partner Packages") {
                $event->cancel();
                if (count(PartnerPackage::getItems()) == 0)
                    return;

                if (!$player->getInventory()->canAddItem(PartnerPackage::getRandomItem())) {
                    $player->sendMessage(TE::RED . "Your inventory is full!");
                    return;
                }
                $item = PartnerPackage::getRandomItem();
                $player->sendMessage(TE::GOLD . "You recived an item" . TE::GRAY . ": " . $item->getCustomName());
                $player->getInventory()->addItem($item);
                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));

            }
        }
    }
}