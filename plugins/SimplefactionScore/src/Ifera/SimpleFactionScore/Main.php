<?php
declare(strict_types=1);

namespace Ifera\SimpleFactionScore;

use Ifera\SimpleFactionScore\listeners\TagResolveListener;
use Ayzrix\SimpleFaction\API\FactionsAPI;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\ScoreHudSettings;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\player\Player;
use function strval;

class Main extends PluginBase{

	private $owningPlugin;

	protected function onEnable(): void{
		$this->saveDefaultConfig();
		$this->owningPlugin = $this->getServer()->getPluginManager()->getPlugin("SimpleFaction");
		$this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void{
            foreach($this->getServer()->getOnlinePlayers() as $player){
                if(!$player->isOnline()){
                    continue;
                }

                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefactionhud.faction", strval($this->getFaction($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefactionhud.power", strval($this->getPower($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefactionhud.money", strval($this->getMoney($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefactionhud.rank", strval($this->getRank($player)))))->call();
            }
        }), 20);
    }

    /**
     * @param Player $player
     * @return string
     */
    private function getFaction(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getFaction($player->getName());
        } else return "Sin Fac";
    }

    /**
     * @param Player $player
     * @return int
     */
    private function getPower(Player $player): int {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getPower(self::getFaction($player));
        } else return 0;
    }

    /**
     * @param Player $player
     * @return int
     */
    private function getMoney(Player $player): int {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getMoney(self::getFaction($player));
        } else return 0;
    }

    /**
     * @param Player $player
     * @return string
     */
    private function getRank(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getRank($player->getName());
        } else return "Sin Rank";
    }
}
