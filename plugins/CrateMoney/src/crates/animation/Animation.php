<?php

namespace crates\animation;

use Couchbase\InvalidStateException;
use crates\Main;
use crates\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\world\Position;

class Animation
{
    public Vector3 $center;
    private Player $player;
    private array $bonusRewards;
    private array $rewards;
    private array $rewarded = [];
    private $extra;
    private $extra2;

    public function __construct(Player $player, Position $center, array $bonusRewards, array $rewards)
    {
        $this->player = $player;
        $this->center = $center->add(0, 3, 0);
        $this->bonusRewards = $bonusRewards;
        $this->rewards = $rewards;
        $this->init();
    }

    private function init(): void
    {
        $points = Utils::getPointsOfRotation($this->center->x, $this->center->z, 3, 180);

        /**
         * @var      $index
         * @var Item $reward
         */
        foreach ($this->rewards as $index => $reward) {
            if ($index > 8) {
                break;
            }

            $startIndex = $points[$index * 20];
            $nbt = new CompoundTag();
            $itemTag = $reward->nbtSerialize();
            $position = Location::fromObject(new Vector3($startIndex->getX(), $this->center->getY(), $startIndex->getZ()), $this->player->getWorld());
            $nbt->setTag('Item', $itemTag);
            $entity = new CrateItemEntity($position, $this->player, $reward);
            $entity->getWorld()->addParticle($position, new CriticalParticle());
            $entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::NO_AI, false);
            $entity->setNameTag($reward->getCustomName());
            $entity->setNameTagAlwaysVisible(true);
            $entity->spawnToAll();
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateRewardTask($this->player, $entity, $index * 20, $points, $this->bonusRewards, $this->rewards, $reward->getNamedTag()->getString("cratecommand"), 0, false, $this), 1);
        }
        $this->spawnBonus($points);
    }

    public function spawnBonus(array $points): void
    {
        /** @var Item $bonusReward */
        $bonusReward = $this->bonusRewards[0];
        $nbt = new CompoundTag();
        $itemTag = $bonusReward->nbtSerialize();
        $nbt->setTag('Item', $itemTag);
        $position1 = Location::fromObject($this->center, $this->getPlayer()->getWorld());
        $position2 = Location::fromObject($this->center->add(0, 3, 0), $this->getPlayer()->getWorld());
        $position3 = Location::fromObject($this->center->add(0, 2.25, 0), $this->getPlayer()->getWorld());

        $entity = new CrateItemEntity($position1, $this->player, $bonusReward);
        $this->extra = new FloatingTextEntity($position2, new CompoundTag());
        $this->extra2 = new FloatingTextTwoEntity($position3, new CompoundTag());
        $entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::NO_AI, false);
        $entity->setNameTag($bonusReward->getCustomName());
        $entity->setNameTagAlwaysVisible(true);
        $entity->spawnToAll();
        $this->extra->spawnToAll();
        $this->extra2->spawnToAll();
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateRewardTask($this->player, $entity, 0, $points, $this->bonusRewards, $this->rewards, $bonusReward->getNamedTag()->getString("cratecommand", ""), 0, true, $this), 1);
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getRewarded(): array
    {
        return $this->rewarded;
    }

    public function addRewarded(Item $item): void
    {
        $this->rewarded[] = $item;

        if (count($this->rewarded) >= 10) {
            $msg = TextFormat::RESET . TextFormat::BLUE . $this->player->getName() . TextFormat::WHITE . " has won: \n";
            $i = 0;

            foreach ($this->rewarded as $reward) {
                $i++;
                $name = $reward->hasCustomName() ? $reward->getCustomName() : $reward->getName();
                $msg .= TextFormat::RESET . TextFormat::WHITE . "" . $name . "\n";
            }

            Server::getInstance()->broadcastMessage($msg);
            try {
                $this->extra->flagForDespawn();
                $this->extra2->flagForDespawn();
            } catch (InvalidStateException $exception) {
                Main::getInstance()->getLogger()->emergency($exception->getMessage());
            }
        }
    }

}