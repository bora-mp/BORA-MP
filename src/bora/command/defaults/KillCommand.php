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

namespace bora\command\defaults;

use bora\command\Command;
use bora\command\CommandSender;
use bora\command\utils\InvalidCommandSyntaxException;
use bora\event\entity\EntityDamageEvent;
use bora\lang\TranslationContainer;
use bora\Player;
use bora\utils\TextFormat;
use function count;

class KillCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"%bora.command.kill.description",
			"%bora.command.kill.usage",
			["suicide"]
		);
		$this->setPermission("bora.command.kill.self;bora.command.kill.other");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) >= 2){
			throw new InvalidCommandSyntaxException();
		}

		if(count($args) === 1){
			if(!$sender->hasPermission("bora.command.kill.other")){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

				return true;
			}

			$player = $sender->getServer()->getPlayer($args[0]);

			if($player instanceof Player){
				$player->attack(new EntityDamageEvent($player, EntityDamageEvent::CAUSE_SUICIDE, 1000));
				Command::broadcastCommandMessage($sender, new TranslationContainer("commands.kill.successful", [$player->getName()]));
			}else{
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.player.notFound"));
			}

			return true;
		}

		if($sender instanceof Player){
			if(!$sender->hasPermission("bora.command.kill.self")){
				$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));

				return true;
			}

			$sender->attack(new EntityDamageEvent($sender, EntityDamageEvent::CAUSE_SUICIDE, 1000));
			$sender->sendMessage(new TranslationContainer("commands.kill.successful", [$sender->getName()]));
		}else{
			throw new InvalidCommandSyntaxException();
		}

		return true;
	}
}
