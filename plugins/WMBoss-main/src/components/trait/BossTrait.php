<?php

declare(strict_types=1);

namespace phuongaz\boss\components\trait;

use phuongaz\boss\Boss;
use phuongaz\boss\entity\BossEntity;
use phuongaz\boss\utils\Utils;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Server;

trait BossTrait{

    private static function makeEntityNBT(Vector3 $position, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag{
        return CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($position->x),
                new DoubleTag($position->y),
                new DoubleTag($position->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0),
                new DoubleTag($motion !== null ? $motion->y : 0),
                new DoubleTag($motion !== null ? $motion->z : 0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }

    public function createBossById(string $bossId) :void {
        $bossData = Boss::getInstance()->getConfig()->get($bossId);
        $bossProps = json_decode($bossData["props"], true);
        $spawn = Utils::arrayToLocation(json_decode($bossData["spawn"], true));
        $nbt = self::makeEntityNBT($spawn);
        $nbt->setString("BossId", $bossId);
        $nbt->setInt("BossLevel", $bossProps["level"] ?? 1);
        $nbt->setFloat("Damage", (float)$bossProps["damage"] ?? 1);
        $nbt->setInt("AttackRange", (int)$bossProps["attack_range"] ?? 2);
        $nbt->setInt("AttackDelay", (int)$bossProps["attack_interval"] ?? 16);
        if(isset($bossProps["respawn_cooldown"])){
            $nbt->setInt("CooldownRespawn", (int)$bossProps["respawn_cooldown"]);
        }else {
            $nbt->setInt("CooldownRespawn", 0);
        }
        $entity = new BossEntity($spawn, Utils::generateSkinFromPath($bossProps['skin']), $nbt);
        $entity->setNameTag($bossProps["name"]);
        $entity->setSpeed((float)$bossProps["speed"] ?? 1);
        $entity->setScale((float)$bossProps["scale"] ?? 1);
        $entity->setBossId($bossId);
        $entity->setMaxHealth((int)$bossProps["hp"] ?? 20);
        $entity->setHealth((int)$bossProps["hp"] ?? 20);
        $entity->setLootReward(Utils::getRewardByBoss($bossId));

        $entity->spawnToAll();
    }

    public function spawnAllBoss() :void {
        foreach (Utils::getListBoss() as $bossId => $bossData) {
            $this->createBossById((string)$bossId);
        }
    }

    public function despawnAllBoss(): void {
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world) {
            foreach($world->getEntities() as $entity) {
                if($entity instanceof BossEntity) {
                    $entity->flagForDespawn();
                }
            }
        }
    }


}