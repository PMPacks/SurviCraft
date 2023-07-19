<?php

namespace codes\code;

use codes\Codes;
use codes\scheduler\CodesTimeScheduler;
use pocketmine\scheduler\Task;

class Code {

    private string $author;

    private string $name;

    private int $time;

    private ?array $rewards;

    private ?array $reclamed;

    private Task $task;

    public function __construct(string $author, string $name, int $time, array $rewards = [], array $reclamed = []) {
        $this->author = $author;
        $this->name = $name;
        $this->time = $time;
        $this->rewards = $rewards;
        $this->reclamed = $reclamed;
        $this->task = Codes::getInstance()->getScheduler()->scheduleRepeatingTask(new CodesTimeScheduler($this), 20)->getTask();
    }

    public function getTask(): Task {
        return $this->task;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getReclamed(): ?array {
        return $this->reclamed;
    }

    public function canClaim(string $player): bool {
        return !in_array($player, $this->getReclamed());
    }

    public function hasReclamed(string $reclamed): bool {
        return in_array($reclamed, $this->getReclamed());
    }

    public function addReclamed(string $reclamed): void {
        $this->reclamed[] = $reclamed;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function setTime(int $time): void {
        $this->time = $time;
    }

    public function getRewards(): ?array {
        return $this->rewards;
    }
}