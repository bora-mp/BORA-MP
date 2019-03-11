<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.bora.net/
 *
 *
*/

declare(strict_types=1);

namespace bora\entity\object;

use bora\entity\Entity;
use bora\entity\Explosive;
use bora\event\entity\EntityDamageEvent;
use bora\event\entity\ExplosionPrimeEvent;
use bora\level\Explosion;
use bora\nbt\tag\ShortTag;
use bora\network\mcpe\protocol\LevelEventPacket;

class PrimedTNT extends Entity implements Explosive{
	public const NETWORK_ID = self::TNT;

	public $width = 0.98;
	public $height = 0.98;

	protected $baseOffset = 0.49;

	protected $gravity = 0.04;
	protected $drag = 0.02;

	protected $fuse;

	public $canCollide = false;


	public function attack(EntityDamageEvent $source) : void{
		if($source->getCause() === EntityDamageEvent::CAUSE_VOID){
			parent::attack($source);
		}
	}

	protected function initEntity() : void{
		parent::initEntity();

		if($this->namedtag->hasTag("Fuse", ShortTag::class)){
			$this->fuse = $this->namedtag->getShort("Fuse");
		}else{
			$this->fuse = 80;
		}

		$this->setGenericFlag(self::DATA_FLAG_IGNITED, true);
		$this->propertyManager->setInt(self::DATA_FUSE_LENGTH, $this->fuse);

		$this->level->broadcastLevelEvent($this, LevelEventPacket::EVENT_SOUND_IGNITE);
	}


	public function canCollideWith(Entity $entity) : bool{
		return false;
	}

	public function saveNBT() : void{
		parent::saveNBT();
		$this->namedtag->setShort("Fuse", $this->fuse, true); //older versions incorrectly saved this as a byte
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->fuse % 5 === 0){ //don't spam it every tick, it's not necessary
			$this->propertyManager->setInt(self::DATA_FUSE_LENGTH, $this->fuse);
		}

		if(!$this->isFlaggedForDespawn()){
			$this->fuse -= $tickDiff;

			if($this->fuse <= 0){
				$this->flagForDespawn();
				$this->explode();
			}
		}

		return $hasUpdate or $this->fuse >= 0;
	}

	public function explode() : void{
		$ev = new ExplosionPrimeEvent($this, 4);
		$ev->call();
		if(!$ev->isCancelled()){
			$explosion = new Explosion($this, $ev->getForce(), $this);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}
}
