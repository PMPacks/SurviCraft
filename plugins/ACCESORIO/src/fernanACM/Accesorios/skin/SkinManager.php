<?php

namespace fernanACM\Accesorios\skin;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\entity\Skin;

use fernanACM\Accesorios\Loader;

class SkinManager{

    # =======(SET SKIN)=======
    /**
     * @param Player $player
     * @param string $skinName
     */
    public function setSkin(Player $player, string $skinName){
        $locate = "skin";
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = Loader::getInstance()->getDataFolder() . "saveskin/" . $name . ".png";

        $size = getimagesize($path);

        $path = $this->imgTricky($path, $skinName, $locate, [$size[0], $size[1], 4]);
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry." . $skinName, file_get_contents(Loader::getInstance()->getDataFolder() . $locate . "/" . $skinName . ".json")));
        $player->sendSkin();
    }

    /**
     * @param string $skinPath
     * @param string $skinName
     * @param string $local
     * @param array $size
     */
    public function imgTricky(string $skinPath, string $skinName, string $locate, array $size){
        $path = Loader::getInstance()->getDataFolder();
        $down = imagecreatefrompng($skinPath);
        $upper = null;
        if ($size[0] * $size[1] * $size[2] == 65536) {
            $upper = $this->resize_image($path . $locate . "/" . $skinName . ".png", 128, 128);
        } else {
            $upper = $this->resize_image($path . $locate . "/" . $skinName . ".png", 64, 64);
        }
        //Remove black color out of the png
        imagecolortransparent($upper, imagecolorallocatealpha($upper, 0, 0, 0, 127));

        imagealphablending($down, true);
        imagesavealpha($down, true);
        imagecopymerge($down, $upper, 0, 0, 0, 0, $size[0], $size[1], 100);
        imagepng($down, $path . 'temp.png');
        return Loader::getInstance()->getDataFolder() . 'temp.png';
    }

    public function resize_image($file, $w, $h, $crop = FALSE){
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if($crop){
            if($width > $height){
                $width = ceil($width - ($width * abs($r - $w / $h)));
            }else{
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if($w / $h > $r){
                $newwidth = $h * $r;
                $newheight = $h;
            }else{
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($w, $h);
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $dst;
    }

    # =======(SAVE SKIN)=======

    public $skin_widght_map = [
        64 * 32 * 4 => 64,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 256

    ];

    public $skin_height_map = [
        64 * 32 * 4 => 32,
        64 * 64 * 4 => 64,
        128 * 128 * 4 => 128,
        128 * 256 * 4 => 128
    ];

    /**
     * @param string $skin
     * @param string $name
     */
    public function saveSkin(string $skin, string $name){
        $path = Loader::getInstance()->getDataFolder();
        if(!file_exists($path . "saveskin")) {
            mkdir($path . "saveskin", 0777);
        }
        $img = $this->skinDataToImage($skin);
        if($img == null) {
            return;
        }
        imagepng($img, $path . "saveskin/" . $name . ".png");
    }

    /**
     * @param string $skinData
     */
    public function skinDataToImage(string $skinData){
        $size = strlen($skinData);

        $width = $this->skin_widght_map[$size];
        $height = $this->skin_height_map[$size];
        $skinPos = 0;
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            Server::getInstance()->broadcastMessage("An error occur on CustomWing plugin,id: 2");
            return null;
        }
        // Make background transparent
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $r = ord($skinData[$skinPos]);
                $skinPos++;
                $g = ord($skinData[$skinPos]);
                $skinPos++;
                $b = ord($skinData[$skinPos]);
                $skinPos++;
                $a = 127 - intdiv(ord($skinData[$skinPos]), 2);
                $skinPos++;
                $col = imagecolorallocatealpha($image, $r, $g, $b, $a);
                imagesetpixel($image, $x, $y, $col);
            }
        }
        imagesavealpha($image, true);
        return $image;
    }

    # =======(RESET SKIN)=======
    /**
     * @param Player $player
     */
    public function resetSkin(Player $player){
        $skin = $player->getSkin();
        $name = $player->getName();
        $path = Loader::getInstance()->getDataFolder() . "saveskin/" . $name . ".png";

        $img = @imagecreatefrompng($path);
        $size = getimagesize($path);
        $skinbytes = "";
        for ($y = 0; $y < $size[1]; $y++) {
            for ($x = 0; $x < $size[0]; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.humanoid.custom", file_get_contents(Loader::getInstance()->getDataFolder() . "steve.json")));
        $player->sendSkin();
    }
}