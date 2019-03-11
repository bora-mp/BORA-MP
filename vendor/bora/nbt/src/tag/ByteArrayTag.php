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

namespace bora\nbt\tag;

use bora\nbt\NBT;
use bora\nbt\NBTStream;
use function strlen;

use bora\utils\Binary;

class ByteArrayTag extends NamedTag{
	/** @var string */
	private $value;

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function __construct(string $name = "", string $value = ""){
		parent::__construct($name);
		$this->value = $value;
	}

	public function getType() : int{
		return NBT::TAG_ByteArray;
	}

	public function read(NBTStream $nbt) : void{
		$this->value = $nbt->get($nbt->getInt());
	}

	public function write(NBTStream $nbt) : void{
		$nbt->putInt(strlen($this->value));
		($nbt->buffer .= $this->value);
	}

	/**
	 * @return string
	 */
	public function getValue() : string{
		return $this->value;
	}
}
