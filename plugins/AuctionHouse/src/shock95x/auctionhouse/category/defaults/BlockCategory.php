<?php
declare(strict_types=1);

namespace shock95x\auctionhouse\category\defaults;

use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use shock95x\auctionhouse\AHListing;
use shock95x\auctionhouse\category\ICategory;

class BlockCategory implements ICategory {

	public function sort(AHListing $listing): bool {
		return $listing->getItem() instanceof ItemBlock;
	}

	public function getName(): string {
		return "Blocks";
	}

	public function getDisplayName(): string {
		return TextFormat::BOLD . TextFormat::RED . $this->getName();
	}

	public function getMenuItem(): Item {
		return ItemFactory::getInstance()->get(ItemIds::BRICK_BLOCK)->setCustomName(TextFormat::RESET . $this->getDisplayName());
	}
}