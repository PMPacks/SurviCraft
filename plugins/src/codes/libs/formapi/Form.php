<?php

namespace codes\libs\formapi;

use pocketmine\form\Form as PMForm;
use pocketmine\player\Player;

abstract class Form implements PMForm {

    protected array $data = [];

    private $callable;

    public function __construct(?callable $callable) {
        $this->callable = $callable;
    }

    public function send(Player $player): void {
        $player->sendForm($this);
    }

    public function getCallable(): ?callable {
        return $this->callable;
    }

    public function setCallable(?callable $callable) {
        $this->callable = $callable;
    }

    public function handleResponse(Player $player, $data): void {
        $this->processData($data);
        $callable = $this->getCallable();
        if($callable !== null) {
            $callable($player, $data);
        }
    }

    public function processData(&$data): void {}

    public function jsonSerialize() {
        return $this->data;
    }
}