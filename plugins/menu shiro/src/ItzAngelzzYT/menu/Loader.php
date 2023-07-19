<?php

namespace ItzAngelzzYT\menu;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

    public function onEnable(): void {
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if($command->getName() === "menu") {
            if(!$sender instanceof Player) {
                $sender->sendMessage("This command can only be executed by a player.");
                return false;
            }
            $config = new Config($this->getDataFolder() . "menu.yml", Config::YAML);
            $items = $config->getNested("items");
            $title = $config->getNested("title", "Menu");
            $size = $config->getNested("size", 9);
            $menu = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function(Player $player, $data) use($items) {
                if($data === null) {
                    return;
                }
                $item = $items[$data];
                $player->getInventory()->addItem($item);
                $player->sendMessage("You received " . $item->getName());
                $player->getLevel()->addSound(new \pocketmine\level\sound\AnvilFallSound($player));
            });
            $menu->setTitle($title);
            foreach($items as $item) {
                $menu->addButton($item->getName());
            }
            $menu->setContent("Please select an item:");
            $menu->sendToPlayer($sender);
            return true;
        }
        return false;
    }
}
