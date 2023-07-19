<?php

namespace Pushkar\MagicDiscord;

use Pushkar\MagicDiscord\PluginUtils;

use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;

class Main extends PluginBase implements Listener
{
		
		public function onEnable(): void
    {
      $this->getLogger()->info("Reportes Enabled - §aSurvi§bCraft");
      }
		
		public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args):bool {
        switch($cmd->getName()) {
        case "report": 
            if($sender instanceof Player) {
                $this->mainForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.enter", 1, 1);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "playerreport": 
            if($sender instanceof Player) {
                $this->reportForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.enter", 1, 1);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "suggest": 
            if($sender instanceof Player) {
                $this->suggestForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.enter", 1, 1);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
        case "bug": 
            if($sender instanceof Player) {
                $this->bugForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.enter", 1, 1);
            } else {
          $sender->sendMessage("command can extend in-game only");
        }
        break;
    }
    return true;
    }

		public function OnDisable(): void
    {
      /*if ($this->getConfig()->get("enable-shutdown-alert") === true) {
        $webhook = new Webhook($this->getConfig()->get("webhook-url"));
  			$colorval = hexdec($this->getConfig()->get("shutdown-embed-color"));
  			
  			$msg = new Message();
  			$msg->setUsername($this->getConfig()->get("webhook-username"));
  			$msg->setAvatarURL($this->getConfig()->get("webhook-avatar-url"));
  
  			$embed = new Embed();
  			$embed->setTitle($this->getConfig()->get("shutdown-message-title"));
  			$embed->setColor($colorval);
  			$embed->addField($this->getConfig()->get("shutdown-embed-field-title"), $this->getConfig()->get("shutdown-embed-field-message"));
  			$embed->setThumbnail($this->getConfig()->get("shutdown-thumbnail-url"));
  			$embed->setFooter($this->getConfig()->get("shutdown-footer-message"), $this->getConfig()->get("shutdown-footer-image-url"));
  			$msg->addEmbed($embed);
  			
  			$webhook->send($msg);
      }*/
    }
    
    public function reportForm($player) {
        $list = [];
        foreach($this->getServer()->getOnlinePlayers() as $p) {
            $list[] = $p->getName();
        }
        $this->players[$player->getName()] = $list;
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null) {
              $player->sendMessage("Report Failed");
                return true;
            }
            $web = new Webhook($this->getConfig()->get("report-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("report-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("report-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("report-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $index=$data[1];
            $e->setTitle("REPORTES");
            $e->addField("Acusado: ", $this->players[$player->getName()][$index]);
            $e->addField("Por: ", $player->getName());
            $e->addField("Razon: ", $data[2]);
            $e->setThumbnail($this->getConfig()->get("report-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§l§aSurvi§bCraft §r§c» §7Se envio el reporte con excito");
        });
        $name = $player->getName();
        $form->setTitle("      ");
        $form->addLabel("§fHola, §e$name\n\n§fEscriba su informe a continuación, se enviará a nuestros moderadores");
        $form->addDropdown("Selecciona un jugador: ", $this->players[$player->getName()]);
        $form->addInput("Escribe el reporte: ", "Type a reason", "Hacking");
        $form->sendToPlayer($player);
        return $form;
    }
    public function suggestForm($player){
		$form = new CustomForm(function (Player $player, array $data = null){
				$result = $data;
				if($result === null){
					return true;
				}
				if($result != null){
					$web = new Webhook($this->getConfig()->get("suggest-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("suggest-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("suggest-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("suggest-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $e->setTitle("SUGERENCIA");
            $e->addField("De: ", $player->getName());
            $e->addField("Sugerencia: ", $data[0]);
            $e->setThumbnail($this->getConfig()->get("suggest-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§lLIVE §r§c» §7Se envio tu sugerencia");
					return true;
				}
		});
		$name = $player->getName();
		$form->setTitle("§aSurvi§bCraft");
		$form->addInput("§fHola, §e$name\n\n§fEscriba su sugerencia para nuestro equipo de developers");
		$form->sendToPlayer($player);
		return $form;
    }
    public function bugForm($player){
		$form = new CustomForm(function (Player $player, array $data = null){
				$result = $data;
				if($result === null){
					return true;
				}
				if($result != null){
					$web = new Webhook($this->getConfig()->get("bug-webhook-url"));
            $colorval = hexdec($this->getConfig()->get("bug-embed-color"));
            $msg = new Message();
            $msg->setUsername($this->getConfig()->get("bug-webhook-username"));
  		    	$msg->setAvatarURL($this->getConfig()->get("bug-webhook-avatar-url"));
            $e = new Embed();
            $e->setColor($colorval);
            $e->setTitle("BUGS");
            $e->addField("Serendipia: ", $player->getName());
            $e->addField("Bug: ", $data[0]);
            $e->setThumbnail($this->getConfig()->get("bug-thumbnail-url"));
            $msg->addEmbed($e);
            $web->send($msg);
            $player->sendMessage("§e§lLIVE §r§c» §7Reporte de bug enviado exitosamente");
					return true;
				}
		});
		$name = $player->getName();
		$form->setTitle("§aSurvi§bCraft");
		$form->addInput("§fHola, §e$name\n\n§fEscriba su sugerencia para nuestro equipo de developers");
		$form->sendToPlayer($player);
		return $form;
    }
    public function mainForm(Player $sender){
        $form = new SimpleForm(function (Player $sender, $data) {
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                case 0:
                $this->reportForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.exit", 1, 1);
                    break;
                case 1:
                $this->suggestForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.exit", 1, 1);
                    break;
                case 2:
                $this->bugForm($sender);
                PluginUtils::PlaySound($sender, "block.beehive.exit", 1, 1);
                    break;
                case 3:
                PluginUtils::PlaySound($sender, "block.beehive.shear", 1, 1);
                    break;
            }
        });
        $name = $sender->getName();
        $form->setTitle("§aSurvi§bCraft");
        $form->setContent("§fHola §e$name\n\n§fSelecione algún tipo de reporte");
        $form->addButton("§l§bPLAYER REPORT\n§r§7Click to report player", 0, "textures/ui/book_addtextpage_hover");
        $form->addButton("§l§bSUGERENCIA\n§r§7Click to suggest", 0, "textures/ui/icon_map");
        $form->addButton("§l§bBUG REPORT\n§r§7Click yo report bug", 0, "textures/ui/anvil_icon");
        $form->addButton("§l§cSALIR\n§r§7Click to exit", 0, "textures/ui/cancel");
        $sender->sendForm($form);
    }
  }
