<?php
declare(strict_types=1);

namespace crates\animation;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\event\entity\EntityDamageEvent;

final class FloatingTextEntity extends Entity
{
    public $canCollide = false;
    protected $gravity = 0.0;
    protected $immobile = true;
    protected $scale = 0.001;
    protected $drag = 0.0;

    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);

        $this->setNameTagAlwaysVisible();
        $this->setNameTagVisible();
        $this->setNameTag("§a§lSurvi§bCraft §eMoney Crate");
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::BAT;
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.001, 0.001, 0.001);
    }

    protected function initEntity(CompoundTag $nbt): void
    {
        $this->fireTicks = $nbt->getShort("Fire", 0);
        $this->onGround = $nbt->getByte("OnGround", 0) !== 0;
        $this->fallDistance = $nbt->getFloat("FallDistance", 0.0);
        if (($customNameTag = $nbt->getTag("CustomName")) instanceof StringTag) {
            $this->setNameTag($customNameTag->getValue());
            if (($customNameVisibleTag = $nbt->getTag("CustomNameVisible")) instanceof StringTag) {
                $this->setNameTagVisible($customNameVisibleTag->getValue() !== "");
            } else {
                $this->setNameTagVisible($nbt->getByte("CustomNameVisible", 1) !== 0);
            }
        }
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();
    }
}