<?php

namespace codes\sessions;

use codes\Codes;
use JsonException;
use pocketmine\utils\Config;

class Session {

    private string $name;

    private array $codes;

    public function __construct(string $name, array $codes) {
        $this->name = $name;
        $this->codes = $codes;
    }

    /**
     * @return array|null
     */
    public function getCodes(): ?array {
        return $this->codes;
    }

    public function addCode(string $code): void {
        $this->codes[] = $code;
    }

    public function hasCodes(): bool {
        return !empty($this->getCodes());
    }

    public function hasCode(string $code): bool {
        return in_array($code, $this->getCodes());
    }

    public function removeCode(string $name): void {
        unset($this->codes[array_search($name, $this->codes)]);
    }

    public function getName(): string {
        return $this->name;
    }

    /**
     * @throws JsonException
     */
    public function save(): void {
        $config = new Config(Codes::getInstance()->getDataFolder() . "sessions/" . $this->getName() . ".yml", Config::YAML);
        if ($config->get("codes") !== $this->getCodes()) {
            $config->set("codes", $this->getCodes());
            $config->save();
        }
    }
}