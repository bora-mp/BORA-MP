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

namespace bora\inventory;

use bora\entity\Entity;
use bora\event\entity\EntityArmorChangeEvent;
use bora\item\Item;

class ArmorInventoryEventProcessor implements InventoryEventProcessor{
	/** @var Entity */
	private $entity;

	public function __construct(Entity $entity){
		$this->entity = $entity;
	}

	public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem, Item $newItem) : ?Item{
		$ev = new EntityArmorChangeEvent($this->entity, $oldItem, $newItem, $slot);
		$ev->call();
		if($ev->isCancelled()){
			return null;
		}

		return $ev->getNewItem();
	}
}
