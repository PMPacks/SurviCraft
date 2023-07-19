<?php

declare(strict_types=1);

namespace phuongaz\boss;

use phuongaz\boss\commands\BossCommand;
use phuongaz\boss\entity\BossEntity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class Boss extends PluginBase{
    use SingletonTrait;

    public function onLoad(): void{
        self::setInstance($this);
    }

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->saveDefaultConfig();
        EntityFactory::getInstance()->register(BossEntity::class, function(World $world, CompoundTag $nbt) :BossEntity{
            return new BossEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["Human"]);
        $this->getServer()->getCommandMap()->register("boss", new BossCommand());
    }

    public function onDisable(): void{
        foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
            foreach($world->getEntities() as $entity){
                if($entity instanceof BossEntity){
                    $entity->kill();
                }
            }
        }
    }

}