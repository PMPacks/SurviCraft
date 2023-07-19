<?php

namespace codes\provider;

use codes\Codes;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class ConfigProvider {
    use SingletonTrait;

    private array $data = [];

    public function start(): void {
        $config = new Config(Codes::getInstance()->getDataFolder() . "config.yml", Config::YAML);
        $this->setData("code-expired", $config->get("code-expired"));
        $this->setData("no-code-exist", $config->get("no-code-exist"));
        $this->setData("code-already-reclamed", $config->get("code-already-reclamed"));
        $this->setData("code-reclamed", $config->get("code-reclamed"));
        $this->setData("author-code-claimed", $config->get("author-code-claimed"));
        $this->setData("enter-a-code-to-claim", $config->get("enter-a-code-to-claim"));
    }

    public function setData(string $key, mixed $value): void {
        $this->data[$key] = $value;
    }

    public function getData(string $key): mixed {
        return $this->data[$key];
    }

}