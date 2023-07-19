<?php

namespace crates\animation;

use InvalidArgumentException;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddItemActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageEvent;

class CrateItemEntity extends Throwable
{
    public $canCollide = false;
    protected $immobile = true;
    protected $gravity = 0.0;
    protected Item $item;
    public int $spins = 0;

    public function __construct(Location $location, ?Entity $thrower, Item $item, ?CompoundTag $nbt = null)
    {
        if ($item->isNull()) {
            throw new InvalidArgumentException("Item is invalid");
        }

        $this->item = clone $item;
        parent::__construct($location, $thrower, $nbt);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ITEM;
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setTag("Item", $this->item->nbtSerialize());
        return $nbt;
    }

    public function getOffsetPosition(Vector3 $vector3): Vector3
    {
        return $vector3->add(0, 1 / 8, 0);
    }

    public function canCollideWith(Entity $entity): bool
    {
        return false;
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    protected function sendSpawnPacket(Player $player): void
    {
        $player->getNetworkSession()->sendDataPacket(AddItemActorPacket::create($this->getId(), $this->getId(), ItemStackWrapper::legacy(TypeConverter::getInstance()->coreItemStackToNet($this->getItem())), $this->location->asVector3(), $this->getMotion(), $this->getAllNetworkData(), false));
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();
    }

}