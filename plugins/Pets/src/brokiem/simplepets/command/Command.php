<?php

/*
 * Copyright (c) 2021 broki
 * brokiem/SimplePets is licensed under the MIT License
 */

declare(strict_types=1);

namespace brokiem\simplepets\command;

use brokiem\simplepets\database\Database;
use brokiem\simplepets\manager\PetManager;
use brokiem\simplepets\pets\base\BasePet;
use brokiem\simplepets\pets\base\CustomPet;
use brokiem\simplepets\SimplePets;

use EasyUI\element\Button;
use EasyUI\element\Dropdown;
use EasyUI\element\Input;
use EasyUI\element\Option;
use EasyUI\utils\FormResponse;
use EasyUI\variant\CustomForm;
use EasyUI\variant\SimpleForm;
use EasyUI\icon\ButtonIcon;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Command extends \pocketmine\command\Command implements PluginOwned {

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (isset($args[0])) {
            switch (strtolower($args[0])) {
                case "spawn":
                case "create":
                case "crear":
                    if (!($sender instanceof Player)) {
                        return;
                    }
                    if (!$sender->hasPermission("pets.acm")) {
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                        return;
                    }
                    $this->PetSpawn($sender);
                    break;
                case "remove":
                case "delete":
                case "eliminar":
                case "clear":
                case "borrar":
                    if(!$sender->hasPermission("pets.acm")){
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                        return;
                    }
                    $this->PetRemove($sender);
                    break;
                case "inventory":
                case "inv":
                case "inventario":
                    if (!$sender->hasPermission("pets.inv")){
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                        return;
                    }
                    
                    if (!SimplePets::getInstance()->getConfig()->get("enable-inventory")) {
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c ¡La función de inventario de mascotas está desactivada!");
                        return;
                    }
                    $this->PetInv($sender);
                break;
                case "ride":
                case "montar":
                    if (!SimplePets::getInstance()->getConfig()->get("enable-riding")) {
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c ¡La función de conducción está deshabilitada!");
                        return;
                    }

                    if (!$sender->hasPermission("pets.montar")) {
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                        return;
                    }
                    $this->PetRide($sender);
                    break;
            }
        } else {
            if (!($sender instanceof Player)) {
                        return;
                    }

                    if (!$sender->hasPermission("pets.acm")) {
                        $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                        return;
                    }
            $form = new SimpleForm("§l§aSurvi§bCraft");
            $form->addButton(new Button("§l§fCrear una mascota\n§r§0Haga clic", new ButtonIcon("textures/items/bucket_axolotl"), function(Player $sender){
                $this->PetSpawn($sender);
            }));
            $form->addButton(new Button("§l§fVer inventario\n§r§0Haga clic", new ButtonIcon("textures/ui/icon_best3"), function(Player $sender){
                if($sender->hasPermission("pets.inv")){
                    $this->PetInv($sender);
                }else{
                    $sender->sendMessage("§l§7[§bAM§7]§8»§r§c No tienes permisos para usar esto");
                }
            }));
            $form->addButton(new Button("§l§fMontarte en una mascota\n§r§0Haga clic", new ButtonIcon("textures/items/saddle"), function(Player $sender){
                if($sender->hasPermission("pets.montar")){
                    $this->PetRide($sender);
                }else{
                    $sender->sendMessage("§l§7§aSurvi§bCraft§7]§8»§r§c No tienes permisos para usar esto");
                }
            }));
            $form->addButton(new Button("§l§fEliminar mascota\n§r§0Haga clic", new ButtonIcon("textures/ui/book_trash_default"), function(Player $sender){
                $this->PetRemove($sender);
            }));
            $form->addButton(new Button("§l§4Cerrar menú\n§r§0Haga clic", new ButtonIcon("textures/ui/cancel"), function(Player $sender){
            }));
            $sender->sendForm($form);
        }
    }
    
    public function PetSpawn(Player $sender){
        $form = new SimpleForm("§l§aSurvi§bCraft");
                    foreach (PetManager::getInstance()->getRegisteredPets() as $type => $class) {
                        $form->addButton(new Button($type, new ButtonIcon("textures/items/egg_npc"), function(Player $player) use ($type) {
                            $dropdown = new Dropdown("Pet type");
                            $dropdown->addOption(new Option($type, $type));

                            $form = new CustomForm("Spawn $type");
                            $form->addElement("pet_type", $dropdown);
                            $form->addElement("pet_name", new Input("Pet name"));

                            $form->setSubmitListener(function(Player $player, FormResponse $response) {
                                $pet_name = $response->getInputSubmittedText("pet_name") ?? $player->getName();
                                $pet_type = $response->getDropdownSubmittedOptionId("pet_type");

                                if ($pet_type === null) {
                                    return;
                                }

                                if (!Player::isValidUserName($pet_name)) {
                                    $player->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c Nombre de mascota no válido. Solo se admite [A-z] con un máximo de 16 caracteres de longitud");
                                    return;
                                }

                                if (isset(PetManager::getInstance()->getActivePets()[$player->getName()])) {
                                    foreach (PetManager::getInstance()->getActivePets()[$player->getName()] as $petName => $petId) {
                                        $pet = $player->getServer()->getWorldManager()->findEntity($petId);

                                        if ($pet instanceof BasePet || $pet instanceof CustomPet) {
                                            $pet->despawn();
                                            $player->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c Tu mascota ha sido removida");
                                        }

                                        PetManager::getInstance()->removeActivePet($player, $petName);
                                        Database::getInstance()->removePet($player, $petName);
                                    }
                                }

                                PetManager::getInstance()->spawnPet($player, $pet_type, $pet_name);

                                $player->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§b " . str_replace("Pet", " Pet", $pet_type) . " §acon el nombre §b" . $pet_name . " §aha sido creado con éxito");
                            });

                            $player->sendForm($form);
                        }));
                    }
                    $sender->sendForm($form);
    }
    
    public function PetRemove(Player $sender){
        $form = new SimpleForm("§l§aSurvi§bCraft");
                    $form->addButton(new Button("§l§fEliminar mascota\n§r§0Haga clic", new ButtonIcon("textures/ui/book_trash_default"), function(Player $sender){
                        if(isset(PetManager::getInstance()->getActivePets()[$sender->getName()])) {
                                foreach(PetManager::getInstance()->getActivePets()[$sender->getName()] as $petName => $petId) {
                                     $pet = $sender->getServer()->getWorldManager()->findEntity($petId);
                                     if($pet instanceof BasePet || $pet instanceof CustomPet) {
                                            $pet->despawn();
                                            $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c Tu mascota ha sido removida");
                                     }

                                     PetManager::getInstance()->removeActivePet($sender, $petName);
                                     Database::getInstance()->removePet($sender, $petName);
                                    }
                                }
                    }));
                    $form->addButton(new Button("§l§4Cancelar\n§r§0Haga clic", new ButtonIcon("textures/ui/cancel"), function(Player $sender){
                    }));
                    $sender->sendForm($form);
    }
    
    public function PetInv(Player $sender){
        $form = new SimpleForm("§l§aSurvi§bCraft");
                    $form->addButton(new Button("§l§fVer inventario\n§r§0Haga clic", new ButtonIcon("textures/ui/icon_best3"), function(Player $sender){
                        if(isset(PetManager::getInstance()->getActivePets()[$sender->getName()])) {
                               foreach(PetManager::getInstance()->getActivePets()[$sender->getName()] as $petName => $petId) {
                                     $pet = $sender->getServer()->getWorldManager()->findEntity($petId);
                                     if($pet instanceof BasePet || $pet instanceof CustomPet) {
                                            $pet->getInventoryMenu()->send($sender, $pet->getName());
                                     }                      
                               }
                        }else{
                            $sender->sendMessage("No tienes ninguna mascota");
                        }
                    }));
                    $form->addButton(new Button("§l§4Cancelar\n§r§0Haga clic", new ButtonIcon("textures/ui/cancel"), function(Player $sender){
                    }));
                    $sender->sendForm($form);
    }
    
    public function PetRide(Player $sender){
        $form = new SimpleForm("§l§aSurvi§bCraft");
                    $form->addButton(new Button("§l§fMontarse\n§r§0Haga clic", new ButtonIcon("textures/items/saddle"), function(Player $sender){
                        if(isset(PetManager::getInstance()->getActivePets()[$sender->getName()])) {
                               foreach(PetManager::getInstance()->getActivePets()[$sender->getName()] as $petName => $petId) {
                                     $pet = $sender->getServer()->getWorldManager()->findEntity($petId);
                                     if($pet instanceof BasePet || $pet instanceof CustomPet) {
                                            if ($pet->isRidingEnabled()) {
                                                 $pet->link($sender);
                                            } else {
                                              $sender->sendMessage("§l§7[§aSurvi§bCraft§7]§8»§r§c Montar acceso a su nombre de mascota §4" . $pet->getName() . " §cestá deshabilitada");
                                            }
                                     }
                               }
                         }
                    }));
                    $form->addButton(new Button("§l§4Cancelar\n§r§0Haga clic", new ButtonIcon("textures/ui/cancel"), function(Player $sender){
                    }));
                    $sender->sendForm($form);
    }

    public function getOwningPlugin(): Plugin {
        return SimplePets::getInstance();
    }
}