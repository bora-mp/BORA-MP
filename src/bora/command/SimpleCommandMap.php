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

namespace bora\command;

use bora\command\defaults\BanCommand;
use bora\command\defaults\BanIpCommand;
use bora\command\defaults\BanListCommand;
use bora\command\defaults\DefaultGamemodeCommand;
use bora\command\defaults\DeopCommand;
use bora\command\defaults\DifficultyCommand;
use bora\command\defaults\DumpMemoryCommand;
use bora\command\defaults\EffectCommand;
use bora\command\defaults\EnchantCommand;
use bora\command\defaults\GamemodeCommand;
use bora\command\defaults\GarbageCollectorCommand;
use bora\command\defaults\GiveCommand;
use bora\command\defaults\HelpCommand;
use bora\command\defaults\KickCommand;
use bora\command\defaults\KillCommand;
use bora\command\defaults\ListCommand;
use bora\command\defaults\MeCommand;
use bora\command\defaults\OpCommand;
use bora\command\defaults\PardonCommand;
use bora\command\defaults\PardonIpCommand;
use bora\command\defaults\ParticleCommand;
use bora\command\defaults\PluginsCommand;
use bora\command\defaults\ReloadCommand;
use bora\command\defaults\SaveCommand;
use bora\command\defaults\SaveOffCommand;
use bora\command\defaults\SaveOnCommand;
use bora\command\defaults\SayCommand;
use bora\command\defaults\SeedCommand;
use bora\command\defaults\SetWorldSpawnCommand;
use bora\command\defaults\SpawnpointCommand;
use bora\command\defaults\StatusCommand;
use bora\command\defaults\StopCommand;
use bora\command\defaults\TeleportCommand;
use bora\command\defaults\TellCommand;
use bora\command\defaults\TimeCommand;
use bora\command\defaults\TimingsCommand;
use bora\command\defaults\TitleCommand;
use bora\command\defaults\TransferServerCommand;
use bora\command\defaults\VanillaCommand;
use bora\command\defaults\VersionCommand;
use bora\command\defaults\WhitelistCommand;
use bora\command\defaults\XpCommand;
use bora\command\defaults\ClearCommand;
use bora\command\utils\InvalidCommandSyntaxException;
use bora\Server;
use function array_shift;
use function count;
use function explode;
use function implode;
use function min;
use function preg_match_all;
use function stripslashes;
use function strpos;
use function strtolower;
use function trim;

class SimpleCommandMap implements CommandMap{

	/**
	 * @var Command[]
	 */
	protected $knownCommands = [];

	/** @var Server */
	private $server;

	public function __construct(Server $server){
		$this->server = $server;
		$this->setDefaultCommands();
	}

	private function setDefaultCommands(){
		$this->registerAll("bora", [
			new BanCommand("ban"),
			new BanIpCommand("ban-ip"),
			new BanListCommand("banlist"),
			new DefaultGamemodeCommand("defaultgamemode"),
			new DeopCommand("deop"),
			new DifficultyCommand("difficulty"),
			new DumpMemoryCommand("dumpmemory"),
			new EffectCommand("effect"),
			new EnchantCommand("enchant"),
			new GamemodeCommand("gamemode"),
			new GarbageCollectorCommand("gc"),
			new GiveCommand("give"),
			new HelpCommand("help"),
			new KickCommand("kick"),
			new KillCommand("kill"),
			new ListCommand("list"),
			new MeCommand("me"),
			new OpCommand("op"),
			new PardonCommand("pardon"),
			new PardonIpCommand("pardon-ip"),
			new ParticleCommand("particle"),
			new PluginsCommand("plugins"),
			new ReloadCommand("reload"),
			new SaveCommand("save-all"),
			new SaveOffCommand("save-off"),
			new SaveOnCommand("save-on"),
			new SayCommand("say"),
			new SeedCommand("seed"),
			new SetWorldSpawnCommand("setworldspawn"),
			new SpawnpointCommand("spawnpoint"),
			new StatusCommand("status"),
			new StopCommand("stop"),
			new TeleportCommand("tp"),
			new TellCommand("tell"),
			new TimeCommand("time"),
			new TimingsCommand("timings"),
			new TitleCommand("title"),
			new TransferServerCommand("transferserver"),
			new VersionCommand("version"),
            new WhitelistCommand("whitelist"),
            new XpCommand("xp"),
            new ClearCommand("clear")
		]);
	}


	public function registerAll(string $fallbackPrefix, array $commands){
		foreach($commands as $command){
			$this->register($fallbackPrefix, $command);
		}
	}

