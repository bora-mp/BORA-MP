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

class NpcRequestPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::NPC_REQUEST_PACKET;

	/** @var int */
	public $entityRuntimeId;
	/** @var int */
	public $requestType;
	/** @var string */
	public $commandString;
	/** @var int */
	public $actionType;

	protected function decodePayload(){
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->requestType = (ord($this->get(1)));
		$this->commandString = $this->getString();
		$this->actionType = (ord($this->get(1)));
	}

	protected function encodePayload(){
		$this->putEntityRuntimeId($this->entityRuntimeId);
		($this->buffer .= chr($this->requestType));
		$this->putString($this->commandString);
		($this->buffer .= chr($this->actionType));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleNpcRequest($this);
	}
}
