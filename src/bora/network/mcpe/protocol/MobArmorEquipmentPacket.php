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

namespace bora\network\mcpe\protocol;

use bora\utils\Binary;


use bora\item\Item;
use bora\network\mcpe\NetworkSession;

class MobArmorEquipmentPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET;

	/** @var int */
	public $entityRuntimeId;
	/** @var Item[] */
	public $slots = [];

	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		for($i = 0; $i < 4; ++$i){
			$this->slots[$i] = $this->getSlot();
		}
	}

	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		for($i = 0; $i < 4; ++$i){
			$this->putSlot($this->slots[$i]);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleMobArmorEquipment($this);
	}
}
