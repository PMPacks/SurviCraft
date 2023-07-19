<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyShopUI\commands\enum;

use DaPigGuy\PiggyShopUI\libs\CortexPE\Commando\args\StringEnumArgument;
use DaPigGuy\PiggyShopUI\PiggyShopUI;
use DaPigGuy\PiggyShopUI\shops\ShopCategory;
use pocketmine\command\CommandSender;

class ShopCategoryEnum extends StringEnumArgument
{
    public function parse(string $argument, CommandSender $sender)
    {
        return PiggyShopUI::getInstance()->getShopCategory($argument);
    }

    public function getEnumName(): string
    {
        return "category";
    }

    public function getEnumValues(): array
    {
        return array_map(function (ShopCategory $category): string {
            return $category->getName();
        }, PiggyShopUI::getInstance()->getShopCategories());
    }
    
    public function getTypeName(): string
    {
        return "category";
    }
}