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

class LabTablePacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::LAB_TABLE_PACKET;

	/** @var int */
	public $uselessByte; //0 for client -> server, 1 for server -> client. Seems useless.

	/** @var int */
	public $x;
	/** @var int */
	public $y;
	/** @var int */
	public $z;

	/** @var int */
	public $reactionType;

	protected function decodePayload(){
		$this->uselessByte = (ord($this->get(1)));
		$this->getSignedBlockPosition($this->x, $this->y, $this->z);
		$this->reactionType = (ord($this->get(1)));
	}

	protected function encodePayload(){
		($this->buffer .= chr($this->uselessByte));
		$this->putSignedBlockPosition($this->x, $this->y, $this->z);
		($this->buffer .= chr($this->reactionType));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleLabTable($this);
	}
}
