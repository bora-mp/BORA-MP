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

use bora\network\mcpe\NetworkSession;

class SetDisplayObjectivePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SET_DISPLAY_OBJECTIVE_PACKET;

	/** @var string */
	public $displaySlot;
	/** @var string */
	public $objectiveName;
	/** @var string */
	public $displayName;
	/** @var string */
	public $criteriaName;
	/** @var int */
	public $sortOrder;

	protected function decodePayload(){
		$this->displaySlot = $this->getString();
		$this->objectiveName = $this->getString();
		$this->displayName = $this->getString();
		$this->criteriaName = $this->getString();
		$this->sortOrder = $this->getVarInt();
	}

	protected function encodePayload(){
		$this->putString($this->displaySlot);
		$this->putString($this->objectiveName);
		$this->putString($this->displayName);
		$this->putString($this->criteriaName);
		$this->putVarInt($this->sortOrder);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSetDisplayObjective($this);
	}
}
