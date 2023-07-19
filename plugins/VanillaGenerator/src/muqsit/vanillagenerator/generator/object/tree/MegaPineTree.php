<?php

declare(strict_types=1);

namespace muqsit\vanillagenerator\generator\object\tree;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\BlockTransaction;
use pocketmine\world\ChunkManager;

class MegaPineTree extends MegaRedwoodTree{

	public function __construct(Random $random, BlockTransaction $transaction){
		parent::__construct($random, $transaction);
		$this->setLeavesHeight($random->nextBoundedInt(5) + 3);
	}

	public function generate(ChunkManager $world, Random $random, int $source_x, int $source_y, int $source_z) : bool{
		$generated = parent::generate($world, $random, $source_x, $source_y, $source_z);
		if($generated){
			$this->generatePodzol($source_x, $source_y, $source_z, $world, $random);
		}
		return $generated;
	}

	protected function generateDirtBelowTrunk(int $block_x, int $block_y, int $block_z) : void{
		// SELF, SOUTH, EAST, SOUTH EAST
		$this->transaction->addBlockAt($block_x, $block_y - 1, $block_z, VanillaBlocks::PODZOL());
		$this->transaction->addBlockAt($block_x, $block_y - 1, $block_z + 1, VanillaBlocks::PODZOL());
		$this->transaction->addBlockAt($block_x + 1, $block_y - 1, $block_z, VanillaBlocks::PODZOL());
		$this->transaction->addBlockAt($block_x + 1, $block_y - 1, $block_z + 1, VanillaBlocks::PODZOL());
	}

	private function generatePodzol(int $source_x, int $source_y, int $source_z, ChunkManager $world, Random $random) : void{
		$this->generatePodzolPatch($source_x - 1, $source_y, $source_z - 1, $world);
		$this->generatePodzolPatch($source_x + 2, $source_y, $source_z - 1, $world);
		$this->generatePodzolPatch($source_x - 1, $source_y, $source_z + 2, $world);
		$this->generatePodzolPatch($source_x + 2, $source_y, $source_z + 2, $world);
		for($i = 0; $i < 5; ++$i){
			$n = $random->nextBoundedInt(64);
			if($n % 8 === 0 || $n % 8 === 7 || $n / 8 === 0 || $n / 8 === 7){
				$this->generatePodzolPatch($source_x - 3 + $n % 8, $source_y, $source_z - 3 + $n / 8, $world);
			}
		}
	}

	private function generatePodzolPatch(int $source_x, int $source_y, int $source_z, ChunkManager $world) : void{
		for($x = -2; $x <= 2; ++$x){
			for($z = -2; $z <= 2; ++$z){
				if(abs($x) === 2 && abs($z) === 2){
					continue;
				}
				for($y = 2; $y >= -3; --$y){
					$block_id = $world->getBlockAt($source_x + $x, $source_y + $y, $source_z + $z)->getId();
					if($block_id === BlockLegacyIds::GRASS || $block_id === BlockLegacyIds::DIRT){
						if($world->getBlockAt($source_x + $x, $source_y + $y + 1, $source_z + $z)->isSolid()){
							$dirt = VanillaBlocks::DIRT();
						}else{
							$dirt = VanillaBlocks::PODZOL();
						}
						$world->setBlockAt($source_x + $x, $source_y + $y, $source_z + $z, $dirt);
					}elseif($block_id !== BlockLegacyIds::AIR && $source_y + $y < $source_y){
						break;
					}
				}
			}
		}
	}
}