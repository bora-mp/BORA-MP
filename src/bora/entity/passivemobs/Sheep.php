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
use bora\item\Dye;
use bora\item\Shears;
use bora\item\Item;
use bora\item\ItemFactory;
use bora\math\Vector3;
use bora\Player;
use bora\utils\Color;
use bora\utils\Random;
use bora\entity\Entity;

use function boolval;
use function intval;
use function rand;
class Sheep extends Animal{
    public const NETWORK_ID = self::SHEEP;
    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity() : void{
        $this->setMaxHealth(8);
        //$this->propertyManager->setByte(self::DATA_COLOR, $this->namedtag->getByte("Color", $this->getRandomColor($this->level->random)));
        $this->setSheared(boolval($this->namedtag->getByte("Sheared", 0)));
        parent::initEntity();
    }
    public function getName() : string{
        return "Sheep";
    }

    public function isInLove() : bool{
        return $this->getDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INLOVE);
    }

    public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool{
        if(!$this->isImmobile()){
            if($item instanceof Shears and !$this->isSheared()){
                $this->setSheared(true);
                $item->applyDamage(1);
                $i = 1 + $this->level->random->nextBoundedInt(3);
                for($a = 0; $a < $i; $a++){
                    $this->level->dropItem($this, ItemFactory::get(Item::WOOL, intval($this->propertyManager->getByte(self::DATA_COLOR)), 1));
                    $this->motion->y += $this->level->random->nextFloat() * 0.05;
                    $this->motion->x += ($this->level->random->nextFloat() - $this->level->random->nextFloat()) * 0.1;
                    $this->motion->z += ($this->level->random->nextFloat() - $this->level->random->nextFloat()) * 0.1;
                }
                return true;
            }
            if($item instanceof Dye){
                if($player->isSurvival()){
                    $item->pop();
                }
                $this->propertyManager->setByte(self::DATA_COLOR, $item->getDamage());
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
            ItemFactory::get(Item::WOOL, intval($this->propertyManager->getByte(self::DATA_COLOR)), $this->isSheared() ? 0 : 1),
            ($this->isOnFire() ? ItemFactory::get(Item::COOKED_MUTTON, 0, rand(1, 3)) : ItemFactory::get(Item::RAW_MUTTON, 0, rand(1, 3)))
        ];
    }
    public function isSheared() : bool{
        return $this->getGenericFlag(self::DATA_FLAG_SHEARED);
    }
    public function setSheared(bool $value = true) : void{
        $this->setGenericFlag(self::DATA_FLAG_SHEARED, $value);
    }
    public function saveNBT() : void{
        parent::saveNBT();
        $this->namedtag->setByte("Sheared", intval($this->isSheared()));
        $this->namedtag->setByte("Color", intval($this->propertyManager->getByte(self::DATA_COLOR)));
    }
    /**
     * @param Random $random
     *
     * @return int
     */
    public function getRandomColor(Random $random) : bool{
        $i = $random->nextBoundedInt(100);
        if($i < 5){
            return Color::COLOR_DYE_BLACK;
        }elseif($i < 10){
            return Color::COLOR_DYE_GRAY;
        }elseif($i < 15){
            return Color::COLOR_DYE_LIGHT_GRAY;
        }elseif($i < 18){
            return Color::COLOR_DYE_BROWN;
        }elseif($i < 22){
            return Color::COLOR_DYE_WHITE;
        }elseif($random->nextBoundedInt(500) === 0){
            return Color::COLOR_DYE_PINK;
        }
       return true;
    }
    /**
     * @param Vector3 $pos
     */
    public function eatGrassBonus(Vector3 $pos) : void{
        if(!$this->isBaby()){
            if($this->isSheared()){
                $this->setSheared(false);
            }
        }else{
            // TODO: enlarge baby
        }
    }
}