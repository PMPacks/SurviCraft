<?php

namespace codes\sessions;

use codes\Codes;
use JsonException;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class SessionFactory {
    use SingletonTrait;

    private array $sessions = [];

    public function start(): void {
        if (!is_dir(Codes::getInstance()->getDataFolder() . "sessions")) @mkdir(Codes::getInstance()->getDataFolder() . "sessions");
    }

    /**
     * @throws JsonException
     */
    public function create(string $name): void {
        $config = new Config(Codes::getInstance()->getDataFolder() . "sessions/" . $name . ".yml", Config::YAML);
        $config->setAll([
            "name" => $name,
            "codes" => []
        ]);
        $config->save();
        $this->add(new Session($name, []));
    }

    public function load(string $name): void {
        $config = new Config(Codes::getInstance()->getDataFolder() . "sessions/" . $name . ".yml", Config::YAML);
        $this->add(new Session($name, $config->get("codes")));
    }

    public function get(string $name): ?Session {
        return $this->sessions[$name] ?? null;
    }

    public function remove(string $name): void {
        unset($this->sessions[$name]);
    }

    public function add(Session $session): void {
        $this->sessions[$session->getName()] = $session;
    }

    public function exist(string $name): bool {
        return isset($this->sessions[$name]);
    }

    public function existFile(string $name): bool {
        return file_exists(Codes::getInstance()->getDataFolder() . "sessions/" . $name . ".yml");
    }

    /**
     * @return Session[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }
}