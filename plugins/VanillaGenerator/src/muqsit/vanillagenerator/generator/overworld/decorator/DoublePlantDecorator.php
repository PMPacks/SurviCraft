<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\overworld\decorator;

use muqsit\vanillagenerator\generator\Decorator;
use muqsit\vanillagenerator\generator\object\DoubleTallPlant;
use muqsit\vanillagenerator\generator\overworld\decorator\types\DoublePlantDecoration;
use pocketmine\block\DoublePlant;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;

class DoublePlantDecorator extends Decorator{

	/**
	 * @param Random $random
	 * @param DoublePlantDecoration[] $decorations
	 * @return DoublePlant|null
	 */
	private static function getRandomDoublePlant(Random $random, array $decorations) : ?DoublePlant{
		$totalWeight = 0;
		foreach($decorations as $decoration){
			$totalWeight += $decoration->getWeight();
		}
		$weight = $random->nextBoundedInt($totalWeight);
		foreach($decorations as $decoration){
			$weight -= $decoration->getWeight();
			if($weight < 0){
				return $decoration->getBlock();
			}
		}
		return null;
	}

	/** @var DoublePlantDecoration[] */
	private array $doublePlants = [];

	final public function setDoublePlants(DoublePlantDecoration ...$doublePlants) : void{
		$this->doublePlants = $doublePlants;
	}

	public function decorate(ChunkManager $world, Random $random, int $chunk_x, int $chunk_z, Chunk $chunk) : void{
		$x = $random->nextBoundedInt(16);
		$z = $random->nextBoundedInt(16);
		$source_y = $random->nextBoundedInt($chunk->getHighestBlockAt($x, $z) + 32);

		$species = self::getRandomDoublePlant($random, $this->doublePlants);
		(new DoubleTallPlant($species))->generate($world, $random, ($chunk_x << 4) + $x, $source_y, ($chunk_z << 4) + $z);
	}
}