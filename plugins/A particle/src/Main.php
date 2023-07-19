<?php

namespace xqwtxon\SlapperParticlesV2;

use pocketmine\plugin\PluginBase;
use xqwtxon\SlapperParticlesV2\ParticleEffect;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\VersionInfo;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {
    public function onLoad() :void{
        $this->saveResource("config.yml");
        $log = $this->getLogger();
        $config = $this->getConfig();
        if ($config->get("config-version") == "1.0.1"){
            @rename($this->getDataFolder()."/"."config.yml", $this->getDataFolder()."/"."old-config.yml");
            $log->notice("Your configuration is outdated! The configuration was renamed as old-config.yml");
            $this->saveResource("config.yml");
        } else {
            $this->saveDefaultConfig();
        }
    }
    public function onEnable(): void{
        $config = $this->getConfig();
        $log = $this->getLogger();
        $colorA = $config->get("Color A");
        $colorB = $config->get("Color B");
        $colorC = $config->get("Color C");
        if (!isset($colorA)) {
            $log->error("Color A is blank!");
            $log->notice("Color A modified to 255 as default.");
            $this->getConfig()->set("Color A", 255);
        }
        if (!isset($colorB)) {
            $log->error("Color B is blank!");
            $log->notice("Color B modified to 255 as default.");
            $this->getConfig()->set("Color B", 255);
        }
        if (!isset($colorC)) {
            $log->error("Color C is blank!");
            $log->notice("Color C modified to 255 as default.");
            $this->getConfig()->set("Color C", 255);
        }
		$this->saveDefaultConfig(); 
                $this->getScheduler()->scheduleRepeatingTask(new ParticleEffect($this), 2); 
    }
}
