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

namespace {
	const INT32_MIN = -0x80000000;
	const INT32_MAX = 0x7fffffff;
}

namespace bora {

	use bora\utils\MainLogger;
	use bora\utils\ServerKiller;
	use bora\utils\Terminal;
	use bora\utils\Timezone;
	use bora\utils\Utils;
	use bora\utils\VersionString;
	use bora\wizard\SetupWizard;

	const NAME = "BORA-MP";
	const BASE_VERSION = "3.6.5";
	const IS_DEVELOPMENT_BUILD = true;
	const BUILD_NUMBER = 1746;
	const CODE_NAME = "[BETA]";

	const MIN_PHP_VERSION = "7.2.0";

	function critical_error($message){
		echo "[ERROR] $message" . PHP_EOL;
	}

	/*
	 * Startup code. Do not look at it, it may harm you.
	 * This is the only non-class based file on this project.
	 * Enjoy it as much as I did writing it. I don't want to do it again.
	 */

	/**
	 * @return string[]
	 */
	function check_platform_dependencies(){
		if(version_compare(MIN_PHP_VERSION, PHP_VERSION) > 0){
			//If PHP version isn't high enough, anything below might break, so don't bother checking it.
			return [
				\bora\NAME . " requires PHP >= " . MIN_PHP_VERSION . ", but you have PHP " . PHP_VERSION . "."
			];
		}

		$messages = [];

		if(PHP_INT_SIZE < 8){
			$messages[] = \bora\NAME . " 32-bit sistem ile çalışıyor/PHP desteklenmiyor. Lütfen sisteminizi 64-bit sisteme güncelleyin, yada 64-bit binary kullanın.";
		}

		if(php_sapi_name() !== "cli"){
			$messages[] = "You must run " . \bora\NAME . " using the CLI.";
		}

		$extensions = [
			"bcmath" => "BC Math",
			"curl" => "cURL",
			"ctype" => "ctype",
			"date" => "Date",
			"hash" => "Hash",
			"json" => "JSON",
			"mbstring" => "Multibyte String",
			"openssl" => "OpenSSL",
			"pcre" => "PCRE",
			"phar" => "Phar",
			"pthreads" => "pthreads",
			"reflection" => "Reflection",
			"sockets" => "Sockets",
			"spl" => "SPL",
			"yaml" => "YAML",
			"zip" => "Zip",
			"zlib" => "Zlib"
		];

		foreach($extensions as $ext => $name){
			if(!extension_loaded($ext)){
				$messages[] = "Unable to find the $name ($ext) extension.";
			}
		}

		if(extension_loaded("pthreads")){
			$pthreads_version = phpversion("pthreads");
			if(substr_count($pthreads_version, ".") < 2){
				$pthreads_version = "0.$pthreads_version";
			}
			if(version_compare($pthreads_version, "3.1.7dev") < 0){
				$messages[] = "pthreads >= 3.1.7dev is required, while you have $pthreads_version.";
			}
		}

		if(extension_loaded("leveldb")){
			$leveldb_version = phpversion("leveldb");
			if(version_compare($leveldb_version, "0.2.1") < 0){
				$messages[] = "php-leveldb >= 0.2.1 is required, while you have $leveldb_version.";
			}
		}

		if(extension_loaded("bora")){
			$messages[] = "The native PocketMine extension is no longer supported.";
		}

		return $messages;
	}

	if(!empty($messages = check_platform_dependencies())){
		echo PHP_EOL;
		$binary = version_compare(PHP_VERSION, "5.4") >= 0 ? PHP_BINARY : "unknown";
		critical_error("Selected PHP binary ($binary) does not satisfy some requirements.");
		foreach($messages as $m){
			echo " - $m" . PHP_EOL;
		}
		critical_error("Please recompile PHP with the needed configuration, or refer to the installation instructions at http://pmmp.rtfd.io/en/rtfd/installation.html.");
		echo PHP_EOL;
		exit(1);
	}
	unset($messages);

	error_reporting(-1);

	if(\Phar::running(true) !== ""){
		define('bora\PATH', \Phar::running(true) . "/");
	}else{
		define('bora\PATH', dirname(__FILE__, 3) . DIRECTORY_SEPARATOR);
	}

	$opts = getopt("", ["bootstrap:"]);
	if(isset($opts["bootstrap"])){
		$bootstrap = realpath($opts["bootstrap"]) ?: $opts["bootstrap"];
	}else{
		$bootstrap = \bora\PATH . 'vendor/autoload.php';
	}
	define('bora\COMPOSER_AUTOLOADER_PATH', $bootstrap);