	/**
	 * @param string      $fallbackPrefix
	 * @param Command     $command
	 * @param string|null $label
	 *
	 * @return bool
	 */
	public function register(string $fallbackPrefix, Command $command, string $label = null) : bool{
		if($label === null){
			$label = $command->getName();
		}
		$label = trim($label);
		$fallbackPrefix = strtolower(trim($fallbackPrefix));

		$registered = $this->registerAlias($command, false, $fallbackPrefix, $label);

		$aliases = $command->getAliases();
		foreach($aliases as $index => $alias){
			if(!$this->registerAlias($command, true, $fallbackPrefix, $alias)){
				unset($aliases[$index]);
			}
		}
		$command->setAliases($aliases);

		if(!$registered){
			$command->setLabel($fallbackPrefix . ":" . $label);
		}

		$command->register($this);

		return $registered;
	}

	/**
	 * @param Command $command
	 *
	 * @return bool
	 */
	public function unregister(Command $command) : bool{
		foreach($this->knownCommands as $lbl => $cmd){
			if($cmd === $command){
				unset($this->knownCommands[$lbl]);
			}
		}

		$command->unregister($this);

		return true;
	}

	/**
	 * @param Command $command
	 * @param bool    $isAlias
	 * @param string  $fallbackPrefix
	 * @param string  $label
	 *
	 * @return bool
	 */
	private function registerAlias(Command $command, bool $isAlias, string $fallbackPrefix, string $label) : bool{
		$this->knownCommands[$fallbackPrefix . ":" . $label] = $command;
		if(($command instanceof VanillaCommand or $isAlias) and isset($this->knownCommands[$label])){
			return false;
		}

		if(isset($this->knownCommands[$label]) and $this->knownCommands[$label]->getLabel() !== null and $this->knownCommands[$label]->getLabel() === $label){
			return false;
		}

		if(!$isAlias){
			$command->setLabel($label);
		}

		$this->knownCommands[$label] = $command;

		return true;
	}

	/**
	 * Returns a command to match the specified command line, or null if no matching command was found.
	 * This method is intended to provide capability for handling commands with spaces in their name.
	 * The referenced parameters will be modified accordingly depending on the resulting matched command.
	 *
	 * @param string   &$commandName
	 * @param string[] &$args
	 *
	 * @return Command|null
	 */
	public function matchCommand(string &$commandName, array &$args){
		$count = min(count($args), 255);

		for($i = 0; $i < $count; ++$i){
			$commandName .= array_shift($args);
			if(($command = $this->getCommand($commandName)) instanceof Command){
				return $command;
			}

			$commandName .= " ";
		}

		return null;
	}

	public function dispatch(CommandSender $sender, string $commandLine) : bool{
		$args = [];
		preg_match_all('/"((?:\\\\.|[^\\\\"])*)"|(\S+)/u', $commandLine, $matches);
		foreach($matches[0] as $k => $_){
			for($i = 1; $i <= 2; ++$i){
				if($matches[$i][$k] !== ""){
					$args[$k] = stripslashes($matches[$i][$k]);
					break;
				}
			}
		}
		$sentCommandLabel = "";
		$target = $this->matchCommand($sentCommandLabel, $args);

		if($target === null){
			return false;
		}

		$target->timings->startTiming();

		try{
			$target->execute($sender, $sentCommandLabel, $args);
		}catch(InvalidCommandSyntaxException $e){
			$sender->sendMessage($this->server->getLanguage()->translateString("commands.generic.usage", [$target->getUsage()]));
		}finally{
			$target->timings->stopTiming();
		}

		return true;
	}

	public function clearCommands(){
		foreach($this->knownCommands as $command){
			$command->unregister($this);
		}
		$this->knownCommands = [];
		$this->setDefaultCommands();
	}

	public function getCommand(string $name){
		return $this->knownCommands[$name] ?? null;
	}

	/**
	 * @return Command[]
	 */
	public function getCommands() : array{
		return $this->knownCommands;
	}


	/**
	 * @return void
	 */
	public function registerServerAliases(){
		$values = $this->server->getCommandAliases();

		foreach($values as $alias => $commandStrings){
			if(strpos($alias, ":") !== false){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("bora.command.alias.illegal", [$alias]));
				continue;
			}

			$targets = [];
			$bad = [];
			$recursive = [];

			foreach($commandStrings as $commandString){
				$args = explode(" ", $commandString);
				$commandName = "";
				$command = $this->matchCommand($commandName, $args);


				if($command === null){
					$bad[] = $commandString;
				}elseif($commandName === $alias){
					$recursive[] = $commandString;
				}else{
					$targets[] = $commandString;
				}
			}

			if(!empty($recursive)){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("bora.command.alias.recursive", [$alias, implode(", ", $recursive)]));
				continue;
			}

			if(!empty($bad)){
				$this->server->getLogger()->warning($this->server->getLanguage()->translateString("bora.command.alias.notFound", [$alias, implode(", ", $bad)]));
				continue;
			}

			//These registered commands have absolute priority
			if(count($targets) > 0){
				$this->knownCommands[strtolower($alias)] = new FormattedCommandAlias(strtolower($alias), $targets);
			}else{
				unset($this->knownCommands[strtolower($alias)]);
			}

		}
	}
}
