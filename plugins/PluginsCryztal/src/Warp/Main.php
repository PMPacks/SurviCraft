<?php

namespace Warp;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use Warp\PluginUtils;
use Warp\commands\BazaarCommand;
use Warp\commands\JoinCommand;
use Warp\commands\BonusCommand;
use pocketmine\item\{ItemIds, Item, ItemFactory};
use pocketmine\Server;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV2;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\level\sound\PopSound;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use Warp\utils\PluginUtilsPE;
use pocketmine\utils\Utils;
use function str_replace;
class Main extends PluginBase implements Listener {
	
    private $main;
    private static $clicks = [];
	public function onEnable() : void
	{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("joinacm", new JoinCommand($this));
        $this->getServer()->getCommandMap()->register("bazaar", new BazaarCommand($this));
        $this->getServer()->getCommandMap()->register("bonus", new BonusCommand($this));
        
        @mkdir($this->getDataFolder());
       $this->saveDefaultConfig();
        $this->main = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        
		$this->getServer()->getWorldManager()->loadWorld("Lobby");
		$this->getServer()->getWorldManager()->loadWorld("MinaSafe");
		$this->getServer()->getWorldManager()->loadWorld("mp");
		$this->getServer()->getWorldManager()->loadWorld("mv");
		$this->getServer()->getWorldManager()->loadWorld("world");
		$this->getServer()->getWorldManager()->loadWorld("FFA");
		$this->getServer()->getWorldManager()->loadWorld("ItzAngelZzYT");
	}
	
