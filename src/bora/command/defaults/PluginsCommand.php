<?php

/*
*
*    ____   ____  _____                 __  __ _____
*   |  _ \ / __ \|  __ \     /\        |  \/  |  __ \
*   | |_) | |  | | |__) |   /  \ ______| \  / | |__) |
*   |  _ <| |  | |  _  /   / /\ \______| |\/| |  ___/
*   | |_) | |__| | | \ \  / ____ \     | |  | | |
*   |____/ \____/|_|  \_\/_/    \_\    |_|  |_|_|
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* @author Bora Team
* @link http://bora.kodmadeni.com/
*
*
*/

declare(strict_types=1);

namespace bora\command\defaults;

use bora\command\CommandSender;
use bora\lang\TranslationContainer;
use bora\plugin\Plugin;
use bora\utils\TextFormat;
use function array_map;
use function count;
use function implode;

class PluginsCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"%bora.command.plugins.description",
			"%bora.command.plugins.usage",
			["pl"]
		);
		$this->setPermission("bora.command.plugins");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		$list = array_map(function(Plugin $plugin) : string{
			return ($plugin->isEnabled() ? TextFormat::GREEN : TextFormat::RED) . $plugin->getDescription()->getFullName();
		}, $sender->getServer()->getPluginManager()->getPlugins());

		$sender->sendMessage(new TranslationContainer("bora.command.plugins.success", [count($list), implode(TextFormat::WHITE . ", ", $list)]));
		return true;
	}
}
