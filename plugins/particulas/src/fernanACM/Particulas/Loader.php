<?php
    
#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

namespace fernanACM\Particulas;

use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use Vecnavium\FormsUI\SimpleForm;

use fernanACM\Particulas\Task;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class Loader extends PluginBase{
    # Trails
    public $red = [];
    public $blue = [];
    public $yellow = [];
    public $green = [];
    public $cyan = [];
    # particulas
    public $flame = [];
    public $hearth = [];
    public $angry = [];
    public $lava = [];
    public $redstone = [];

    public static $instance;

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->getScheduler()->scheduleRepeatingTask(new Task($this), 5);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch($command->getName()){
            case "particulas":
                if($sender instanceof Player){
                    $this->Particles($sender);
                }else{
                    $sender->sendMessage("USA ESTO EN EL JUEGO");
                }
            break;

            case "trails":
                if($sender instanceof Player){
                    $this->Trails($sender);
                }else{
                    $sender->sendMessage("USA ESTO EN EL JUEGO");
                }
            break;
        }
        return true;
    }

    public function Particles(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
            if($data !== null){
                switch($data){
                    case 0: // FLAMAS
                        $type = "FLAMAS";
                        if(!$player->hasPermission("particulas.flame")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->flame)){
                            $this->flame[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la particula de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->hearth)){
                                unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                            }elseif(in_array($player->getName(), $this->angry)){
                                unset($this->angry[array_search($player->getName(), $this->angry)]);
                            }elseif(in_array($player->getName(), $this->lava)){
                                unset($this->lava[array_search($player->getName(), $this->lava)]);
                            }elseif(in_array($player->getName(), $this->redstone)){
                                unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                            }
                        }else{
                            unset($this->flame[array_search($player->getName(), $this->flame)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa particula §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 1: // CORAZON
                        $type = "CORAZÓN";
                        if(!$player->hasPermission("particulas.hearth")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->hearth)){
                            $this->hearth[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la particula de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->flame)){
                                unset($this->flame[array_search($player->getName(), $this->flame)]);
                            }elseif(in_array($player->getName(), $this->angry)){
                                unset($this->angry[array_search($player->getName(), $this->angry)]);
                            }elseif(in_array($player->getName(), $this->lava)){
                                unset($this->lava[array_search($player->getName(), $this->lava)]);
                            }elseif(in_array($player->getName(), $this->redstone)){
                                unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                            }
                        }else{
                            unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa particula §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 2: // ENOJADO
                        $type = "ENOJADO";
                        if(!$player->hasPermission("particulas.angry")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->angry)){
                            $this->angry[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la particula de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->flame)){
                                unset($this->flame[array_search($player->getName(), $this->flame)]);
                            }elseif(in_array($player->getName(), $this->hearth)){
                                unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                            }elseif(in_array($player->getName(), $this->lava)){
                                unset($this->lava[array_search($player->getName(), $this->lava)]);
                            }elseif(in_array($player->getName(), $this->redstone)){
                                unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                            }
                        }else{
                            unset($this->angry[array_search($player->getName(), $this->angry)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa particula §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 3: // LAVA
                        $type = "LAVA";
                        if(!$player->hasPermission("particulas.lava")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->lava)){
                            $this->lava[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la particula de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->flame)){
                                unset($this->flame[array_search($player->getName(), $this->flame)]);
                            }elseif(in_array($player->getName(), $this->hearth)){
                                unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                            }elseif(in_array($player->getName(), $this->angry)){
                                unset($this->angry[array_search($player->getName(), $this->angry)]);
                            }elseif(in_array($player->getName(), $this->redstone)){
                                unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                            }
                        }else{
                            unset($this->lava[array_search($player->getName(), $this->lava)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa particula §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 4: // REDSTONE
                        $type = "REDSTONE";
                        if(!$player->hasPermission("particulas.redstone")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->redstone)){
                            $this->redstone[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la particula de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->flame)){
                                unset($this->flame[array_search($player->getName(), $this->flame)]);
                            }elseif(in_array($player->getName(), $this->hearth)){
                                unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                            }elseif(in_array($player->getName(), $this->angry)){
                                unset($this->angry[array_search($player->getName(), $this->angry)]);
                            }elseif(in_array($player->getName(), $this->lava)){
                                unset($this->lava[array_search($player->getName(), $this->lava)]);
                            }
                        }else{
                            unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa particula §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 5: // BORRAR
                        $player->sendMessage(Loader::Prefix() . "§cTodas las particulas han sido removidas.");
                        if(in_array($player->getName(), $this->flame)){
                            unset($this->flame[array_search($player->getName(), $this->flame)]);
                        }elseif(in_array($player->getName(), $this->hearth)){
                            unset($this->hearth[array_search($player->getName(), $this->hearth)]);
                        }elseif(in_array($player->getName(), $this->angry)){
                            unset($this->angry[array_search($player->getName(), $this->angry)]);
                        }elseif(in_array($player->getName(), $this->redstone)){
                            unset($this->redstone[array_search($player->getName(), $this->redstone)]);
                        }elseif(in_array($player->getName(), $this->lava)){
                            unset($this->lava[array_search($player->getName(), $this->lava)]);
                        }
                    break;

                }
            }
        });
        $form->setTitle("§l§cPARTICULAS");
        $form->setContent("§eSelecciona el tipo de particula que deseas:");
        $form->addButton("§l§2FLAMAS\n§r§7Haga clic",0,"textures/ui/icon_trending");
        $form->addButton("§l§2CORAZON\n§r§7Haga clic",0,"textures/ui/icon_staffpicks");
        $form->addButton("§l§2ENOJADO\n§r§7Haga clic",0,"textures/ui/bad_omen_effect");
        $form->addButton("§l§2LAVA\n§r§7Haga clic",0,"textures/items/bucket_lava");
        $form->addButton("§l§2REDSTONE\n§r§7Haga clic",0,"textures/items/redstone_dust");
        $form->addButton("§l§4REMOVER\n§r§7Borrar trails",0,"textures/ui/trash");
        $form->addButton("§l§aSALIR\n§r§7Cerrar menú",0,"textures/ui/cancel");
        $player->sendForm($form);
    }

    public function Trails(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
            if($data !== null){
                switch($data){
                    case 0:
                        $type = "ROJO";
                        if(!$player->hasPermission("particulas.rojo")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->red)){
                            $this->red[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la trait de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->blue)){
                                unset($this->blue[array_search($player->getName(), $this->blue)]);
                            }elseif(in_array($player->getName(), $this->yellow)){
                                unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                            }elseif(in_array($player->getName(), $this->green)){
                                unset($this->green[array_search($player->getName(), $this->green)]);
                            }elseif(in_array($player->getName(), $this->cyan)){
                                unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                            }
                        }else{
                            unset($this->red[array_search($player->getName(), $this->red)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa trait §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 1:
                        $type = "AZUL";
                        if(!$player->hasPermission("particulas.azul")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->blue)){
                            $this->blue[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la trait de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->red)){
                                unset($this->red[array_search($player->getName(), $this->red)]);
                            }elseif(in_array($player->getName(), $this->yellow)){
                                unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                            }elseif(in_array($player->getName(), $this->green)){
                                unset($this->green[array_search($player->getName(), $this->green)]);
                            }elseif(in_array($player->getName(), $this->cyan)){
                                unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                            }
                        }else{
                            unset($this->blue[array_search($player->getName(), $this->blue)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa trait §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 2:
                        $type = "AMARILLO";
                        if(!$player->hasPermission("particulas.amarillo")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->yellow)){
                            $this->yellow[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la trait de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->red)){
                                unset($this->red[array_search($player->getName(), $this->red)]);
                            }elseif(in_array($player->getName(), $this->blue)){
                                unset($this->blue[array_search($player->getName(), $this->blue)]);
                            }elseif(in_array($player->getName(), $this->green)){
                                unset($this->green[array_search($player->getName(), $this->green)]);
                            }elseif(in_array($player->getName(), $this->cyan)){
                                unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                            }
                        }else{
                            unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa trait §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 3:
                        $type = "VERDE";
                        if(!$player->hasPermission("particulas.verde")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->green)){
                            $this->green[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la trait de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->red)){
                                unset($this->red[array_search($player->getName(), $this->red)]);
                            }elseif(in_array($player->getName(), $this->blue)){
                                unset($this->blue[array_search($player->getName(), $this->blue)]);
                            }elseif(in_array($player->getName(), $this->yellow)){
                                unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                            }elseif(in_array($player->getName(), $this->cyan)){
                                unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                            }
                        }else{
                            unset($this->green[array_search($player->getName(), $this->green)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa trait §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 4:
                        $type = "CIAN";
                        if(!$player->hasPermission("particulas.cian")){
                            $player->sendMessage(Loader::Prefix() . "§c¡Tu no tienes permisos para usar esto!");
                            return;
                        }
                        if(!in_array($player->getName(), $this->cyan)){
                            $this->cyan[] = $player->getName();
                            $player->sendMessage(Loader::Prefix() . "§aHas seleccionado la trait de §6{$type}S§a.");
                            if(in_array($player->getName(), $this->red)){
                                unset($this->red[array_search($player->getName(), $this->red)]);
                            }elseif(in_array($player->getName(), $this->blue)){
                                unset($this->blue[array_search($player->getName(), $this->blue)]);
                            }elseif(in_array($player->getName(), $this->yellow)){
                                unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                            }elseif(in_array($player->getName(), $this->green)){
                                unset($this->green[array_search($player->getName(), $this->green)]);
                            }
                        }else{
                            unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                            $player->sendMessage(Loader::Prefix() . "§cLa trait §6{$type}§c, ha sido desactivada.");
                        }
                    break;

                    case 5:
                        $player->sendMessage(Loader::Prefix() . "§cTodas las trails han sido removidas.");
                        if(in_array($player->getName(), $this->red)){
                            unset($this->red[array_search($player->getName(), $this->red)]);
                        }elseif(in_array($player->getName(), $this->blue)){
                            unset($this->blue[array_search($player->getName(), $this->blue)]);
                        }elseif(in_array($player->getName(), $this->yellow)){
                            unset($this->yellow[array_search($player->getName(), $this->yellow)]);
                        }elseif(in_array($player->getName(), $this->green)){
                            unset($this->green[array_search($player->getName(), $this->green)]);
                        }elseif(in_array($player->getName(), $this->cyan)){
                            unset($this->cyan[array_search($player->getName(), $this->cyan)]);
                        }
                    break;
                }
            }
        });
        $form->setTitle("§l§cTRAILS");
        $form->setContent("§eSelecciona el tipo de trail que deseas:");
        $form->addButton("§l§2ROJO\n§r§7Haga clic",0,"textures/items/dye_powder_red");
        $form->addButton("§l§2AZUL\n§r§7Haga clic",0,"textures/items/dye_powder_light_blue");
        $form->addButton("§l§2AMARILLO\n§r§7Haga clic",0,"textures/items/dye_powder_yellow");
        $form->addButton("§l§2VERDE\n§r§7Haga clic",0,"textures/items/dye_powder_green");
        $form->addButton("§l§2CIAN\n§r§7Haga clic",0,"textures/items/dye_powder_cyan");
        $form->addButton("§l§4REMOVER\n§r§7Borrar particulas",0,"textures/ui/trash");
        $form->addButton("§l§cSALIR\n§r§7Cerrar menú",0,"textures/ui/cancel");
        $player->sendForm($form);
    }

    public static function getInstance(): Loader{
        return self::$instance;
    }

    public static function Prefix(){
        return "§l§f[§bAL§f]§8»§r ";
    }
}