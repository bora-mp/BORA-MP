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


namespace bora\updater;


use bora\scheduler\AsyncTask;
use bora\Server;
use bora\utils\Internet;
use function is_array;
use function json_decode;

class UpdateCheckTask extends AsyncTask{

	/** @var string */
	private $endpoint;
	/** @var string */
	private $channel;
	/** @var string */
	private $error = "Unknown error";

	public function __construct(string $endpoint, string $channel){
		$this->endpoint = $endpoint;
		$this->channel = $channel;
	}

	public function onRun(){
		$error = "";
		$response = Internet::getURL($this->endpoint . "?channel=" . $this->channel, 4, [], $error);
		$this->error = $error;

		if($response !== false){
			$response = json_decode($response, true);
			if(is_array($response)){
				if(
					isset($response["base_version"]) and
					isset($response["is_dev"]) and
					isset($response["build"]) and
					isset($response["date"]) and
					isset($response["download_url"])
				){
					$response["details_url"] = $response["details_url"] ?? null;
					$this->setResult($response);
				}elseif(isset($response["error"])){
					$this->error = $response["error"];
				}else{
					$this->error = "Invalid response data";
				}
			}else{
				$this->error = "Invalid response data";
			}
		}
	}

	public function onCompletion(Server $server){
		if($this->error !== ""){
			$server->getLogger()->debug("[AutoUpdater] Async update check failed due to \"$this->error\"");
		}else{
			$updateInfo = $this->getResult();
			if(is_array($updateInfo)){
				$server->getUpdater()->checkUpdateCallback($updateInfo);
			}else{
				$server->getLogger()->debug("[AutoUpdater] Update info error");
			}

		}
	}
}
