<?php

namespace crates\animation;

use crates\Main;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\Position;

class CrateRewardTask extends Task
{

    private Entity $entity;
    private array $points;
    private int $i;
    private int $startIndex;
    private array $rewards;
    private int $revolutions;
    private bool $frozen;
    private Player $player;
    private string $command;

    private ?Animation $animation;
    private array $bonusRewards;


    public function __construct(Player $player, Entity $entity, int $startIndex, array $points, array $bonusRewards, array $rewards, string $command, int $revolutions = 0, bool $frozen = false, Animation $animation = null, FloatingTextEntity $extra = null)
    {
        $this->i = $startIndex;
        $this->entity = $entity;
        $this->points = $points;
        $this->rewards = $rewards;
        $this->revolutions = $revolutions;
        $this->frozen = $frozen;
        $this->player = $player;
        $this->command = $command;
        $this->animation = $animation;
        $this->bonusRewards = $bonusRewards;
    }

    public function onRun(): void
    {
        if ($this->getHandler() === null) {
            return;
        }

        if ($this->revolutions >= 2) {
            $this->getHandler()->cancel();
            $this->rewardPlayer();
            return;
        }

        $this->i++;

        if ($this->i >= count($this->points)) {
            $this->i = 0;
            $this->revolutions++;
        }

        if (!$this->frozen) {
            $this->entity->teleport(new Vector3($this->points[$this->i]->getX(), $this->entity->getPosition()->y, $this->points[$this->i]->getZ()));
        }

        if ($this->i % 20 === 0) {
            $this->entity->close();
            $this->getHandler()->cancel();
            $this->spawnNewReward();
        }
    }

    private function rewardPlayer(): void
    {
        $this->animation->addRewarded($this->entity->getItem());
        //$this->addStrike($this->entity->getPosition());
        $this->entity->close();

        Server::getInstance()->dispatchCommand(new ConsoleCommandSender($server = Server::getInstance(), $server->getLanguage()), str_replace("{player}", '"' . $this->player->getName() . '"', $this->command));
        $this->getHandler()?->cancel();
    }

    public function addStrike(Position $p): void
    {
        $light = new AddActorPacket();
        $light->type = "minecraft:lightning_bolt";
        $light->actorUniqueId = Entity::nextRuntimeId();
        $light->actorRuntimeId = Entity::nextRuntimeId();
        $light->metadata = [];
        $light->position = $p->asVector3();
        $this->player->getNetworkSession()->sendDataPacket($light);
    }

    public function spawnNewReward(): void
    {
        /** @var Item $reward */
        $reward = $this->frozen ? $this->bonusRewards[array_rand($this->bonusRewards)] : $this->rewards[array_rand($this->rewards)];
        $command = $reward->getNamedTag()->getString("cratecommand", '');

        $nbt = new CompoundTag();
        $itemTag = $reward->nbtSerialize();
        $nbt->setTag('Item', $itemTag);
        $position = Location::fromObject(new Vector3($this->entity->getLocation()->x, $this->entity->getLocation()->y, $this->entity->getLocation()->z), $this->entity->getWorld());
        $entity = new CrateItemEntity($position, $this->player, $reward);

        $entity->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::NO_AI, false);
        $entity->setNameTag($reward->getCustomName());
        $entity->setNameTagAlwaysVisible(true);
        $entity->spawnToAll();
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CrateRewardTask($this->player, $entity, $this->i, $this->points, $this->bonusRewards, $this->rewards, $command, $this->revolutions, $this->frozen, $this->animation), 1);
    }
}