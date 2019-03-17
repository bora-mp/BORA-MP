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
use bora\item\Item;
use bora\item\ItemFactory;
use bora\math\Vector3;
use bora\entity\Entity;
use bora\utils\Random;
use bora\level\Level;

use function boolval;
use function intval;
use function rand;

class Chicken extends Animal{
    public const NETWORK_ID = self::CHICKEN;
    public $width = 0.4;
    public $height = 0.7;
    protected $chickenJockey = false;
    protected $timeUntilNextEgg = 0;
    /**
     * @return bool
     */
    public function isChickenJockey() : bool{
        return $this->chickenJockey;
    }
    /**
     * @param bool $chickenJockey
     */
    public function setChickenJockey(bool $chickenJockey) : void{
        $this->chickenJockey = $chickenJockey;
    }

    public function random(Random $random, Level $level) {
        return new Random($level->random->nextInt());
    }

    protected function initEntity() : void{
        $this->setMaxHealth(4);
        $this->setChickenJockey(boolval($this->namedtag->getByte("isChickenJockey", 0)));
        $this->timeUntilNextEgg = $this->level->random->nextBoundedInt(6000) + 6000;
        parent::initEntity();
    }
    public function getName() : string{
        return "Chicken";
    }


    public function getXpDropAmount() : int{
        return rand(1, 3);
    }
    public function getDrops() : array{
        return [
            ($this->isOnFire() ? ItemFactory::get(Item::COOKED_CHICKEN, 0, 1) : ItemFactory::get(Item::RAW_CHICKEN, 0, 1)),
            ItemFactory::get(Item::FEATHER, 0, rand(0, 2))
        ];
    }
    public function saveNBT() : void{
        parent::saveNBT();
        $this->namedtag->setByte("isChickenJockey", intval($this->isChickenJockey()));
    }
    public function getRiderSeatPosition(int $seatNumber = 0) : Vector3{
        return new Vector3(0, 1, 0);
    }
    public function entityBaseTick(int $diff = 1) : bool{
        if(!$this->onGround and $this->motion->y < 0){
            $this->motion->y *= 0.6;
        }
        if(!$this->isImmobile() and !$this->isBaby() and !$this->isChickenJockey() and $this->timeUntilNextEgg-- <= 0){
            $this->level->dropItem($this, ItemFactory::get(Item::EGG));
            $this->timeUntilNextEgg = $this->level->random->nextBoundedInt(6000) + 6000;
        }
        return parent::entityBaseTick($diff);
    }
    public function fall(float $fallDistance) : void{
        // chickens do not get damage when fall
    }
}