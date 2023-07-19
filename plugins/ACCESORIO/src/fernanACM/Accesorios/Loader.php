<?php

namespace fernanACM\Accesorios;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use Vecnavium\FormsUI\SimpleForm;

use fernanACM\Accesorios\Event;
use fernanACM\Accesorios\skin\SkinManager;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\permission\DefaultPermissions;

class Loader extends PluginBase{

    /** @var Loader $instance */
    public static Loader $instance;

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->saveSkinResource();
        Server::getInstance()->getPluginManager()->registerEvents(new Event($this), $this);
        $this->checkSkin();
    }

    public function saveSkinResource(){
        @mkdir($this->getDataFolder() . "skin");
        @mkdir($this->getDataFolder() . "saveskin");
        $this->saveResource("skin/Bruja.png");
        $this->saveResource("skin/Bruja.json");

        $this->saveResource("skin/Calavera.png");
        $this->saveResource("skin/Calavera.json");

        $this->saveResource("skin/Calabaza.png");
        $this->saveResource("skin/Calabaza.json");

        $this->saveResource("skin/Luffy.png");
        $this->saveResource("skin/Luffy.json");

        $this->saveResource("skin/Gorra.png");
        $this->saveResource("skin/Gorra.json");

        $this->saveResource("skin/AngelRed.png");
        $this->saveResource("skin/AngelRed.json");

        $this->saveResource("skin/AngelWhite.png");
        $this->saveResource("skin/AngelWhite.json");

        $this->saveResource("skin/AngleBlaze.png");
        $this->saveResource("skin/AngleBlaze.json");

        $this->saveResource("steve.png");
        $this->saveResource("steve.json");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch($command->getName()){
            case "ac":
                if($sender instanceof Player){
                    $this->getAccesorios($sender);
                }else{
                    $sender->sendMessage("Usa esto en el juegp");
                }
            break;
        }
        return true;
    }

    /**
     * @param Player $player
     * @param SimpleForm $form
     */
    public function getAccesorios(Player $player){
        $skin = new SkinManager();
        $form = new SimpleForm(function(Player $player, $data) use($skin){
            if($data === null){
                return;
            }
            switch($data){
                case 0: // BRUJA
                    if($player->hasPermission("bruja.sombrero") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "Bruja";
                        $skin->setSkin($player, "Bruja");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 1: // CALAVERA
                    if($player->hasPermission("calavera.sombrero") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "Calavera";
                        $skin->setSkin($player, "Calavera");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 2: // CALABAZA
                    if($player->hasPermission("calabaza.sombrero") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "Calabaza";
                        $skin->setSkin($player, "Calabaza");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 3:
                    if($player->hasPermission("luffy.sombrero") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "Luffy";
                        $skin->setSkin($player, "Luffy");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 4:
                    if($player->hasPermission("gorra.sombrero") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "Gorra";
                        $skin->setSkin($player, "Gorra");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 5:
                    if($player->hasPermission("angleblaze.alas") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "AngleBlaze";
                        $skin->setSkin($player, "AngleBlaze");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 6:
                    if($player->hasPermission("angelred.alas") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "AngelRed";
                        $skin->setSkin($player, "AngelRed");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 7:
                    if($player->hasPermission("angelwhite.alas") or $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
                        $type = "AngelWhite";
                        $skin->setSkin($player, "AngelWhite");
                        $player->sendMessage(self::Prefix(). "§aHas seleccionado el accesorio de §b{$type}§a.");
                    }else{
                        $player->sendMessage(self::Prefix() ."§c¡No tienes permisos para esto!");
                    }
                break;

                case 8:
                break;

                case 9: // RESET
                    $this->resetSkin($player);
                    $player->sendMessage(self::Prefix()."§aSe han reiniciado todos los accesorios");
                break;
            }
        });
        $form->setTitle("§l§cACCESORIOS");
    	$form->addButton("§l§9BRUJA\n§r§7Haga clic");
    	$form->addButton("§l§9CALAVERA\n§r§7Haga clic");
    	$form->addButton("§l§9CALABAZA\n§r§7Haga clic");
    	$form->addButton("§l§9LUFFY\n§r§7Haga clic");
    	$form->addButton("§l§9GORRA\n§r§7Haga clic");
    	$form->addButton("§l§9ANGEL BLAZE\n§r§7Haga clic");
    	$form->addButton("§l§9ANGEL RED\n§r§7Haga clic");
    	$form->addButton("§l§9ANGEL WHITE\n§r§7Haga clic");
    	$form->addButton("§l§bSalir\n§r§7Haga clic");
    	$form->addButton("§l§4Reiniciar skin\n§r§7Haga clic");
    	$player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public function resetSkin(Player $player){
        $reset = new SkinManager();
        $reset->resetSkin($player);
    }

    public function checkSkin(){
        $Available = [];
        if(!file_exists($this->getDataFolder() . "skin")){
          mkdir($this->getDataFolder() . "skin");
        }
        $path = $this->getDataFolder() . "skin/";
        $allskin = scandir($path);
        foreach($allskin as $file){
            array_push($Available, preg_replace("/.json/", "", $file));
        }
        foreach($Available as $value){
          if(!in_array($value . ".png", $allskin)){
            unset($Available[array_search($value, $Available)]);
          }
        }
        $this->json = count($Available);
        $Available = [];
    }

    public static function getInstance(): Loader{
        return self::$instance;
    }

    public static function Prefix(){
        return "§l§f[§bAL§f]§8»§r ";
    }
}