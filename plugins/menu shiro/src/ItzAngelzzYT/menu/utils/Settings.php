<?php

namespace ItzAngelzzYT\menu\utils;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use ItzAngelzzYT\menu\Loader;

final class Settings{
    use SingletonTrait;
    private static array $config;

    public function init(): void{
        self::$config = yaml_parse(Config::fixYAMLIndexes(file_get_contents(Loader::getInstance()->getDataFolder() . "config.yml")));
    }

    /**
     * @return array
     */
    public static function getConfig(): array{
        return self::$config;
    }

    public function getCommand(): array{
        return self::$config['command'] ?? [];
    }

    public function getForm(): array{
        return self::$config['form'] ?? [];
    }

    public function getButtons(): array{
        return $this->getForm()['buttons'];
    }

    public function getCommandName(): string{
        return $this->getCommand()['name'] ?? "menu";
    }

    public function getCommandDescription(): string{
        return $this->getCommand()['description'] ?? "Set description in config";
    }

    public function getCommandUsage(): string{
        return $this->getCommand()['usage'] ?? "Set Usage in config";
    }

    public function getCommandAliases(): array{
        return $this->getCommand()['aliases'] ?? [];
    }

    public function getCommandByButton(string $button): ?string{
        return $this->getButtons()[$button]['command'] ?? null;
    }

}