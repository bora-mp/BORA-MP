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

/**
 * Named Binary Tag handling classes
 */
namespace bora\nbt;

use bora\nbt\tag\ByteArrayTag;
use bora\nbt\tag\ByteTag;
use bora\nbt\tag\CompoundTag;
use bora\nbt\tag\DoubleTag;
use bora\nbt\tag\FloatTag;
use bora\nbt\tag\IntArrayTag;
use bora\nbt\tag\IntTag;
use bora\nbt\tag\ListTag;
use bora\nbt\tag\LongTag;
use bora\nbt\tag\NamedTag;
use bora\nbt\tag\ShortTag;
use bora\nbt\tag\StringTag;

abstract class NBT{

	public const TAG_End = 0;
	public const TAG_Byte = 1;
	public const TAG_Short = 2;
	public const TAG_Int = 3;
	public const TAG_Long = 4;
	public const TAG_Float = 5;
	public const TAG_Double = 6;
	public const TAG_ByteArray = 7;
	public const TAG_String = 8;
	public const TAG_List = 9;
	public const TAG_Compound = 10;
	public const TAG_IntArray = 11;

	/**
	 * @param int $type
	 *
	 * @return NamedTag
	 */
	public static function createTag(int $type) : NamedTag{
		switch($type){
			case self::TAG_Byte:
				return new ByteTag();
			case self::TAG_Short:
				return new ShortTag();
			case self::TAG_Int:
				return new IntTag();
			case self::TAG_Long:
				return new LongTag();
			case self::TAG_Float:
				return new FloatTag();
			case self::TAG_Double:
				return new DoubleTag();
			case self::TAG_ByteArray:
				return new ByteArrayTag();
			case self::TAG_String:
				return new StringTag();
			case self::TAG_List:
				return new ListTag();
			case self::TAG_Compound:
				return new CompoundTag();
			case self::TAG_IntArray:
				return new IntArrayTag();
			default:
				throw new \InvalidArgumentException("Unknown NBT tag type $type");
		}
	}
}
