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
use bora\network\mcpe\protocol\ProtocolInfo;
use bora\plugin\Plugin;
use bora\utils\TextFormat;
use function count;
use function implode;
use function stripos;
use function strtolower;

class VersionCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"%bora.command.version.description",
			"%bora.command.version.usage",
			["ver", "about"]
		);
		$this->setPermission("bora.command.version");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) === 0){
			$sender->sendMessage(new TranslationContainer("bora.server.info.extended", [
				$sender->getServer()->getName(),
				$sender->getServer()->getApiVersion(),
				$sender->getServer()->getVersion(),
                $sender->getServer()->getCodeName(),
                ProtocolInfo::CURRENT_PROTOCOL
			]));
		}else{
			$pluginName = implode(" ", $args);
			$exactPlugin = $sender->getServer()->getPluginManager()->getPlugin($pluginName);

			if($exactPlugin instanceof Plugin){
				$this->describeToSender($exactPlugin, $sender);

				return true;
			}

			$found = false;
			$pluginName = strtolower($pluginName);
			foreach($sender->getServer()->getPluginManager()->getPlugins() as $plugin){
				if(stripos($plugin->getName(), $pluginName) !== false){
					$this->describeToSender($plugin, $sender);
					$found = true;
				}
			}

			if(!$found){
				$sender->sendMessage(new TranslationContainer("bora.command.version.noSuchPlugin"));
			}
		}

		return true;
	}

	private function describeToSender(Plugin $plugin, CommandSender $sender){
		$desc = $plugin->getDescription();
		$sender->sendMessage(TextFormat::DARK_GREEN . $desc->getName() . TextFormat::WHITE . " version " . TextFormat::DARK_GREEN . $desc->getVersion());

		if($desc->getDescription() !== ""){
			$sender->sendMessage($desc->getDescription());
		}

		if($desc->getWebsite() !== ""){
			$sender->sendMessage("Website: " . $desc->getWebsite());
		}

		if(count($authors = $desc->getAuthors()) > 0){
			if(count($authors) === 1){
				$sender->sendMessage("Author: " . implode(", ", $authors));
			}else{
				$sender->sendMessage("Authors: " . implode(", ", $authors));
			}
		}
	}
}
