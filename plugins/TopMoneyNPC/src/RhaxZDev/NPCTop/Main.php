<?php

namespace RhaxZDev\NPCTop;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\{Event, Listener};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\scheduler\Task;
use pocketmine\command\{Command, CommandSender};
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{CompoundTag, StringTag, ByteArrayTag, DoubleTag, FloatTag, ListTag, ShortTag};

class Main extends PluginBase implements Listener {
    
    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->api = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->saveResource("steve.json");
        $this->saveResource("steve.png");
       $this->saveResource("config.yml");
        $this->getScheduler()->scheduleRepeatingTask(new NPCTask($this), 20 * $this->getConfig()->get("task-updater"));
        if($this->api == null){
            $this->getServer()->shutdown();
            $this->getLogger()->alert("EconomyAPI not installed!");
        }else{
            $this->getLogger()->info("EconomyAPI Installed :)");
        }
        EntityFactory::getInstance()->register(NPC::class, function ($world, $nbt) : NPC {
            return new NPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ["NPCTop"]);
    }
    
    public function onCommand(CommandSender $p, Command $cmd, String $label, array $args): bool{
        if($cmd->getName() === 'topnpc'){
            if($p->hasPermission("cmd.topnpc")){
                $arg = array_shift($args);
                switch($arg){
                    case "spawn":
                        if(isset($args[0])){
                            if(is_numeric($args[0])){
                                $this->spawnNPC($p, $args[0]);
                            }else{
                                $p->sendMessage("§cTop Number Must Type Numeric! ");
                            }
                        }else{
                            $p->sendMessage("§aUse: §7/topnpc spawn <top>");
                        }
                    break;
                    case "remove":
                        if(isset($args[0])){
                            if(is_numeric($args[0])){
                                $npc = $this->getDistanceNPC($p, $args[0]);
                                if($npc !== null){
                                    $npc->flagForDespawn();
                                    $p->sendMessage("§aSucces Remove NPC!");
                                }else{
                                    $p->sendMessage("§cNPC Not Found!");
                                }
                            }else{
                                $p->sendMessage("§cId Number Top Must Type Of Numeric!");
                            }
                        }else{
                            $p->sendMessage("§aUse: §7/topnpc remove <top>");
                        }
                    break;
                    case "help":
                        $p->sendMessage("§l§a>>>> §r§aTopMoneyNPC §l§a<<<<");
                        $p->sendMessage("§a- /topnpc spawn §b[numeric] §fTo Spawn TopNPC!");
                        $p->sendMessage("§a- /topnpc remove §b[numeric] §fTo Despawn TopNPC!");
                    break;
                    default:
                        $p->sendMessage("§aUse: §7/topnpc help");
                    break;
                }
                return true;
            }
        }
        return true;
    }
    
    public function onHitNPC(EntityDamageByEntityEvent $event) {
        if ($event->getEntity() instanceof NPC) {
            $player = $event->getDamager();
            if ($player instanceof Player) {
                $event->cancel(true);
            }else{
   $event->cancel(true);
   $event->getEntity()->setHealth($event->getEntity()->getMaxHealth());
  }
        }
    }
    
    public function getDistanceNPC(Player $player, int $top) {
        $world = $player->getWorld();
        foreach ($world->getEntities() as $entity){
            if($entity instanceof NPC){
                $nots = explode("\n", $entity->getNameTag());
                $nol = ltrim($nots[2], "§r[");
                $no = rtrim($nol, "]");
            var_dump($no);
                if((int)$no == $top){
                           return $entity;
                }else{
                    return null;
                }
            }
        }
        return null;
    }
    
    public function getPlayerByTop(int $top){
        $all = $this->api->getAllMoney();
        arsort($all);
        $tops = 1;
        foreach($all as $user => $money){
            if($tops == $top){
                return $user;
            }
            $tops++;
        }
    }
    