	if(\bora\COMPOSER_AUTOLOADER_PATH !== false and is_file(\bora\COMPOSER_AUTOLOADER_PATH)){
		require_once(\bora\COMPOSER_AUTOLOADER_PATH);
	}else{
		critical_error("Composer autoloader not found at " . $bootstrap);
		critical_error("Please install/update Composer dependencies or use provided builds.");
		exit(1);
	}

	set_error_handler([Utils::class, 'errorExceptionHandler']);

	/*
	 * We now use the Composer autoloader, but this autoloader is still for loading plugins.
	 */
	$autoloader = new \BaseClassLoader();
	$autoloader->register(false);

	set_time_limit(0); //Who set it to 30 seconds?!?!

	ini_set("allow_url_fopen", '1');
	ini_set("display_errors", '1');
	ini_set("display_startup_errors", '1');
	ini_set("default_charset", "utf-8");

	ini_set("memory_limit", '-1');

	define('bora\RESOURCE_PATH', \bora\PATH . 'src' . DIRECTORY_SEPARATOR . 'bora' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);

	$opts = getopt("", ["data:", "plugins:", "no-wizard"]);

	define('bora\DATA', isset($opts["data"]) ? $opts["data"] . DIRECTORY_SEPARATOR : realpath(getcwd()) . DIRECTORY_SEPARATOR);
	define('bora\PLUGIN_PATH', isset($opts["plugins"]) ? $opts["plugins"] . DIRECTORY_SEPARATOR : realpath(getcwd()) . DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR);

	if(!file_exists(\bora\DATA)){
		mkdir(\bora\DATA, 0777, true);
	}

	//Logger has a dependency on timezone
	$tzError = Timezone::init();

	$logger = new MainLogger(\bora\DATA . "server.log");
	$logger->registerStatic();

	foreach($tzError as $e){
		$logger->warning($e);
	}
	unset($tzError);

	if(extension_loaded("xdebug")){
		$logger->warning(PHP_EOL . PHP_EOL . PHP_EOL . "\tYou are running " . \bora\NAME . " with xdebug enabled. This has a major impact on performance." . PHP_EOL . PHP_EOL);
	}

	if(\Phar::running(true) === ""){
		$logger->warning("Non-packaged " . \bora\NAME . " installation detected. Consider using a phar in production for better performance.");
	}

	$version = new VersionString(\bora\BASE_VERSION, \bora\IS_DEVELOPMENT_BUILD, \bora\BUILD_NUMBER);
	define('bora\VERSION', $version->getFullVersion(true));

	$gitHash = str_repeat("00", 20);

	if(\Phar::running(true) === ""){
		if(Utils::execute("git rev-parse HEAD", $out) === 0 and $out !== false and strlen($out = trim($out)) === 40){
			$gitHash = trim($out);
			if(Utils::execute("git diff --quiet") === 1 or Utils::execute("git diff --cached --quiet") === 1){ //Locally-modified
				$gitHash .= "-dirty";
			}
		}
	}else{
		$phar = new \Phar(\Phar::running(false));
		$meta = $phar->getMetadata();
		if(isset($meta["git"])){
			$gitHash = $meta["git"];
		}
	}

	define('bora\GIT_COMMIT', $gitHash);


	@define("INT32_MASK", is_int(0xffffffff) ? 0xffffffff : -1);
	@ini_set("opcache.mmap_base", bin2hex(random_bytes(8))); //Fix OPCache address errors

	$exitCode = 0;
	do{
		if(!file_exists(\bora\DATA . "server.properties") and !isset($opts["no-wizard"])){
			$installer = new SetupWizard();
			if(!$installer->run()){
				$exitCode = -1;
				break;
			}
		}

		//TODO: move this to a Server field
		define('bora\START_TIME', microtime(true));
		ThreadManager::init();
		new Server($autoloader, $logger, \bora\DATA, \bora\PLUGIN_PATH);

		$logger->info("Stopping other threads");

		$killer = new ServerKiller(8);
		$killer->start(PTHREADS_INHERIT_NONE);
		usleep(10000); //Fixes ServerKiller not being able to start on single-core machines

		if(ThreadManager::getInstance()->stopAll() > 0){
			if(\bora\DEBUG > 1){
				echo "Some threads could not be stopped, performing a force-kill" . PHP_EOL . PHP_EOL;
			}
			Utils::kill(getmypid());
		}
	}while(false);

	$logger->shutdown();
	$logger->join();

	echo Terminal::$FORMAT_RESET . PHP_EOL;

	exit($exitCode);
}
