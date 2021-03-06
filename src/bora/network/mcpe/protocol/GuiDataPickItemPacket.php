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

class GuiDataPickItemPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::GUI_DATA_PICK_ITEM_PACKET;

	/** @var string */
	public $itemDescription;
	/** @var string */
	public $itemEffects;
	/** @var int */
	public $hotbarSlot;

	protected function decodePayload(){
		$this->itemDescription = $this->getString();
		$this->itemEffects = $this->getString();
		$this->hotbarSlot = ((unpack("V", $this->get(4))[1] << 32 >> 32));
	}

	protected function encodePayload(){
		$this->putString($this->itemDescription);
		$this->putString($this->itemEffects);
		($this->buffer .= (pack("V", $this->hotbarSlot)));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleGuiDataPickItem($this);
	}
}
