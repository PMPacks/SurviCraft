<?php
declare(strict_types=1);

namespace shock95x\auctionhouse\commands\subcommand;

use shock95x\auctionhouse\libs\CortexPE\Commando\BaseSubCommand;
use shock95x\auctionhouse\libs\CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use shock95x\auctionhouse\menu\admin\AdminMenu;
use shock95x\auctionhouse\menu\type\AHMenu;

class AdminCommand extends BaseSubCommand {

	protected function prepare(): void {
		$this->setPermission("auctionhouse.command.admin");
		$this->addConstraint(new InGameRequiredConstraint($this));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		assert($sender instanceof Player);
		//AHMenu::open(new AdminMenu($sender, false));
	}
}