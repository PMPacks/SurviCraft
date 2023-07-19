<?php

declare(strict_types=1);

namespace phuongaz\boss\entity;

use phuongaz\boss\Boss;
use phuongaz\boss\components\trait\BossTrait;
use phuongaz\boss\components\Reward;
use phuongaz\boss\utils\Utils;
use pocketmine\block\Air;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\utils\SlabType;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class BossEntity extends Human {

    private string $bossId;
    private int $bossLevel;
    private float $damage;
    private int $attackRange;
    private int $attackDelay;
    private Vector3 $spawnPos;
    private ?Entity $target = null;
    private array $damgers = [];
    private ?Reward $reward = null;
    private float $speed = 1;
    protected int $jumpTicks = 0;
    private int $cooldown_respawn = 0;

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt){
        parent::__construct($location, $skin, $nbt);
        $this->spawnPos = $location;
    }

    public function initEntity(CompoundTag $nbt): void{
        $this->bossId = $nbt->getString("BossId");
        $this->bossLevel = $nbt->getInt("BossLevel");
        $this->damage = $nbt->getFloat("Damage");
        $this->attackRange = $nbt->getInt("AttackRange");
        $this->attackDelay = $nbt->getInt("AttackDelay");
        $this->cooldown_respawn = $nbt->getInt("CooldownRespawn");
        $this->setNameTagAlwaysVisible();
        $this->setNameTagVisible();
        parent::initEntity($nbt);
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        if(!$this->isAlive() and !$this->closed) {
            $this->flagForDespawn();
            return false;
        }
        if($this->jumpTicks > 0) {
            $this->jumpTicks -= $tickDiff;
        }
        $target = $this->target;
        if($target != null) {
            if($target->isAlive()) {
                if($this->getLocation()->distance($target->getLocation()) > 20) {
                    $this->target = null;
                }else{
                    if($target instanceof Player && $target->isSurvival()) {
                        $this->moveToTarget();
                    }
                }
            }else{
                $this->target = null;
            }
        }
        if($this->target == null) {
            $this->findTarget();
        }
        $this->updateScore();
        return parent::entityBaseTick($tickDiff);
    }

    public function updateScore(){
        $this->setScoreTag($this->getScoreTag());
    }

    public function moveToTarget(): void{
        if($this->target !== null){
            $targetPos = $this->target->getPosition();
            $this->doMovement($targetPos);
            if($this->getLocation()->distance($targetPos) <= 1){
                $this->attackDelay--;
                if($this->attackDelay <= 0){
                    $this->attackTarget();
                }
            }
        }
    }

    public function attackTarget(): void{
        if($this->target !== null){
            $target = $this->target;
            $targetPos = $target->getPosition();
            $xDiff = $targetPos->x - $this->getLocation()->getX();
            $yDiff = $targetPos->y - $this->getLocation()->getY();
            $zDiff = $targetPos->z - $this->getLocation()->getZ();
            $distance = sqrt($xDiff * $xDiff + $yDiff * $yDiff + $zDiff * $zDiff);
            $attackDamge = $this->getDamage();
            if($distance <= $this->getAttackRange()){
                if($target instanceof Player && $target->isAlive()){
                    $armorPoint = ($this->target?->getArmorPoints() == 0) ? 0.1 : $this->target?->getArmorPoints();
                    $attackDamge += $attackDamge / ($armorPoint * 1.5);
                }
                $this->target->attack(new EntityDamageEvent($this->target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $attackDamge));
                $target->doHitAnimation();
                $this->server->broadcastPackets($this->hasSpawned, [ActorEventPacket::create($this->getId(), ActorEvent::ARM_SWING, 0)]);
                $this->attackDelay = 16;
            }
        }
    }

    public function findTarget(): void{
        $target = null;
        foreach($this->getWorld()->getEntities() as $entity){
            if($entity instanceof Player && $entity->isAlive() && $entity->isSurvival()){
                $square = $entity->getLocation()->distanceSquared($this->getLocation());
                if($square <= $this->getAttackRange()){
                    $target = $entity;
                    break;
                }
            }
        }
        $this->target = $target;
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setString("BossId", $this->bossId);
        $nbt->setInt("BossLevel", $this->bossLevel);
        $nbt->setFloat("Damage", $this->damage);
        $nbt->setInt("AttackRange", $this->attackRange);
        $nbt->setInt("AttackDelay", $this->attackDelay);
        $nbt->setInt("CooldownRespawn", $this->cooldown_respawn);
        return $nbt;
    }

    public function attack(EntityDamageEvent $source): void{
        if($source instanceof EntityDamageByEntityEvent){
            if($source->getDamager() instanceof Player) {
                $damger = $source->getDamager();
                if(isset($this->damgers[$damger->getName()])) {
                    $this->damgers[$damger->getName()] += $source->getBaseDamage();
                }else {
                    $this->damgers[$damger->getName()] = $source->getBaseDamage();
                }
                $this->lookAtLocation($damger->getLocation());
                $this->target = $damger;
            }
        }
        parent::attack($source);
    }

    public function kill(): void{
        $this->rewardToHighestDamger();
        parent::kill();
    }

    public function onDispose(): void{
        parent::onDispose();
    }

    public function getHighestDamger(): array{
        $highestDamger = null;
        $highestDamage = 0;
        foreach($this->damgers as $damger => $damage){
            if($damage > $highestDamage){
                $highestDamger = $damger;
                $highestDamage = $damage;
            }
        }
        return [$highestDamger, $highestDamage];
    }


    public function doMovement(Position $targetPos): void
    {
        $facing = $this->getHorizontalFacing();

        $location = $this->getLocation();

        $motion = $this->getMotion();

        $xDist = $targetPos->x - $location->x;
        $zDist = $targetPos->z - $location->z;
        $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;

        if ($yaw < 0) {
            $yaw += 360.0;
        }

        $this->setRotation($yaw, 0);

        $x = -1 * sin(deg2rad($yaw));
        $z = cos(deg2rad($yaw));
        $directionVector = (new Vector3($x, 0, $z))->normalize()->multiply($this->getSpeed());

        $motion->x = $directionVector->x;
        $motion->z = $directionVector->z;

        if ($this->isOnGround() && $this->isCollidedHorizontally && $this->jumpTicks <= 0) {

            $isFullJump = null;

            $block = $location->getWorld()->getBlock($location);

            $aboveBlock = $location->getWorld()->getBlock($location->add(0, 1, 0));

            $frontBlock = $location->getWorld()->getBlock($location->add(0, 0.5, 0)->getSide($facing));

            $secondFrontBlock = $location->getWorld()->getBlock($frontBlock->getPosition()->add(0, 1, 0));

            if ($block instanceof Air && $aboveBlock instanceof Air && !$frontBlock instanceof Air && $secondFrontBlock instanceof Air) {
                if (($frontBlock instanceof Slab && !$frontBlock->getSlabType()->equals(SlabType::TOP()) && !$frontBlock->getSlabType()->equals(SlabType::DOUBLE())) || ($frontBlock instanceof Stair && !$frontBlock->isUpsideDown() && $frontBlock->getFacing() === $facing)) {
                    $isFullJump = false;
                } else {
                    $isFullJump = true;
                }
            } elseif ($block instanceof Stair || $block instanceof Slab && $frontBlock instanceof Air && $secondFrontBlock instanceof Air && $aboveBlock instanceof Air) {
                $isFullJump = false;
            }

            if ($isFullJump !== null) {
                $motion->y = ($isFullJump ? 0.42 : 0.3) + $this->gravity;
                $this->jumpTicks = $isFullJump ? 5 : 2;
            }

            if ($motion->y > 0) {
                $motion->x /= 3;
                $motion->z /= 3;
            }
        }
        $this->setMotion($motion);
    }

    protected function lookAtLocation(Location $location): array{
        $angle = atan2($location->z - $this->getLocation()->z, $location->x - $this->getLocation()->x);
        $yaw = (($angle * 180) / M_PI) - 90;
        $angle = atan2((new Vector2($this->getLocation()->x, $this->getLocation()->z))->distance(new Vector2($location->x, $location->z)), $location->y - $this->getLocation()->y);
        $pitch = (($angle * 180) / M_PI) - 90;

        $this->setRotation($yaw, $pitch);

        return [$yaw, $pitch];
    }

    public function cooldownRespawn() :void {
        $time = $this->getCooldownRespawn();
        if($time > 0){
            Boss::getInstance()->getScheduler()->scheduleRepeatingTask(new class($time, $this) extends Task{
                use BossTrait;
                public function __construct(
                    private int $timer,
                    private BossEntity $entity
                ){}

                public function onRun(): void
                {
                    if($this->timer <= 0){
                        $this->createBossById($this->entity->getBossId());
                        $this->getHandler()->cancel();
                    }
                    $this->timer--;
                }
            }, 20);
        }
    }

    public function rewardToHighestDamger() :void{
        $highestDamger = $this->getHighestDamger();
        if($highestDamger[0] !== null){
            $player = Server::getInstance()->getPlayerExact($highestDamger[0]);
            if(!is_null($player)) {
                $entityName = $this->getName();
                if($player instanceof Player){
                    $this->getLootRewards()?->reward($player);
                }
                Server::getInstance()->broadcastMessage(TextFormat::colorize("&a{$player->getName()} §fhas received a reward for killing &a$entityName with $highestDamger[1] damage"));
                Utils::sendToast($player, "&l&aCongratulations", "&fYou have received a reward for killing &e$entityName&f with&f $highestDamger[1] damage");
            }
        }
    }

    public function getScoreTag() :string{
        $percentHealth = round($this->getHealth() / $this->getMaxHealth() * 100);
        return "§f§lHP§r§f: §a$percentHealth%";
    }

    public function getLootRewards() :?Reward{
        return $this->reward;
    }

    private function getSpeed() :float{
        return $this->speed;
    }

    private function getSpawnLocation() :Location{
        return $this->spawnPos;
    }

    public function getDamage() :float {
        return $this->damage;
    }

    public function getAttackRange(): int{
        return $this->attackRange;
    }

    public function getBossId(): string{
        return $this->bossId;
    }

    public function setSpeed(float $speed): void{
        $this->speed = $speed;
    }

    public function setBossId(string $bossId): void{
        $this->bossId = $bossId;
    }

    public function setLootReward(?Reward $reward): void{
        $this->reward = $reward;
    }

    public function getCooldownRespawn(): int{
        return $this->cooldown_respawn;
    }

}