<?php
declare(strict_types=1);

namespace itz;

use itz\PluginUtils;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\Config;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacketV2;
use pocketmine\level\sound\PopSound;
use jojoe77777\FormAPI;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\world\World;

class Main extends PluginBase implements Listener {
	
	public function onEnable() : void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        @mkdir($this->getDataFolder());
       $this->saveDefaultConfig();
       $this->getResource("config.yml");
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label,array $args): bool{
		switch($cmd->getName()){
			case "menusc":
			if(!$sender instanceof Player){
                 $this->getMenuUI($sender);
                 PluginUtils::PlaySound($sender, "random.levelup", 1, 1);
                }else{
                 $this->getMenuUI($sender);
                 PluginUtils::PlaySound($sender, "random.levelup", 1, 1);
              }
          }
		return true;
	}
	
	/*
	
	## Tienda UI
	
	*/
	
	public function getMenuUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
			    $this->getPerfilUI($player);
			    PluginUtils::PlaySound($player, "random.levelup", 1, 1);
			    break;
				case 1:
			    $this->getInfoUI($player);
			    PluginUtils::PlaySound($player, "random.levelup", 1, 1);
				break;
				case 2:
			    $this->getRedesSUI($player);
			    PluginUtils::PlaySound($player, "random.levelup", 1, 1);
				break;
				case 3:
			    $this->getRangosUI($player);
			    PluginUtils::PlaySound($player, "random.levelup", 1, 1);
				break;
				case 4:
			    $this->getReglasUI($player);
			    PluginUtils::PlaySound($player, "random.levelup", 1, 1);
                break;
			}
		});	
		
		
		$form->setTitle("§l§bMENU §aEspirabCraft");
 		$form->addButton("§l§aPERFIL\n§r§0[Abra el formulario]",0,"textures/ui/dressing_room_customization");
 		$form->addButton("§l§aINFORMACIÓN\n§r§0[Abra el formulario]",0,"textures/ui/icon_staffpicks");
 		$form->addButton("§l§aREDES\n§r§0[Abra el formulario]",0,"textures/ui/color_picker");
 		$form->addButton("§l§aRANGOS\n§r§0[Abra el formulario]",0,"textures/ui/icon_summer");
 		$form->addButton("§l§aREGLAS\n§r§0[Abra el formulario]",0,"textures/ui/dev_glyph_color");
		$form->addButton("§l§cSALIR",0,"textures/ui/realms_red_x");
		$form->sendToPlayer($player);
	}
	
	public function getInfoUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$this->getMenuUI($player);
				PluginUtils::PlaySound($player, "random.levelup", 1, 1);
			}
		});					
		$form->setTitle("§l§gINFORMACION");
        $form->setContent($this->getConfig()->get("Content-InfoButton")); 
        $form->addButton("§l§cATRAS", 0,"textures/ui/refresh_light");
		$form->sendToPlayer($player);
	}
	
	public function getRedesSUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$this->getMenuUI($player);
				PluginUtils::PlaySound($player, "random.levelup", 1, 1);
			}
		});					
		$form->setTitle("§l§gREDES");
        $form->setContent($this->getConfig()->get("Content-RedesButton")); 
        $form->addButton("§l§cATRAS", 0,"textures/ui/refresh_light");
		$form->sendToPlayer($player);
	}
	
	public function getRangosUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$this->getMenuUI($player);
				PluginUtils::PlaySound($player, "random.levelup", 1, 1);
			}
		});					
		$form->setTitle("§l§gRANGOS");
        $form->setContent($this->getConfig()->get("Content-RangosButton")); 
        $form->addButton("§l§cATRAS", 0,"textures/ui/refresh_light");
		$form->sendToPlayer($player);
	}
	
	public function getReglasUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
			}
			switch($result){
				case 0:
				$this->getMenuUI($player);
				PluginUtils::PlaySound($player, "random.levelup", 1, 1);
			}
		});					
		$form->setTitle("§l§gREGLAS");
        $form->setContent($this->getConfig()->get("Content-ReglasButton")); 
        $form->addButton("§l§cATRAS", 0,"random.levelup");
		$form->sendToPlayer($player);
	}
	
    public function getPerfilUI($sender){
        $form = new SimpleForm(function (Player $sender, $data){
            $result = $data;
            if($result === null){
                return true;
            }
            switch($result){
                case 0:
				$this->getMenuUI($sender);
				PluginUtils::PlaySound($sender, "random.levelup", 1, 1);
                break;
                
             }
        });
        $p = $sender;
        $name = $p->getName();
        $rank = $this->getServer()->getPluginManager()->getPlugin("PurePerms")->getUserDataMgr()->getGroup($p)->getName();
        $eco = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $money = $eco->myMoney($p);
        $ping = $p->getNetworkSession()->getPing();
        $world = $p->getWorld()->getProvider()->getWorldData()->getName();
        $itemInHand = $p->getInventory()->getItemInHand()->getName();
        $health = $p->getHealth();
        $gm = $p->getGamemode()->getEnglishName();
        $x = $p->getPosition()->getX();
        $y = $p->getPosition()->getY();
        $z = $p->getPosition()->getZ();
        $form->setTitle("§bProfile");
        $form->setContent("Esta es la informacion tuya en el servidor\n\n§7"."\n§fName : §a".$name."\n§fRank : §a".$rank."\n§fMoney : §a".$money."\n§fHealth : §a".$health."\n§fItemUsing : §a".$itemInHand."\n§fGamemode: §a".$gm."\n§fPing : §a".$ping."\n§fWorld/Map : §a".$world."\n§fPosition : §a".$x." ".$y." ".$z);
        $form->addButton("§l§cATRAS",0, "textures/ui/refresh_light");
        $form->sendToPlayer($sender);
    }
}