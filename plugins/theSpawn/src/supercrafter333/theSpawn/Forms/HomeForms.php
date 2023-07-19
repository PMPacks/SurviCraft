<?php

namespace supercrafter333\theSpawn\Forms;

use supercrafter333\theSpawn\libs\jojoe77777\FormAPI\CustomForm;
use supercrafter333\theSpawn\libs\jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use supercrafter333\theSpawn\Commands\DelhomeCommand;
use supercrafter333\theSpawn\Commands\HomeCommand;
use supercrafter333\theSpawn\Commands\SethomeCommand;
use supercrafter333\theSpawn\MsgMgr;
use supercrafter333\theSpawn\theSpawn;

/**
 *
 */
class HomeForms
{

    /**
     * @param string $playerName
     */
    public function __construct(private string $playerName) {}

    /**
     * @param Player $player
     * @return SimpleForm
     */
    public function open(Player $player): ?SimpleForm
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) return;

            HomeCommand::simpleExecute($player, [$result]);
        });
        $form->setTitle(MsgMgr::getMsg("form-home-menu-title"));
        $form->setContent(MsgMgr::getMsg("form-home-menu-content"));
        foreach (theSpawn::getInstance()->getHomeCfg($this->playerName)->getAll() as $home => $homeN) {
            $homeName = $homeN["homeName"];
            $form->addButton(str_replace(["{home}", "{line}"], [$homeName, "\n"], MsgMgr::getMsg("form-home-menu-homeButton")), -1, "", $homeName);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    /**
     * @param Player $player
     * @return SimpleForm
     */
    public function openRmHome(Player $player): ?SimpleForm
    {
        $form = new SimpleForm(function (Player $player, $data = null) {
            $result = $data;
            if ($result === null) return;

            DelhomeCommand::simpleExecute($player, [$result]);
        });
        $form->setTitle(MsgMgr::getMsg("form-rmHome-menu-title"));
        $form->setContent(MsgMgr::getMsg("form-rmHome-menu-content"));
        foreach (theSpawn::getInstance()->getHomeCfg($this->playerName)->getAll() as $home => $homeN) {
            $homeName = $homeN["homeName"];
            $form->addButton(str_replace(["{home}", "{line}"], [$homeName, "\n"], MsgMgr::getMsg("form-rmHome-menu-homeButton")), -1, "", $homeName);
        }
        $form->sendToPlayer($player);
        return $form;
    }

    /**
     * @param Player $player
     * @return CustomForm
     */
    public function openSetHome(Player $player): ?CustomForm
    {
        $form = new CustomForm(function (Player $player, array $data = null) {
            if ($data === null) return;

            if (isset($data["homeName"])) {
                SethomeCommand::simpleExecute($player, [$data["homeName"]]);
                return;
            }
        });
        $form->setTitle(MsgMgr::getMsg("form-setHome-menu-title"));
        $form->addLabel(MsgMgr::getMsg("form-setHome-menu-content"));
        $form->addInput(MsgMgr::getMsg("form-setHome-menu-inputDescription"), "", null, "homeName");
        $form->sendToPlayer($player);
        return $form;
    }
}