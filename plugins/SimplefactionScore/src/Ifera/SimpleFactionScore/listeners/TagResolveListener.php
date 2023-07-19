<?php

declare(strict_types=1);

namespace Ifera\SimpleFactionScore\listeners;

use Ifera\SimpleFactionScore\Main;
use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;
use function count;
use function explode;

class TagResolveListener implements Listener {

	/** @var Main */
	private $plugin;

	public function __construct(Main $plugin){
		$this->plugin = $plugin;
	}

	public function onTagResolve(TagsResolveEvent $event){
		$tag = $event->getTag();
		$tags = explode('.', $tag->getName(), 4);
		$value = "";

		if($tags[0] !== 'simplefactionhud' || count($tags) < 4){
			return;
		}

		switch($tags[1]){
			case "faction":
				$value = $this->getFaction($event->getPlayer());
			break;

			case "power":
				$value = $this->getPower($event->getPlayer());
			break;
			
			case "money":
			  $value = $this->getMoney($event->getPlayer());
			break;
			
			case "rank":
			  $value = $this->getRank($event->getPlayer());
			break;
		}

		$tag->setValue((string)($value));
	}
}
