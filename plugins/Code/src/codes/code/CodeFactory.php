<?php

namespace codes\code;

use codes\Codes;
use JsonException;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class CodeFactory {
    use SingletonTrait;

    private array $codes = [];

    public function addCode(string $author, string $name, int $time, array $rewards = [], array $reclamed = []): void {
        $this->codes[$name] = new Code($author, $name, $time, $rewards, $reclamed);
    }

    public function getCode(string $name): ?Code {
        return $this->codes[$name] ?? null;
    }

    public function removeCode(string $name): void {
        unset($this->codes[$name]);
    }

    public function forceRemoveCode(string $name): void {
        $code = $this->getCode($name);
        if (!$code->getTask()->getHandler()->isCancelled()) {
            $code->getTask()->getHandler()->cancel();
        }
        $this->removeCode($name);
    }

    public function existCode(string $name): bool {
        return isset($this->codes[$name]);
    }

    public function start(): void {
        if (!file_exists(Codes::getInstance()->getDataFolder() . "codes.yml")) return;
        $config = new Config(Codes::getInstance()->getDataFolder() . "codes.yml", Config::YAML);
        foreach ($config->getAll() as $code => $codeData) {
            $this->addCode($codeData["author"], $code, $codeData["time"], $codeData["rewards"] ?? [], $codeData["reclamed"] ?? []);
        }
        unlink(Codes::getInstance()->getDataFolder() . "codes.yml");
    }

    /**
     * @throws JsonException
     */
    public function close(): void {
        $codes = $this->getCodes();
        if (empty($codes)) {
            return;
        }
        foreach ($codes as $codesData) {
            $config = new Config(Codes::getInstance()->getDataFolder() . "codes.yml", Config::YAML);
            $config->set($codesData->getName(), [
                "author" => $codesData->getAuthor(),
                "time" => $codesData->getTime(),
                "rewards" => $codesData->getRewards() ?? [],
                "reclamed" => $codesData->getReclamed() ?? []
            ]);
            $config->save();
        }
    }

    /**
     * @return Code[]|null
     */
    public function getCodes(): ?array {
        return $this->codes;
    }
}