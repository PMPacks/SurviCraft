<?php

namespace GreekHCF\backup;


use GreekHCF\Greek;
use GreekHCF\GreekUtils;
use GreekHCF\modules\PartnerPackage;
use pocketmine\utils\Config;

class ItemsBackup {

    public static function init() : void {
        $items = [];

        $data = new Config(Greek::getInstance()->getDataFolder()."backup".DIRECTORY_SEPARATOR."items.yml", Config::YAML);
        foreach($data->getAll() as $itemBackup){
            $result = $data->getAll();
            if(isset($result["items"])){
                foreach($result["items"] as $number => $item){
                    $items[$number] = GreekUtils::itemDeserialize($item);
                    Greek::$items = new PartnerPackage($items);
                }
            }
        }
    }

    public static function save() : void {
        $itemData = [];
        $result = new Config(Greek::getInstance()->getDataFolder()."backup".DIRECTORY_SEPARATOR."items.yml", Config::YAML);
            foreach(PartnerPackage::getItems() as $number => $item){
                $itemData[$number] = GreekUtils::itemSerialize($item);
            }
        $result->set("items", $itemData);
        $result->save();
    }

}

?>