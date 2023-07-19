<?php

namespace FactionExtensionUI;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

use FactionExtensionUI\commands\FactionExtensionUICommand;

class Main extends PluginBase implements Listener {
	
	private $main;

	public function onEnable():void
    {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		
        $this->getServer()->getCommandMap()->register("factionui", new FactionExtensionUICommand($this));
        
        @mkdir($this->getDataFolder());
       $this->saveDefaultConfig();
       $this->getResource("config.yml");
       
	}
	
	 public function FactionExtensionUI($player){
		$form = new SimpleForm(function ($player, $data){
		$result = $data;
		if($result === null){
			return true;
          }
          switch($result){
              case 0:
			  $this->Create($player);
			  
              break;
              case 1:
              $command = "f del";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 2:
              $command = "f leave";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 3:
			  $this->Invite($player);
              break;
              case 4:
              $command = "f top";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 5:
              $command = "f accept";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 6:
              $command = "f border";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 7:
              $command = "f map";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 8:
              $command = "f home";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 9:
              $command = "f sethome";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 10:
              $command = "f delhome";
              $this->getServer()->getCommandMap()->dispatch($player, $command);
              
              break;
              case 11:
			  $this->Transferfaction($player);
			  
              break;
              case 12:
			  $this->Infofaction($player);
			  
              break;
              case 13:
			  $this->Chatfaction($player);
			  
              break;
              case 14:
			  $this->Promotefaction($player);
			  
              break;
              case 15:
			  $this->Demotefaction($player);
			  
              break;
              case 16:
			  $this->Kickfaction($player);
			  
			  break;
			  case 17:
			      
			  break;
          }
        });
        $config = $this->getConfig();
        $name = $player->getName();
        $form->setTitle("   ");
		$form->setContent("§fHola §e$name §feste es el menu de acceso rapido de factions");
        $form->addButton($this->getConfig()->get("Createfaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("DisbandFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("LeaveFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("InviteFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("TopFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("AcceptFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("BorderFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("MapFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("HomeFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("SetHomeFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("DelHomeFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("TransferFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("InfoFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("ChatFaction-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("PromoteMember-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("DemoteMember-Button"),0,"textures/ui/absorption_effect");
        $form->addButton($this->getConfig()->get("KickMember-Button"),0,"textures/ui/absorption_effect");
		$form->addButton("§l§cSALIR",0,"textures/ui/realms_red_x");
        $form->sendToPlayer($player);
	}
	
	public function Create($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f create $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§g§lFactionExtensionUI");
		$form->addInput("§bInput Your Factions Name");
		$form->sendToPlayer($player);
	}
	
	public function Invite($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f invite $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§einvite Player");
		$form->sendToPlayer($player);
		
	}
	
	public function Infofaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f info $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§eInfo factiom");
		$form->sendToPlayer($player);
		
	}

	public function Chatfaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f chat $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§eChat Faction: GLOBAL/FACTION/ALLIANCE");
		$form->sendToPlayer($player);
		
	}
	
	public function Transferfaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f transfer $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§eTransfer faction");
		$form->sendToPlayer($player);
	}

	public function Promotefaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f promote $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§ePromote Member");
		$form->sendToPlayer($player);
		
	}
	
	public function Demotefaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f demote $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§eDemote member");
		$form->sendToPlayer($player);
	}
	
	
	public function Kickfaction($player){
		$form = new CustomForm(function ($player, $data){
		$result = $data;
		if($result === null){
				return true;
			}
			$cmd = "f kick $data[0]";
			$this->getServer()->getCommandMap()->dispatch($player, $cmd);
		});
		$form->setTitle("§b§lFactionExtensionUI");
		$form->addInput("§eKick member");
		$form->sendToPlayer($player);
	}
}
