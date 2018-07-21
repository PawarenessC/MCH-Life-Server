<?php

namespace machi;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\utils\Config;

class warn extends PluginBase{

	const RELEASE = 0;
	const YELLOW_WARNING = 1;
	const ORANGE_WARNING = 2;                        
	const RED_WARNING = 3;

	public function onEnable(): void{
		$this->getLogger()->info("§a[起動] §bWarningPlugin§aを起動しました。");

		new EventListener($this);

		if (!file_exists($this->getDataFolder())){
            @mkdir($this->getDataFolder());
        }
		$this->list = new Config($this->getDataFolder() . "warn.yml", Config::YAML);
	}


	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		if (!isset($args[0]) || !isset($args[1])) return false;

		switch ($command->getName()){
			case "/warn":			
			$target = $args[0];
			$type = $args[1];

			if (!($type === self::RELEASE || self::YELLOW_WARNING || self::ORANGE_WARNING || self::RED_WARNING)) return false;
			
			$this->warning($target, $type, $sender);
			return true;
		}
	}

	public function warning(string $target, int $type, CommandSender $sender): void{
		if ($sender instanceof Player){
			$sender_name = $sender->getName();
		}
		else{
			$sender_name = "管理者";
		}
		$this->list->set($target, $type);
		$this->list->save();

		switch ($type){
			case self::RELEASE:
				$color = "§r";
				$message = $sender_name . " が " . $target . " の警告を解除しました。";
				break;
			case self::YELLOW_WARNING:
				$color = "§e⚠§r";
				$message = $sender_name . " が " . $target . " に警告1を付与しました。";
				break;
			case self::ORANGE_WARNING:
				$color = "§6⚠§r";
				$message = $sender_name . " が " . $target . " に警告2を付与しました。"; 
				break;
			case self::RED_WARNING:
				$color = "§c⚠§r";
				$message = $sender_name . " が " . $target . " に警告3を付与しました。"; 
				break;
		}
		$player = $this->getServer()->getPlayer($target);
		if (!($player === null)){
			$player->setNameTag($color . $player->getNameTag());
		}
		$this->getServer()->broadcastMessage($message);
		$player->setDisplayName($color . $player->getDisplayName());
	}

	public function onDisable(): void{
		$this->getLogger()->info("§c[終了] §bWarningPlugin§aを終了しています...");
	}
}
