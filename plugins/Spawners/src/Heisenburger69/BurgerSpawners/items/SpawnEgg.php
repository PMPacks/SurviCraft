<?php

namespace Heisenburger69\BurgerSpawners\items;

use pocketmine\block\Block;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\entity\Location;
use pocketmine\item\ItemUseResult;
use pocketmine\item\SpawnEgg as PMSpawnEgg;
use Heisenburger69\BurgerSpawners\utils\Utils;
use Heisenburger69\BurgerSpawners\entities\Pig;
use Heisenburger69\BurgerSpawners\utils\EntityIds;

class SpawnEgg extends PMSpawnEgg
{
    private string $entityId = EntityIds::PIG;

    protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity
    {
        return Utils::getEntityFromId($this->entityId, Location::fromObject($pos, $world, $yaw, $pitch)) ?? new Pig(Location::fromObject($pos, $world, $yaw, $pitch));
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        if ($blockClicked instanceof SpawnerBlock) {
            return ItemUseResult::FAIL();
        }
        $entity = $this->createEntity($player->getWorld(), $blockReplace->getPosition()->add(0.5, 0, 0.5), lcg_value() * 360, 0);

        if (!$player->isCreative()) {
            $this->count--;
        }
        $entity->spawnToAll();
        //TODO: what if the entity was marked for deletion?
        return ItemUseResult::SUCCESS();
    }

    public function setEntityId(string $entityId): void
    {
        $this->entityId = $entityId;
    }
}
