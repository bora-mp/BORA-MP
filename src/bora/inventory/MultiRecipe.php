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

use bora\utils\UUID;

class MultiRecipe{
	public const TYPE_REPAIR_ITEM = "00000000-0000-0000-0000-000000000001";
	public const TYPE_MAP_EXTENDING = "D392B075-4BA1-40AE-8789-AF868D56F6CE";
	public const TYPE_MAP_CLONING = "85939755-BA10-4D9D-A4CC-EFB7A8E943C4";
	public const TYPE_MAP_UPGRADING = "AECD2294-4B94-434B-8667-4499BB2C9327";
	public const TYPE_BOOK_CLONING = "D1CA6B84-338E-4F2F-9C6B-76CC8B4BD98D";
	public const TYPE_BANNER_DUPLICATE = "B5C5D105-75A2-4076-AF2B-923EA2BF4BF0";
	public const TYPE_BANNER_ADD_PATTERN = "D81AAEAF-E172-4440-9225-868DF030D27B";
	public const TYPE_FIREWORKS = "00000000-0000-0000-0000-000000000002";

	private $uuid;

	public function __construct(UUID $uuid){
		$this->uuid = $uuid;
	}
}
