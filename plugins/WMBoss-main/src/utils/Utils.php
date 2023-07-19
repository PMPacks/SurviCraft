<?php

declare(strict_types=1);

namespace phuongaz\boss\utils;

use JsonException;
use phuongaz\boss\Boss;
use phuongaz\boss\components\Reward;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Utils {

    /**
     * @throws JsonException
     */
    public static function generateSkinFromPath(string $file): Skin {
        $path = Boss::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . $file;
        $img = @imagecreatefrompng($path . ".png");
        $bytes = '';
        $l = (int)@getimagesize($path . ".png")[1];

        for ($y = 0; $y < $l; $y++) {
            for ($x = 0; $x < 64; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);
        $geometryPath = $path . ".json";
        if(!file_exists($geometryPath)) {
            $geometryPath = Boss::getInstance()->getDataFolder() . "skins " . DIRECTORY_SEPARATOR ."geometry.json";
        }
        return new Skin("Standard_CustomSlim", $bytes, "", "geometry.".$file, file_get_contents($geometryPath));
    }

    public static function sendToast(Player $player, string $title, string $body) :void {
        $packet = ToastRequestPacket::create(TextFormat::colorize($title), TextFormat::colorize($body));
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public static function getRewardByBoss(string $bossId) :?Reward {
        $reward = Boss::getInstance()->getConfig()->get($bossId);
        if (isset($reward['reward'])) {
            return new Reward(json_encode($reward['reward']));
        }
        return null;
    }

    public static function getListBoss() :\Generator{
        $list = Boss::getInstance()->getConfig()->getAll();
        foreach ($list as $key => $value) {
            yield $key => $value['props'];
        }
    }

    public static function createSimpleConfig(string $bossId, Position $spawn, array $props) :void{
        $config = Boss::getInstance()->getConfig();
        $config->set($bossId, [
            'spawn' => json_encode([
                'x' => $spawn->getX(),
                'y' => $spawn->getY(),
                'z' => $spawn->getZ(),
                'world' => $spawn->getWorld()->getFolderName()
            ]),
            'props' => json_encode($props),
            'reward' => [
                'items' => [],
                'commands' => [],
                'money' => 0
            ]
        ]);
        $config->save();
    }

    public static function arrayToLocation(array $array) :Location {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($array['world']);
        return new Location($array['x'], $array['y'], $array['z'], $world, $array['yaw'] ?? 0.0, $array['pitch'] ?? 0.0);
    }
}