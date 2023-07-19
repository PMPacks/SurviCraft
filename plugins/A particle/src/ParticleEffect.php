<?php

namespace xqwtxon\SlapperParticlesV2;

use pocketmine\scheduler\Task;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\particle\CriticalParticle;
use pocketmine\math\Vector3;
use snpc\entity\human_snpc;
use xqwtxon\SlapperParticlesV2\Main;

class ParticleEffect extends Task {
    
    private int $r = 0;

    public function __construct(private Main $plugin){
        //NOOP
    }
    
    public function onRun(): void{
        if($this->r < 0){
            $this->r++;
            return;
        }
        foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $world){
            $config = $this->plugin->getConfig();
            $colorA = $config->get("Color A");
            $colorB = $config->get("Color B");
            $colorC = $config->get("Color C");
            foreach($world->getEntities() as $entity){
                if($entity instanceof human_snpc){
                    $x = $entity->getLocation()->getX();
                    $y = $entity->getLocation()->getY();
                    $z = $entity->getLocation()->getZ();
                    $worlds = $entity->getWorld();
                    $hypo = 0.8;
                    $a = cos(deg2rad($this->r/0.09))* $hypo;
                    $b = sin(deg2rad($this->r/0.09))* $hypo;
                    $time = (int) (microtime(true) - $this->plugin->getServer()->getStartTime());
                    $seconds = floor($time % 20);
                    $up = $seconds/5;
                    $pos1 = new Vector3($x - $a, $y + $up, $z - $b);
                    $pos2 = new Vector3($x - $b, $y + $up, $z - $a);
                    $effect1 = new DustParticle(new \pocketmine\color\Color($colorA,$colorB,$colorC));
                    $effect2 = new DustParticle(new \pocketmine\color\Color($colorA,$colorB,$colorC));
                    $worlds->addParticle($pos1, $effect1);
                    $worlds->addParticle($pos2, $effect2);
                    $this->r++;
                }
            }
        }
    }
}
