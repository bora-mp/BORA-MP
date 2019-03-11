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

use bora\block\BlockFactory;
use bora\command\CommandSender;
use bora\command\utils\InvalidCommandSyntaxException;
use bora\item\Item;
use bora\item\ItemFactory;
use bora\lang\TranslationContainer;
use bora\level\Level;
use bora\level\particle\AngryVillagerParticle;
use bora\level\particle\BlockForceFieldParticle;
use bora\level\particle\BubbleParticle;
use bora\level\particle\CriticalParticle;
use bora\level\particle\DustParticle;
use bora\level\particle\EnchantmentTableParticle;
use bora\level\particle\EnchantParticle;
use bora\level\particle\ExplodeParticle;
use bora\level\particle\FlameParticle;
use bora\level\particle\HappyVillagerParticle;
use bora\level\particle\HeartParticle;
use bora\level\particle\HugeExplodeParticle;
use bora\level\particle\HugeExplodeSeedParticle;
use bora\level\particle\InkParticle;
use bora\level\particle\InstantEnchantParticle;
use bora\level\particle\ItemBreakParticle;
use bora\level\particle\LavaDripParticle;
use bora\level\particle\LavaParticle;
use bora\level\particle\Particle;
use bora\level\particle\PortalParticle;
use bora\level\particle\RainSplashParticle;
use bora\level\particle\RedstoneParticle;
use bora\level\particle\SmokeParticle;
use bora\level\particle\SplashParticle;
use bora\level\particle\SporeParticle;
use bora\level\particle\TerrainParticle;
use bora\level\particle\WaterDripParticle;
use bora\level\particle\WaterParticle;
use bora\math\Vector3;
use bora\Player;
use bora\utils\Random;
use bora\utils\TextFormat;
use function count;
use function explode;
use function max;
use function microtime;
use function mt_rand;
use function strpos;
use function strtolower;

class ParticleCommand extends VanillaCommand{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"%bora.command.particle.description",
			"%bora.command.particle.usage"
		);
		$this->setPermission("bora.command.particle");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(count($args) < 7){
			throw new InvalidCommandSyntaxException();
		}

		if($sender instanceof Player){
			$level = $sender->getLevel();
			$pos = new Vector3(
				$this->getRelativeDouble($sender->getX(), $sender, $args[1]),
				$this->getRelativeDouble($sender->getY(), $sender, $args[2], 0, Level::Y_MAX),
				$this->getRelativeDouble($sender->getZ(), $sender, $args[3])
			);
		}else{
			$level = $sender->getServer()->getDefaultLevel();
			$pos = new Vector3((float) $args[1], (float) $args[2], (float) $args[3]);
		}

		$name = strtolower($args[0]);

		$xd = (float) $args[4];
		$yd = (float) $args[5];
		$zd = (float) $args[6];

		$count = isset($args[7]) ? max(1, (int) $args[7]) : 1;

		$data = isset($args[8]) ? (int) $args[8] : null;

		$particle = $this->getParticle($name, $pos, $xd, $yd, $zd, $data);

		if($particle === null){
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.particle.notFound", [$name]));
			return true;
		}


		$sender->sendMessage(new TranslationContainer("commands.particle.success", [$name, $count]));

		$random = new Random((int) (microtime(true) * 1000) + mt_rand());

		for($i = 0; $i < $count; ++$i){
			$particle->setComponents(
				$pos->x + $random->nextSignedFloat() * $xd,
				$pos->y + $random->nextSignedFloat() * $yd,
				$pos->z + $random->nextSignedFloat() * $zd
			);
			$level->addParticle($particle);
		}

		return true;
	}

	/**
	 * @param string   $name
	 * @param Vector3  $pos
	 * @param float    $xd
	 * @param float    $yd
	 * @param float    $zd
	 * @param int|null $data
	 *
	 * @return Particle|null
	 */
	private function getParticle(string $name, Vector3 $pos, float $xd, float $yd, float $zd, int $data = null){
		switch($name){
			case "explode":
				return new ExplodeParticle($pos);
			case "hugeexplosion":
				return new HugeExplodeParticle($pos);
			case "hugeexplosionseed":
				return new HugeExplodeSeedParticle($pos);
			case "bubble":
				return new BubbleParticle($pos);
			case "splash":
				return new SplashParticle($pos);
			case "wake":
			case "water":
				return new WaterParticle($pos);
			case "crit":
				return new CriticalParticle($pos);
			case "smoke":
				return new SmokeParticle($pos, $data ?? 0);
			case "spell":
				return new EnchantParticle($pos);
			case "instantspell":
				return new InstantEnchantParticle($pos);
			case "dripwater":
				return new WaterDripParticle($pos);
			case "driplava":
				return new LavaDripParticle($pos);
			case "townaura":
			case "spore":
				return new SporeParticle($pos);
			case "portal":
				return new PortalParticle($pos);
			case "flame":
				return new FlameParticle($pos);
			case "lava":
				return new LavaParticle($pos);
			case "reddust":
				return new RedstoneParticle($pos, $data ?? 1);
			case "snowballpoof":
				return new ItemBreakParticle($pos, ItemFactory::get(Item::SNOWBALL));
			case "slime":
				return new ItemBreakParticle($pos, ItemFactory::get(Item::SLIMEBALL));
			case "itembreak":
				if($data !== null and $data !== 0){
					return new ItemBreakParticle($pos, ItemFactory::get($data));
				}
				break;
			case "terrain":
				if($data !== null and $data !== 0){
					return new TerrainParticle($pos, BlockFactory::get($data));
				}
				break;
			case "heart":
				return new HeartParticle($pos, $data ?? 0);
			case "ink":
				return new InkParticle($pos, $data ?? 0);
			case "droplet":
				return new RainSplashParticle($pos);
			case "enchantmenttable":
				return new EnchantmentTableParticle($pos);
			case "happyvillager":
				return new HappyVillagerParticle($pos);
			case "angryvillager":
				return new AngryVillagerParticle($pos);
			case "forcefield":
				return new BlockForceFieldParticle($pos, $data ?? 0);

		}

		if(strpos($name, "iconcrack_") === 0){
			$d = explode("_", $name);
			if(count($d) === 3){
				return new ItemBreakParticle($pos, ItemFactory::get((int) $d[1], (int) $d[2]));
			}
		}elseif(strpos($name, "blockcrack_") === 0){
			$d = explode("_", $name);
			if(count($d) === 2){
				return new TerrainParticle($pos, BlockFactory::get(((int) $d[1]) & 0xff, ((int) $d[1]) >> 12));
			}
		}elseif(strpos($name, "blockdust_") === 0){
			$d = explode("_", $name);
			if(count($d) >= 4){
				return new DustParticle($pos, ((int) $d[1]) & 0xff, ((int) $d[2]) & 0xff, ((int) $d[3]) & 0xff, isset($d[4]) ? ((int) $d[4]) & 0xff : 255);
			}
		}

		return null;
	}
}
