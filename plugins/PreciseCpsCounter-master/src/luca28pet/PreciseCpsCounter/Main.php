<?php

namespace luca28pet\PreciseCpsCounter;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use function array_unshift;
use function array_pop;
use function microtime;
use function round;
use function count;
use function array_filter;

class Main extends PluginBase implements Listener{

    private const ARRAY_MAX_SIZE = 100;
    public static $instance;

    /** @var bool */
    private $countLeftClickBlock;

    /** @var array[] */
    private $clicksData = [];

    private $cps = [];

    private $coolDown;
    private $timer = [];

    public function onEnable() : void{
        $this->saveDefaultConfig();
        self::$instance = $this;
        $this->countLeftClickBlock = $this->getConfig()->get('count-left-click-on-block');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->coolDown = 2;
    }

    public function initPlayerClickData(Player $p) : void{
        $this->clicksData[mb_strtolower($p->getName())] = [];
        $this->cps[mb_strtolower($p->getName())] = [];
    }

    public function addClick(Player $p) : void{
        array_unshift($this->clicksData[mb_strtolower($p->getName())], microtime(true));
        if(count($this->clicksData[mb_strtolower($p->getName())]) >= self::ARRAY_MAX_SIZE){
            array_pop($this->clicksData[mb_strtolower($p->getName())]);
        }
        if(isset($this->clicksData[mb_strtolower($p->getName())]) && isset($this->cps[mb_strtolower($p->getName())])) {
            $this->getScheduler()->scheduleRepeatingTask(new CPSTASK($p), 20);
        }else{
            $this->getScheduler()->cancelAllTasks();
        }

        if($this->getCps($p) > 19){
            foreach (Server::getInstance()->getOnlinePlayers() as $player){
                if($player instanceof Player){
                    if($player->hasPermission("cps.see")){
                        if (!isset($this->timer[$player->getName()]) or time() > $this->timer[$player->getName()]) {
                            $this->timer[$player->getName()] = time() + $this->coolDown;
                            $player->sendMessage("§l§9»§r§9 {$p->getName()} §ffait actuellement§9 {$this->getCps($p)} §fcps §f(§9{$p->getNetworkSession()->getPing()} §fPing) !");
                        }
                    }
                }
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        $commandname = $command->getName();
        if($commandname === "cps"){
            if($sender instanceof Player){
                $name = $sender->getName();
                if(isset($this->cps[mb_strtolower($name)])) {
                    unset($this->cps[mb_strtolower($name)]);
                    $sender->sendMessage("§l§6»§r§f Vous avez bien désactivé le cps counter !");
                }else{
                    $this->cps[mb_strtolower($name)] = [];
                    $sender->sendMessage("§l§6»§r§f Vous avez bien activé le cps counter !");
                }
            }
        }
        if($commandname === "seecps") {
            if ($sender instanceof Player) {
                if(isset($args[0])){
                    $player = Server::getInstance()->getPlayerExact($args[0]);
                    if($player instanceof Player){
                        $sender->sendMessage("");
                    }
                }else{
                    $sender->sendMessage("§l§6»§r§f Le joueur n'a pas été trouvé !");
                }
            }
        }return true;
    }

    /**
     * @param Player $player
     * @param float $deltaTime Interval of time (in seconds) to calculate CPS in
     * @param int $roundPrecision
     * @return float
     */
    public function getCps(Player $player, float $deltaTime = 1.0, int $roundPrecision = 1) : float{
        if(!isset($this->clicksData[mb_strtolower($player->getName())]) || empty($this->clicksData[mb_strtolower($player->getName())])){
            return 0.0;
        }
        $ct = microtime(true);
        return round(count(array_filter($this->clicksData[mb_strtolower($player->getName())], static function(float $t) use ($deltaTime, $ct) : bool{
            return ($ct - $t) <= $deltaTime;
        })) / $deltaTime, $roundPrecision);
    }

    public function removePlayerClickData(Player $p) : void{
        unset($this->clicksData[mb_strtolower($p->getName())]);
        unset($this->cps[mb_strtolower($p->getName())]);
    }

    public function playerJoin(PlayerJoinEvent $e) : void{
        $this->initPlayerClickData($e->getPlayer());
    }

    public function playerQuit(PlayerQuitEvent $e) : void{
        $this->removePlayerClickData($e->getPlayer());
        $this->getScheduler()->cancelAllTasks();
    }

    public function packetReceive(DataPacketReceiveEvent $e) : void{
        $player = $e->getOrigin()->getPlayer();
        if($player !== null && isset($this->clicksData[mb_strtolower($player->getName())]) &&
            (
                ($e->getPacket()::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID && $e->getPacket()->trData instanceof UseItemOnEntityTransactionData) ||
                ($e->getPacket()::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $e->getPacket()->sound === LevelSoundEvent::ATTACK_NODAMAGE) ||
                ($this->countLeftClickBlock && $e->getPacket()::NETWORK_ID === PlayerActionPacket::NETWORK_ID && $e->getPacket()->action === PlayerAction::START_BREAK)
            )
        ){
            $this->addClick($player);
        }
    }
    public static function getInstance() : Main {
        return self::$instance;
    }
}
