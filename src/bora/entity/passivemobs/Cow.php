<?php
/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 */

/*
*
*    ____   ____  _____                 __  __ _____
*   |  _ \ / __ \|  __ \     /\        |  \/  |  __ \
*   | |_) | |  | | |__) |   /  \ ______| \  / | |__) |
*   |  _ <| |  | |  _  /   / /\ \______| |\/| |  ___/
*   | |_) | |__| | | \ \  / ____ \     | |  | | |
*   |____/ \____/|_|  \_\/_/    \_\    |_|  |_|_|
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* @author Bora Team
* @link http://bora.kodmadeni.com/
*
*
*/
declare(strict_types=1);

namespace bora\entity\passivemobs;

use bora\entity\Animal;
use bora\item\Bucket;
use bora\item\Item;
use bora\item\ItemFactory;
use bora\math\Vector3;
use bora\Player;
use bora\entity\Entity;
use function rand;

class Cow extends Animal{
    public const NETWORK_ID = self::COW;
    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity() : void{
        $this->setMaxHealth(10);

        parent::initEntity();
    }
    public function getName() : string{
        return "Cow";
    }

    public function isInLove() : bool{
        return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE);
    }

    public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
        if(!$this->isImmobile()){
            if($item instanceof Bucket and $item->getDamage() === 0){
                $item->pop();
                $player->getInventory()->addItem(ItemFactory::get(Item::BUCKET, 1));
                return true;
            }
        }
        return parent::onInteract($player, $item, $clickPos);
    }
    public function getXpDropAmount() : int{
        return rand(1, ($this->isInLove() ? 7 : 3));
    }
    public function getDrops() : array{
        return [
            ItemFactory::get(Item::LEATHER, 0, rand(0, 2)),
            ($this->isOnFire() ? ItemFactory::get(Item::STEAK, 0, rand(1, 3)) : ItemFactory::get(Item::RAW_BEEF, 0, rand(1, 3)))
        ];
    }
}