    public function forTask(Player $p){
        foreach($p->getWorld()->getEntities() as $entity){
            if($entity instanceof NPC){
                $nots = explode("\n", $entity->getNameTag());
                $nol = ltrim($nots[2], "§r[");
                $no = rtrim($nol, "]");
                $user = $this->getPlayerByTop((int)$no);
                if($user !== null){
                    $users = $this->getServer()->getPlayerExact($user);
                    $money = $this->api->myMoney($user);
                    if($users instanceof Player){
                        $skin = $users->getSkin();
                        $entity->setSkin($skin);
                        $entity->setNameTagAlwaysVisible(true);
                        $entity->setNameTag("§aTop #{$no}\n§e{$user} §f» §a{$money}\n§r[{$no}]");
                        $entity->sendSkin($this->getServer()->getOnlinePlayers());
                        $entity->spawnToAll();
                    }else{
                        $skin = $this->defaultSkin();
                        $entity->setSkin($skin);
                        $entity->setNameTagAlwaysVisible(true);
                        $entity->setNameTag("§aTop #{$no}\n§e{$user} §f» §a{$money}\n§r[{$no}]");
                        $entity->sendSkin($this->getServer()->getOnlinePlayers());
                        $entity->spawnToAll();
                    }
                }
            }
        }
    }
    
    public function spawnNPC(Player $p, $top){
        $user = $this->getPlayerByTop((int)$top);
        if($user !== null){
            $money = $this->api->myMoney($user);
            $users = $this->getServer()->getPlayerExact($user);
            $location = $p->getLocation();
            $nbt = $this->getNBT($p, $location);
            if($users instanceof Player){
                $skin = $users->getSkin();
                $npc = new NPC($location, $skin, $nbt);
                $npc->setSkin($skin);
                $npc->setNameTag("§aTop #{$top}\n§e{$user} §f» §a{$money}\n§r[{$top}]");
                $npc->setNameTagAlwaysVisible(true);
                $npc->sendSkin($this->getServer()->getOnlinePlayers());
                $npc->spawnToAll();
            }else{
                $skin = $this->defaultSkin();
                $npc = new NPC($location, $skin, $nbt);
                $npc->setSkin($skin);
                $npc->setNameTag("§aTop #{$top}\n§e{$user} §f» §a{$money}\n§r[{$top}]");
                $npc->setNameTagAlwaysVisible(true);
                $npc->sendSkin($this->getServer()->getOnlinePlayers());
                $npc->spawnToAll();
            }
        }else{
            $p->sendMessage("Player Of Top #{$top} Not Found!");
        }
    }
    
    public function defaultSkin(){
        $Texturefile = "steve.png";
        $geometryFile = "steve.json";
        $geometryIdentifier = "geometry.humanoid.custom";
        $texturePath = $this->getDataFolder() . $Texturefile;
        if(!file_exists($texturePath)) return null;
        // Magic SkinBytes creator
        $img = @imagecreatefrompng($texturePath);
        $size = getimagesize($texturePath);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);

        $modelPath = $this->getDataFolder() . $geometryFile;
        $newskin = new Skin("DefaultSkin", $skinbytes, "", $geometryIdentifier, file_get_contents($modelPath));
        return $newskin;
    }
    
    public function getNBT(Player $player, $location){
        $nbt = $this->createBaseNBT($location->asVector3(), null, $location->getYaw(), $location->getPitch());
        $nbt->setTag("Skin", CompoundTag::create()
            ->setString("Name", $player->getSkin()->getSkinId())
            ->setByteArray("Data", $player->getSkin()->getSkinData())
            ->setByteArray("CapeData", $player->getSkin()->getCapeData())
            ->setString("GeometryName", $player->getSkin()->getGeometryName())
            ->setByteArray("GeometryData", $player->getSkin()->getGeometryData())
        );
        return $nbt;
    }
    
    public function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0): CompoundTag {
        return CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($pos->x),
                new DoubleTag($pos->y),
                new DoubleTag($pos->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($motion !== null ? $motion->x : 0.0),
                new DoubleTag($motion !== null ? $motion->y : 0.0),
                new DoubleTag($motion !== null ? $motion->z : 0.0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($yaw),
                new FloatTag($pitch)
            ]));
    }
}

class NPCTask extends Task {
    
    public function __construct(Main $pl) {
        $this->pl = $pl;
    }
    
    public function onRun() : void{
        foreach($this->pl->getServer()->getOnlinePlayers() as $p){
            $this->pl->forTask($p);
        }
    }
}
