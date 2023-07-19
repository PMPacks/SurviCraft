<?php
    
#      _       ____   __  __ 
#     / \     / ___| |  \/  |
#    / _ \   | |     | |\/| |
#   / ___ \  | |___  | |  | |
#  /_/   \_\  \____| |_|  |_|
# The creator of this plugin was fernanACM.
# https://github.com/fernanACM

namespace fernanACM\Particulas;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\scheduler\Task as TaskEvent;

use pocketmine\world\particle\FlameParticle;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\particle\HeartParticle;
use pocketmine\world\particle\LavaParticle;
use pocketmine\world\particle\RedstoneParticle;

use fernanACM\Particulas\Loader;
use pocketmine\math\Vector3;
use pocketmine\world\particle\DustParticle;

class Task extends TaskEvent{

    public function onRun(): void{
        foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
            foreach($world->getEntities() as $player){
                if($player instanceof Player){
                    $worldName = $player->getWorld();
                    // FLAME
                    if(in_array($player->getName(), Loader::getInstance()->flame)){
                        $particle = new FlameParticle();
                        $slice = 2 * M_PI / 16;
                        $radius = 0.75;
                        $playerOffset = 2.5;
                        for($i = 0; $i < 16; $i++){
                            $angle = $slice * $i;
                            $dx = $radius * cos($angle);
                            $dy = $playerOffset;
                            $dz = $radius * sin($angle);
                            $worldName->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
                        }
                    }
                    // HEARTH
                    if(in_array($player->getName(), Loader::getInstance()->hearth)){
                        $particle = new HeartParticle();
                        $slice = 2 * M_PI / 16;
                        $radius = 0.75;
                        $playerOffset = 2.5;
                        for($i = 0; $i < 16; $i++){
                            $angle = $slice * $i;
                            $dx = $radius * cos($angle);
                            $dy = $playerOffset;
                            $dz = $radius * sin($angle);
                            $worldName->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
                        }
                    }
                    // ANGRY
                    if(in_array($player->getName(), Loader::getInstance()->angry)){
                        $particle = new AngryVillagerParticle();
                        $slice = 2 * M_PI / 16;
                        $radius = 0.75;
                        $playerOffset = 2.5;
                        for($i = 0; $i < 16; $i++){
                            $angle = $slice * $i;
                            $dx = $radius * cos($angle);
                            $dy = $playerOffset;
                            $dz = $radius * sin($angle);
                            $worldName->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
                        }
                    }
                    // LAVA
                    if(in_array($player->getName(), Loader::getInstance()->lava)){
                        $particle = new LavaParticle();
                        $slice = 2 * M_PI / 16;
                        $radius = 0.75;
                        $playerOffset = 2.5;
                        for($i = 0; $i < 16; $i++){
                            $angle = $slice * $i;
                            $dx = $radius * cos($angle);
                            $dy = $playerOffset;
                            $dz = $radius * sin($angle);
                            $worldName->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
                        }
                    }
                    // REDSTONE
                    if(in_array($player->getName(), Loader::getInstance()->redstone)){
                        $particle = new RedstoneParticle();
                        $slice = 2 * M_PI / 16;
                        $radius = 0.75;
                        $playerOffset = 2.5;
                        for($i = 0; $i < 16; $i++){
                            $angle = $slice * $i;
                            $dx = $radius * cos($angle);
                            $dy = $playerOffset;
                            $dz = $radius * sin($angle);
                            $worldName->addParticle($player->getLocation()->add($dx, $dy, $dz), $particle);
                        }
                    }
                    # TRAILS
                    if(in_array($player->getName(), Loader::getInstance()->red)){
                        $x = $player->getLocation()->getX() + -0.1;
                        $y = $player->getLocation()->getY() + 0.2;
                        $z = $player->getLocation()->getZ() + -0.1;
                        $center = new Vector3($x, $y, $z, $worldName);
                        $vector = new Vector3($x, $y, $z, $worldName);
                        $particle = new DustParticle(new \pocketmine\color\Color(255,0,0));
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                    }
                    if(in_array($player->getName(), Loader::getInstance()->blue)){
                        $x = $player->getLocation()->getX() + -0.1;
                        $y = $player->getLocation()->getY() + 0.2;
                        $z = $player->getLocation()->getZ() + -0.1;
                        $center = new Vector3($x, $y, $z, $worldName);
                        $vector = new Vector3($x, $y, $z, $worldName);
                        $particle = new DustParticle(new \pocketmine\color\Color(0,124,255));
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                    }
                    if(in_array($player->getName(), Loader::getInstance()->yellow)){
                        $x = $player->getLocation()->getX() + -0.1;
                        $y = $player->getLocation()->getY() + 0.2;
                        $z = $player->getLocation()->getZ() + -0.1;
                        $center = new Vector3($x, $y, $z, $worldName);
                        $vector = new Vector3($x, $y, $z, $worldName);
                        $particle = new DustParticle(new \pocketmine\color\Color(251,255,0));
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                    }
                    if(in_array($player->getName(), Loader::getInstance()->green)){
                        $x = $player->getLocation()->getX() + -0.1;
                        $y = $player->getLocation()->getY() + 0.2;
                        $z = $player->getLocation()->getZ() + -0.1;
                        $center = new Vector3($x, $y, $z, $worldName);
                        $vector = new Vector3($x, $y, $z, $worldName);
                        $particle = new DustParticle(new \pocketmine\color\Color(2,173,36));
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                    }
                    if(in_array($player->getName(), loader::getInstance()->cyan)){
                        $x = $player->getLocation()->getX() + -0.1;
                        $y = $player->getLocation()->getY() + 0.2;
                        $z = $player->getLocation()->getZ() + -0.1;
                        $center = new Vector3($x, $y, $z, $worldName);
                        $vector = new Vector3($x, $y, $z, $worldName);
                        $particle = new DustParticle(new \pocketmine\color\Color(15,201,142));
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                        $worldName->addParticle($vector, $particle);
                        $worldName->addParticle($center, $particle);
                    }
                }
            }
        }
    }
}