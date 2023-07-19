<?php

namespace Electro\WordScrambler;

use pocketmine\scheduler\Task;

class ScrambleTask extends Task{

    public function __construct(WordScrambler $plugin){
        $this->plugin = $plugin;
    }

    public function onRun() : void
    {
        $this->plugin->scrambleWord();
    }
}