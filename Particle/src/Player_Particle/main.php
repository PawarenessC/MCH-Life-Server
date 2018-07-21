<?php

namespace Player_Particle;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\FlameParticle;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§a[起動] §bParticle_Player§aを起動しました。");
	}

	public function onDisable() {
		$this->getLogger()->info("§c[終了] §bParticle_Player§aを終了しています...");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {

		if(!$sender instanceof Player) {
			$this->getLogger()->warning("§cゲーム内で実行して下さい。");
			return true;
		}
		
		switch (strtolower($command->getName())) {

			case "pte":

				$sender->sendMessage("§f=====§bpte §a(Particle)§bコマンドの使用方法§f=====");
				$sender->sendMessage("§a/pte <0,1,2,3,4,5> <プレイヤー名>: §b番号指定");
				$sender->sendMessage("§a0 §f- §e削除");
				$sender->sendMessage("§a1 §f- §bDustParticle: §bほこり");
				$sender->sendMessage("§a2 §f- §bExplodeParticle: §e煙");
				$sender->sendMessage("§a3 §f- §bPortalParticle: §eポータル");
				$sender->sendMessage("§a4 §f- §bHappyVillagerParticle: §e緑の光");
				$sender->sendMessage("§a5 §f- §bFlameParticle: §e炎");

				if (isset($args[0])) {

					$level = $sender->getLevel();
					$x = $sender->getX();
					$y = $sender->getY();
					$z = $sender->getZ();

				switch ($args[0]) {
					
					case "0":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 0;
					$particle = new SmokeParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;

					case "1":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$particle = new DustParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;

					case "2":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$particle = new ExplodeParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;
					
					case "3":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$particle = new PortalParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;
					
					case "4":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$particle = new HappyVillagerParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;
					
					case "5":
					$center = new Vector3($x, $y, $z);
					$radius = 0.5;
					$count = 100;
					$particle = new FlameParticle($center, mt_rand(), mt_rand(), mt_rand(), mt_rand());
					for($yaw = 0, $y = $center->y; $y < $center->y + 4; $yaw += (M_PI * 2) / 20, $y += 1 / 20){
						$x = -sin($yaw) + $center->x;
						$z = cos($yaw) + $center->z;
						$particle->setComponents($x, $y, $z);
						$level->addParticle($particle);
					}
					break;
										
				}
			}
		}return true;
	}
}