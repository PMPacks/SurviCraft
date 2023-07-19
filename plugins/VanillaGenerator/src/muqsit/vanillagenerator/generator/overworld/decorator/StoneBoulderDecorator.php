<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use muqsit\vanillagenerator\generator\object\StoneBoulder;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class StoneBoulderDecorator extends Decorator{
	
	public function populate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$source_x = $chunk_x << 4;
        $source_z = $chunk_z << 4;
        for ($i = 0; $i < $random->nextBoundedInt(3); ++$i) {
			$x = $source_x + $random->nextBoundedInt(16);
            $z = $source_z + $random->nextBoundedInt(16);
            $y = $chunk->getHighestBlockAt($x & 0x0f, $z & 0x0f);
			(new StoneBoulder())->generate($world, $random, $x, $y, $z);
        }
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
	}
}