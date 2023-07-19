<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\TreeType;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class JungleBush extends GenericTree{

	/**
	 * Initializes this bush, preparing it to attempt to generate.
	 * @param Random $random
	 * @param BlockTransaction $transaction
	 */
	public function __construct(Random $random, BlockTransaction $transaction){
		parent::__construct($random, $transaction);
		$this->setType(TreeType::JUNGLE());
	}

	public function canPlaceOn(Block $soil) : bool{
		$id = $soil->getId();
		return $id === BlockLegacyIds::GRASS || $id === BlockLegacyIds::DIRT;
	}

	public function generate(ChunkManager $world, Random $random, int $source_x, int $source_y, int $source_z) : bool{
		while((($block_id = $world->getBlockAt($source_x, $source_y, $source_z)->getId()) === BlockLegacyIds::AIR || $block_id === BlockLegacyIds::LEAVES) && $source_y > 0){
			--$source_y;
		}

		// check only below block
		if(!$this->canPlaceOn($world->getBlockAt($source_x, $source_y - 1, $source_z))){
			return false;
		}

		// generates the trunk
		$adjust_y = $source_y;
		$this->transaction->addBlockAt($source_x, $adjust_y + 1, $source_z, $this->log_type);

		// generates the leaves
		for($y = $adjust_y + 1; $y <= $adjust_y + 3; ++$y){
			$radius = 3 - ($y - $adjust_y);

			for($x = $source_x - $radius; $x <= $source_x + $radius; ++$x){
				for($z = $source_z - $radius; $z <= $source_z + $radius; ++$z){
					if(
						!$this->transaction->fetchBlockAt($x, $y, $z)->isSolid() &&
						(
							abs($x - $source_x) !== $radius ||
							abs($z - $source_z) !== $radius ||
							$random->nextBoolean()
						)
					){
						$this->transaction->addBlockAt($x, $y, $z, $this->leaves_type);
					}
				}
			}
		}

		return true;
	}
}