    public function addClick(Player $player)
    {
        if(!isset(self::$clicks[$player->getName()]) || empty(self::$clicks[$player->getName()])){
            self::$clicks[$player->getName()][] = microtime(true);
	}else{
	    array_unshift(self::$clicks[$player->getName()], microtime(true));
	    if (count(self::$clicks[$player->getName()]) >= 100) {
	        array_pop(self::$clicks[$player->getName()]);
	    }
	        $player->sendTip($this->getConfig()->get("CpsFormat") . TextFormat::RESET . self::getCps($player));
	}   
    }

    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->addClick($event->getOrigin()->getPlayer());
            }
        }
        if ($packet instanceof LevelSoundEventPacket and $packet->sound == 42) {
            $this->addClick($player);
         }
        if ($event->getPacket()->pid() === AnimatePacket::NETWORK_ID) {
            $event->getOrigin()->getPlayer()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [$event->getPacket()]);
            $event->cancel();
        }
    }
    public static function getCps(Player $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (empty(self::$clicks[$player->getName()])) {
            return 0.0;
        }
        $mt = microtime(true);
        return round(count(array_filter(self::$clicks[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {
                return ($mt - $t) <= $deltaTime;
            })) / $deltaTime, $roundPrecision);
    }
    
	
    public function onJoin(PlayerJoinEvent $event): void{
        $player = $event->getPlayer();
        $event->setJoinMessage("");
             $player->teleport($this->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        $this->getJoinUI($player);
        PluginUtils::PlaySound($player, $this->getConfig()->get("PlaySoundJoin"), 1, 1);
        $message1 = $this->getConfig()->get("PlayerJoinMessage");
        $this->getServer()->broadcastMessage(str_replace("{PLAYER}", $player->getName(), $message1));
    }
    
    public function onQuit(PlayerQuitEvent $event): void{
        $player = $event->getPlayer();
        $event->setQuitMessage("");
        if ($this->main->get("QuitSound", false) === false) {
            PluginUtils::PlaySound($player, $this->main->get("PlaySoundQuit"), 1, 1);
        }
        if ($this->main->get("PlayerQuit", false) === false) {
        $message2 = $this->main->get("PlayerQuitMessage");
        $this->getServer()->broadcastMessage(str_replace("{PLAYER}", $player->getName(), $message2));
        }
    }
    
	public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args): bool{
		switch($cmd->getName()){
			case "warp":
			if(!$sender instanceof Player){
                 $this->openWarp($sender);
                 PluginUtils::PlaySound($sender, "bubble.downinside", 1, 1);
                }else{
                 $this->openWarp($sender);
                 PluginUtils::PlaySound($sender, "bubble.downinside", 1, 1);
              }
          }
		return true;
	}
	
	public function openWarp($player) {
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
			    $this->getLobbyUI($player);
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
			    $this->getMinapvpUI($player);
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 2:
			    $this->getMinasafeUI($player);
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 3:
			    $this->getMinavipUI($player);
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 4:
			    $this->getSurvivalUI($player);
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 5:
			    $this->getGamesUI($player);
			    PluginUtils::PlaySound($player, "bubble.up", 1, 1);
			    break;
			    case 6:

                break;
				
			}
		});
		$form->setTitle($this->getConfig()->get("TitleForm-Warp"));
		$form->addButton("§l§eLOBBY\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("Lobby")->getPlayers()), 0, "textures/items/lodestonecompass_item");
		$form->addButton("§l§eMINAPVP\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("mp")->getPlayers()), 0, "textures/items/diamond_pickaxe");
		$form->addButton("§l§eMINACOMUN\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("MinaSafe")->getPlayers()), 0, "textures/items/iron_pickaxe");
		$form->addButton("§l§eMINAVIP\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("mv")->getPlayers()), 0, "textures/items/netherite_pickaxe");
		$form->addButton("§l§eSURVIVAL\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("world")->getPlayers()), 0, "textures/ui/icon_new");
 		$form->addButton($this->getConfig()->get("Button-Games"),0,"textures/ui/bad_omen_effect");
		$form->addButton("§l§cSALIR",0,"textures/ui/redX1");
		$player->sendForm($form);
	}
	
	public function getGamesUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("GamesButton1"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
                $player->sendTitle($this->getConfig()->get("Title-Game1"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Game1"));
				break;
				case 1:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("GamesButton2"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
                $player->sendTitle($this->getConfig()->get("Title-Game2"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Game2"));
                break;
                case 2:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Games"));
		$form->addButton("§l§eFFA\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("FFA")->getPlayers()), 0, "textures/ui/strength_effect");
		$form->addButton("§l§cBOSS\n§r§7Jugadores§b ".count($this->getServer()->getWorldManager()->getWorldByName("ItzAngelZzYT")->getPlayers()), 0, "textures/ui/jump_boost_effect");
        $form->addButton("§l§cATRAS", 0,"textures/ui/refresh_light");
		$form->sendToPlayer($player);
	}
	
	public function getLobbyUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$player->teleport($this->getServer()->getWorldManager()->getWorldByName("Lobby")->getSafeSpawn());
                $player->sendTitle($this->getConfig()->get("Title-Lobby"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Lobby"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Lobby"));
		$form->setContent($this->getConfig()->get("ContentForm-Lobby"));
        $form->addButton($this->getConfig()->get("Button1-Lobby"), "0", "textures/ui/check");
        $form->addButton($this->getConfig()->get("Button2-Lobby"), "0", "textures/ui/redX1");
		$form->sendToPlayer($player);
	}
	
	public function getMinavipUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
            if (!$player->hasPermission("minavip.warp")) {
                $player->sendMessage($this->getConfig()->get("NoPermission-Minavip"));
                return;
                }
				$player->teleport($this->getServer()->getWorldManager()->getWorldByName("mv")->getSafeSpawn());
                $player->sendTitle($this->getConfig()->get("Title-Minavip"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Minavip"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Minavip"));
		$form->setContent($this->getConfig()->get("ContentForm-Minavip"));
        $form->addButton($this->getConfig()->get("Button1-Minavip"), "0", "textures/ui/check");
        $form->addButton($this->getConfig()->get("Button2-Minavip"), "0", "textures/ui/redX1");
		$form->sendToPlayer($player);
	}
	
	public function getMinapvpUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$player->teleport($this->getServer()->getWorldManager()->getWorldByName("mp")->getSafeSpawn());
                $player->sendTitle($this->getConfig()->get("Title-Minapvp"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Minapvp"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Minapvp"));
		$form->setContent($this->getConfig()->get("ContentForm-Minapvp"));
        $form->addButton($this->getConfig()->get("Button1-Minapvp"), "0", "textures/ui/check");
        $form->addButton($this->getConfig()->get("Button2-Minapvp"), "0", "textures/ui/redX1");
		$form->sendToPlayer($player);
	}
	
	public function getMinasafeUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$player->teleport($this->getServer()->getWorldManager()->getWorldByName("MinaSafe")->getSafeSpawn());
                $player->sendTitle($this->getConfig()->get("Title-Minasafe"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Minasafe"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Minasafe"));
		$form->setContent($this->getConfig()->get("ContentForm-Minasafe"));
        $form->addButton($this->getConfig()->get("Button1-Minasafe"), "0", "textures/ui/check");
        $form->addButton($this->getConfig()->get("Button2-Minasafe"), "0", "textures/ui/redX1");
		$form->sendToPlayer($player);
	}
	
	public function getSurvivalUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$player->teleport($this->getServer()->getWorldManager()->getWorldByName("world")->getSafeSpawn());
                $player->sendTitle($this->getConfig()->get("Title-Survival"));
                $player->sendSubTitle($this->getConfig()->get("SubTitle-Survival"));
                PluginUtils::PlaySound($player, "break.tuff", 1, 2);
				break;
				case 1:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
			}
		});					
		$form->setTitle($this->getConfig()->get("TitleForm-Survival"));
		$form->setContent($this->getConfig()->get("ContentForm-Survival"));
        $form->addButton($this->getConfig()->get("Button1-Survival"), "0", "textures/ui/check");
        $form->addButton($this->getConfig()->get("Button2-Survival"), "0", "textures/ui/redX1");
		$form->sendToPlayer($player);
	}
	/*------------------------------*/
	/*Bazar Form Adicional*/
	/*------------------------------*/
    public function getBazaarUI($player){
        $form = new SimpleForm(function(Player $player, int $data = null){
            if($data === null){
                return true;
            }

            switch($data){
                case 0:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("CommandButton1"));
                break;

                case 1:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("CommandButton2"));
                break;

                case 2:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("CommandButton3"));
                break;
            }

        });
        $form->setTitle($this->getConfig()->get("TitleBazaar"));
        $form->setContent($this->getConfig()->get("ContentBazaar"));
        $form->addButton($this->getConfig()->get("BazaarButton1"), "0", "textures/items/gold_ingot");
        $form->addButton($this->getConfig()->get("BazaarButton2"), "0", "textures/items/emerald");
        $form->addButton($this->getConfig()->get("BazaarButton3"), "0", "textures/items/diamond");
        $form->sendToPlayer($player);
    }
    
    public function getBonusUI($player){
        $form = new SimpleForm(function(Player $player, int $data = null){
            if($data === null){
                return true;
            }

            switch($data){
                case 0:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand1"));
                break;

                case 1:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand2"));
                break;

                case 2:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand3"));
                break;

                case 3:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand4"));
                break;

                case 4:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand5"));
                break;

                case 5:
                    $this->getServer()->getCommandMap()->dispatch($player, $this->getConfig()->get("BonusCommand6"));
                break;
            }

        });
        $form->setTitle($this->getConfig()->get("TitleBonus"));
        $form->addButton($this->getConfig()->get("BonusButton1"), "0", "textures/ui/icon_blackfriday");
        $form->addButton($this->getConfig()->get("BonusButton2"), "0", "textures/ui/inventory_icon");
        $form->addButton($this->getConfig()->get("BonusButton3"), "0", "textures/ui/icon_blackfriday");
        $form->addButton($this->getConfig()->get("BonusButton4"), "0", "textures/ui/inventory_icon");
        $form->addButton($this->getConfig()->get("BonusButton5"), "0", "textures/ui/icon_blackfriday");
        $form->addButton($this->getConfig()->get("BonusButton6"), "0", "textures/ui/inventory_icon");
        $form->sendToPlayer($player);
    }
    
    public function getJoinUI($player){
        $form = new SimpleForm(function($player, $data){
		$result = $data;
        if($result !== null){
            switch($result){
                case 0:
				$this->openWarp($player);
				PluginUtils::PlaySound($player, "bubble.down", 1, 1);
                break;
                case 1:
                $player->sendMessage($this->getConfig()->get("Join-Message"));
                $player->sendTitle($this->getConfig()->get("Join-Title"));
                $player->sendSubTitle($this->getConfig()->get("Join-SubTitle"));
                PluginUtils::PlaySound($player, $this->getConfig()->get("PlaySoundButton"), 1, 1);
                  break;
                  }
              }
        });
        $form->setTitle($this->getConfig()->get("JoinUI-Title"));
        $form->setContent($this->getConfig()->get("JoinUI-Content"));
        $form->addButton($this->getConfig()->get("WarpJoinButton"), "0", "textures/ui/controller_glyph_color");
        $form->addButton($this->getConfig()->get("JoinUI-Button"),0,"textures/ui/redX1");
        $player->sendForm($form);
    }
}