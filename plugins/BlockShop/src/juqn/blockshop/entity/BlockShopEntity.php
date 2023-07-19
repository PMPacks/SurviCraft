<?php

declare(strict_types=1);

namespace juqn\blockshop\entity;

use cqrl\player\Player;
use cqrl\Loader;
use juqn\blockshop\utils\Utils;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class BlockShopEntity extends Human
{

    /**
     * @param CompoundTag $nbt
     */
    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setImmobile(true);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag(TextFormat::colorize('&l&a  × NEW ×&r' . PHP_EOL . '&6Block Shop' . PHP_EOL . '&r&o&7/blockshop'));
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('remove.npc.blockshop') && $damager->getInventory()->getItemInHand()->getId() === 7) {
                    $this->kill();
                    return;
                }
                Utils::openBlockShop($damager);
            }
        }
    }
}
