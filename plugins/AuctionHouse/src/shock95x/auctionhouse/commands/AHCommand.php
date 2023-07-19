<?php
declare(strict_types=1);

namespace shock95x\auctionhouse\commands;

use shock95x\auctionhouse\libs\CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use shock95x\auctionhouse\commands\subcommand\AboutCommand;
use shock95x\auctionhouse\commands\subcommand\AdminCommand;
use shock95x\auctionhouse\commands\subcommand\CategoryCommand;
use shock95x\auctionhouse\commands\subcommand\ConvertCommand;
use shock95x\auctionhouse\commands\subcommand\ExpiredCommand;
use shock95x\auctionhouse\commands\subcommand\ListingsCommand;
use shock95x\auctionhouse\commands\subcommand\ReloadCommand;
use shock95x\auctionhouse\commands\subcommand\SellCommand;
use shock95x\auctionhouse\commands\subcommand\ShopCommand;
use shock95x\auctionhouse\commands\subcommand\TestCommand;
use shock95x\auctionhouse\menu\ShopMenu;
use shock95x\auctionhouse\menu\ExpiredMenu;
use shock95x\auctionhouse\menu\type\AHMenu;
use shock95x\auctionhouse\AuctionHouse;

use shock95x\auctionhouse\libs\jojoe77777\FormAPI\SimpleForm;

class AHCommand extends BaseCommand {

	protected function prepare(): void {
		//$this->registerSubCommand(new ShopCommand("shop", "Shows AH shop menu"));
		//$this->registerSubCommand(new AdminCommand("admin", "Opens AH admin menu"));
		$this->registerSubCommand(new SellCommand("sell", "Sell item in hand to the AH"));
		$this->registerSubCommand(new CategoryCommand("category", "Opens category menu"));
		$this->registerSubCommand(new ListingsCommand("listings", "Shows player listings"));
		$this->registerSubCommand(new ExpiredCommand("expired", "Shows expired listings"));
		$this->registerSubCommand(new ReloadCommand("reload", "Reload plugin configuration files"));
		$this->registerSubCommand(new AboutCommand("about", "Plugin information"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if(!$sender instanceof Player){
            $sender->sendMessage("Usa este comando en el juego");
        }
        if($sender->hasPermission("auctionhouse.command.shop")){
            $this->AhFunction($sender);
        }else{
            $sender->sendMessage("§cNo tienes permisos para ejecutar este comando");
        }
	}
    
    public function AhFunction(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
           if($data !== null){
              switch($data){
                 case 0: // CASA DE SUBASTAS
                     AHMenu::open(new ShopMenu($player));
                 break;
                   
                 case 1: // ITEMS EXPIRADOS
                    AHMenu::open(new ExpiredMenu($player));
                 break;
                   
                 case 2: // SALIR
                 break;
               }
           }
        });
        $form->setTitle("       ");
        $form->setContent("§eSelecciona la opción que gustes:");
        $form->addButton("§l§aTIENDA DE SUBASTAS\n§r§7Haga clic",0,"textures/blocks/chest_front");
        $form->addButton("§l§eZONA CADUCADA\n§r§7Haga clic",0,"textures/items/potato_poisonous");
        $form->addButton("§l§4SALIR\n§r§7Haga clic",0,"textures/ui/cancel");
        $player->sendForm($form);
    }
}
