<?php

namespace fernanACM\Accesorios;

use pocketmine\event\Listener;

use pocketmine\event\server\DataPacketReceiveEvent;

use Himbeer\LibSkin\SkinConverter;

use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\types\login\ClientData;
use pocketmine\network\PacketHandlingException;

use fernanACM\Accesorios\Loader;
use fernanACM\Accesorios\skin\SkinManager;
use pocketmine\event\player\PlayerJoinEvent;

class Event implements Listener{

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $core = Loader::getInstance();
        SkinConverter::skinDataToImageSave($player->getSkin()->getSkinData(), $core->getDataFolder() . "saveskin/{$player->getName()}.png");
    }

    public function onDataPacket(DataPacketReceiveEvent $event){
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($packet instanceof LoginPacket) {
            $data = self::decodeClientData($packet->clientDataJwt);
            $name = $data->ThirdPartyName;
            if ($data->PersonaSkin) {
                if (!file_exists(Loader::getInstance()->getDataFolder() . "saveskin")) {
                    mkdir(Loader::getInstance()->getDataFolder() . "saveskin", 0777);
                }
                copy(Loader::getInstance()->getDataFolder()."steve.png", Loader::getInstance()->getDataFolder() . "saveskin/{$name}.png");
                return;
            }
            if ($data->SkinImageHeight == 32) {
            }
            $saveSkin = new SkinManager();
            $saveSkin->saveSkin(base64_decode($data->SkinData, true), $name);
        }
    }

    /**
     * @param string $clientDataJwt
     */
    public static function decodeClientData(string $clientDataJwt): ClientData{
        try{
            [, $clientDataClaims, ] = JwtUtils::parse($clientDataJwt);
        }catch(JwtException $e){
            throw PacketHandlingException::wrap($e);
        }

        $mapper = new \JsonMapper;
        $mapper->bEnforceMapType = false;
        $mapper->bExceptionOnMissingData = true;
        $mapper->bExceptionOnUndefinedProperty = true;
        try{
            $clientData = $mapper->map($clientDataClaims, new ClientData);
        }catch(\JsonMapper_Exception $e){
            throw PacketHandlingException::wrap($e);
        }
        return $clientData;
    }
}