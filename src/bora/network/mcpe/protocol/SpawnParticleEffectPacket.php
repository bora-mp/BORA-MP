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

use bora\math\Vector3;
use bora\network\mcpe\NetworkSession;
use bora\network\mcpe\protocol\types\DimensionIds;

class SpawnParticleEffectPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::SPAWN_PARTICLE_EFFECT_PACKET;

	/** @var int */
	public $dimensionId = DimensionIds::OVERWORLD; //wtf mojang
	/** @var int */
	public $entityUniqueId = -1; //default none
	/** @var Vector3 */
	public $position;
	/** @var string */
	public $particleName;

	protected function decodePayload(){
		$this->dimensionId = (ord($this->get(1)));
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->position = $this->getVector3();
		$this->particleName = $this->getString();
	}

	protected function encodePayload(){
		($this->buffer .= chr($this->dimensionId));
		$this->putEntityUniqueId($this->entityUniqueId);
		$this->putVector3($this->position);
		$this->putString($this->particleName);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleSpawnParticleEffect($this);
	}
}
