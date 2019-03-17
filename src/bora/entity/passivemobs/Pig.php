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
declare(strict_types=1);
namespace bora\entity\passivemobs;
use bora\entity\Animal;
use bora\item\Item;
use bora\item\ItemFactory;
use bora\math\Vector3;
use bora\Player;
use bora\entity\Entity;

use function boolval;
use function intval;
use function rand;
class Pig extends Animal{
    public const NETWORK_ID = self::PIG;
    public $width = 0.9;
    public $height = 0.9;

    protected function initEntity() : void{
        $this->setMaxHealth(10);
        $this->setSaddled(boolval($this->namedtag->getByte("Saddle", 0)));
        parent::initEntity();
    }
    public function getName() : string{
        return "Pig";
    }

    public function isInLove() : bool{
        return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE);
    }

    public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
        if(parent::onInteract($player, $item, $clickPos)){
            return true;
        }
        if(!$this->isImmobile()){
            if($this->isSaddled() and $this->getRiddenByEntity() === null){
                $player->mountEntity($this);
                return true;
            }
        }
        return false;
    }
    public function getXpDropAmount() : int{
        return rand(1, ($this->isInLove() ? 7 : 3));
    }
    public function getDrops() : array{
        return [
            ($this->isOnFire() ? ItemFactory::get(Item::COOKED_PORKCHOP, 0, rand(1, 3)) : ItemFactory::get(Item::RAW_PORKCHOP, 0, rand(1, 3)))
        ];
    }
    public function isSaddled() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_SADDLED);
    }
    public function setSaddled(bool $value = true) : void{
        $this->setGenericFlag(self::DATA_FLAG_SADDLED, $value);
    }
    public function saveNBT() : void {
        parent::saveNBT();
        $this->namedtag->setByte("Saddle", intval($this->isSaddled()));
    }
    public function getRiderSeatPosition(int $seatNumber = 0) : Vector3{
        return new Vector3(0, 0.63, 0);
    }
}