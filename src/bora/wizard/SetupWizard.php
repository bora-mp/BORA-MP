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

/**
 * Set-up wizard used on the first run
 * Can be disabled with --no-wizard
 */
namespace bora\wizard;

use bora\lang\BaseLang;
use bora\utils\Config;
use bora\utils\Internet;
use bora\utils\InternetException;
use function base64_encode;
use function fgets;
use function random_bytes;
use function sleep;
use function strtolower;
use function substr;
use function trim;
use const PHP_EOL;
use const STDIN;

class SetupWizard{
	public const DEFAULT_NAME = \bora\NAME . " Sunucu";
	public const DEFAULT_PORT = 19132;
	public const DEFAULT_PLAYERS = 20;
	public const DEFAULT_GAMEMODE = 0;

	/** @var BaseLang */
	private $lang;

	public function __construct(){

	}

	public function run() : bool{
		$this->message(\bora\NAME . " kurulum sihirbazı");

		$langs = BaseLang::getLanguageList();
		if(empty($langs)){
			$this->error("Dil dosyası bulunamadı, lütfen sağlanan yapıları kullanın veya depoyu tekrar tekrar klonlayın.");
			return false;
		}

		$this->message("* Lütfen Dil seçin *");
		foreach($langs as $short => $native){
			$this->writeLine(" $native => $short");
		}

		do{
			$lang = strtolower($this->getInput("Language", "tur"));
			if(!isset($langs[$lang])){
				$this->error("Seçilen Dil Bulunamadı");
				$lang = null;
			}
		}while($lang === null);

		$this->lang = new BaseLang($lang);

		$this->message($this->lang->get("language_has_been_selected"));

		if(!$this->showLicense()){
			return false;
		}

		//this has to happen here to prevent user avoiding agreeing to license
		$config = new Config(\bora\DATA . "server.properties", Config::PROPERTIES);
		$config->set("language", $lang);
		$config->save();

		if(strtolower($this->getInput($this->lang->get("skip_installer"), "n", "y/N")) === "y"){
			return true;
		}

		$this->writeLine();
		$this->welcome();
		$this->generateBaseConfig();
		$this->generateUserFiles();

		$this->networkFunctions();

		$this->endWizard();

		return true;
	}

	private function showLicense() : bool{
		$this->message($this->lang->translateString("welcome_to_bora", [\bora\NAME]));
		echo <<<LICENSE

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Lesser General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

LICENSE;
		$this->writeLine();
		if(strtolower($this->getInput($this->lang->get("accept_license"), "n", "y/N")) !== "y"){
			$this->error($this->lang->translateString("you_have_to_accept_the_license", [\bora\NAME]));
			sleep(5);

			return false;
		}

		return true;
	}

	private function welcome(){
		$this->message($this->lang->get("setting_up_server_now"));
		$this->message($this->lang->get("default_values_info"));
		$this->message($this->lang->get("server_properties"));
	}

	private function generateBaseConfig(){
		$config = new Config(\bora\DATA . "server.properties", Config::PROPERTIES);

		$config->set("motd", ($name = $this->getInput($this->lang->get("name_your_server"), self::DEFAULT_NAME)));
		$config->set("server-name", $name);

		$this->message($this->lang->get("port_warning"));

		do{
			$port = (int) $this->getInput($this->lang->get("server_port"), (string) self::DEFAULT_PORT);
			if($port <= 0 or $port > 65535){
				$this->error($this->lang->get("invalid_port"));
				continue;
			}

			break;
		}while(true);
		$config->set("server-port", $port);

		$this->message($this->lang->get("gamemode_info"));

		do{
			$gamemode = (int) $this->getInput($this->lang->get("default_gamemode"), (string) self::DEFAULT_GAMEMODE);
		}while($gamemode < 0 or $gamemode > 3);
		$config->set("gamemode", $gamemode);

		$config->set("max-players", (int) $this->getInput($this->lang->get("max_players"), (string) self::DEFAULT_PLAYERS));

		$this->message($this->lang->get("spawn_protection_info"));

		if(strtolower($this->getInput($this->lang->get("spawn_protection"), "y", "Y/n")) === "n"){
			$config->set("spawn-protection", -1);
		}else{
			$config->set("spawn-protection", 16);
		}

		$config->save();
	}

	private function generateUserFiles(){
		$this->message($this->lang->get("op_info"));

		$op = strtolower($this->getInput($this->lang->get("op_who"), ""));
		if($op === ""){
			$this->error($this->lang->get("op_warning"));
		}else{
			$ops = new Config(\bora\DATA . "ops.txt", Config::ENUM);
			$ops->set($op, true);
			$ops->save();
		}

		$this->message($this->lang->get("whitelist_info"));

		$config = new Config(\bora\DATA . "server.properties", Config::PROPERTIES);
		if(strtolower($this->getInput($this->lang->get("whitelist_enable"), "n", "y/N")) === "y"){
			$this->error($this->lang->get("whitelist_warning"));
			$config->set("white-list", true);
		}else{
			$config->set("white-list", false);
		}
		$config->save();
	}

	private function networkFunctions(){
		$config = new Config(\bora\DATA . "server.properties", Config::PROPERTIES);
		$this->error($this->lang->get("query_warning1"));
		$this->error($this->lang->get("query_warning2"));
		if(strtolower($this->getInput($this->lang->get("query_disable"), "n", "y/N")) === "y"){
			$config->set("enable-query", false);
		}else{
			$config->set("enable-query", true);
		}

		$this->message($this->lang->get("rcon_info"));
		if(strtolower($this->getInput($this->lang->get("rcon_enable"), "n", "y/N")) === "y"){
			$config->set("enable-rcon", true);
			$password = substr(base64_encode(random_bytes(20)), 3, 10);
			$config->set("rcon.password", $password);
			$this->message($this->lang->get("rcon_password") . ": " . $password);
		}else{
			$config->set("enable-rcon", false);
		}

		$config->save();


		$this->message($this->lang->get("ip_get"));

		$externalIP = Internet::getIP();
		if($externalIP === false){
			$externalIP = "unknown (server offline)";
		}
		try{
			$internalIP = Internet::getInternalIP();
		}catch(InternetException $e){
			$internalIP = "unknown (" . $e->getMessage() . ")";
		}

		$this->error($this->lang->translateString("ip_warning", ["EXTERNAL_IP" => $externalIP, "INTERNAL_IP" => $internalIP]));
		$this->error($this->lang->get("ip_confirm"));
		$this->readLine();
	}

	private function endWizard(){
		$this->message($this->lang->get("you_have_finished"));
		$this->message($this->lang->get("bora_plugins"));
		$this->message($this->lang->translateString("bora_will_start", [\bora\NAME]));

		$this->writeLine();
		$this->writeLine();

		sleep(4);
	}

	private function writeLine(string $line = ""){
		echo $line . PHP_EOL;
	}

	private function readLine() : string{
		return trim((string) fgets(STDIN));
	}

	private function message(string $message){
		$this->writeLine("[*] " . $message);
	}

	private function error(string $message){
		$this->writeLine("[!] " . $message);
	}

	private function getInput(string $message, string $default = "", string $options = "") : string{
		$message = "[?] " . $message;

		if($options !== "" or $default !== ""){
			$message .= " (" . ($options === "" ? $default : $options) . ")";
		}
		$message .= ": ";

		echo $message;

		$input = $this->readLine();

		return $input === "" ? $default : $input;
	}
}
