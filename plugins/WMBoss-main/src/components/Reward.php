<?php

declare(strict_types=1);

namespace phuongaz\boss\components;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Reward {

    private array $items = [];
    private array $commands = [];

    public function __construct(
        private string $rewards,
    ){}

    public function parseRewards() :self {
        $rewards = json_decode($this->rewards, true);
        if(isset($rewards["items"])){
           foreach($rewards["items"] as $item){
              $itemExplode = explode(":", $item['item']);
              $itemObject = ItemFactory::getInstance()->get((int)$itemExplode[0], $itemExplode[1] ?? 0, $itemExplode[2] ?? 1);
              if(isset($item['name'])) {
                  $itemObject->setCustomName(TextFormat::colorize($item['name']));
              }
              if(isset($item['lore'])) {
                  $itemObject->setLore(array_map(function($lore){
                    return TextFormat::colorize($lore);
                  }, $item['lore']));
              }
              if(isset($item['enchantments'])) {
                  foreach($item['enchantments'] as $enchantmentString) {
                    $enchantExplode = explode(":", $enchantmentString);
                    $enchantId = $enchantExplode[0];
                    $enchantLevel = (int) $enchantExplode[1];
                    $enchantment = StringToEnchantmentParser::getInstance()->parse($enchantId);
                    $enchantInstance = new EnchantmentInstance($enchantment, $enchantLevel);
                    $itemObject->addEnchantment($enchantInstance);
                  }
              }
              $this->items[] = $itemObject;
           }
        }
        if(isset($rewards["commands"])){
            $this->commands = $rewards["commands"];
        }
        return $this;
    }

    public function reward(Player $player) :void {
            $rewardObj = $this->parseRewards();
            foreach($rewardObj->getItems() as $item){
                $player->getInventory()->addItem($item);
            }
            var_dump("Commands", $rewardObj->getCommands());
            foreach($rewardObj->getCommands() as $command){
                $server = Server::getInstance();
                $console = new ConsoleCommandSender($server, $server->getLanguage());
                $server->dispatchCommand($console, str_replace("{player}", $player->getName(), $command));
            }
    }

    private function getItems() :array {
        return $this->items;
    }

    private function getCommands() :array {
        return $this->commands;
    }

}