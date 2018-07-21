<?php

namespace ServerMessage;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;

class Main extends PluginBase implements Listener{

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("§a[起動] §bServerMessage§aを起動しました。");
	}

	public function onDisable() {
		$this->getLogger()->info("§c[終了] §bServerMessage§aを終了しています...");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) :bool{
		switch (strtolower($command->getName())) {
			case "sevm":
			if(!isset($args[0])) return false;
			
			$this->getServer()->broadcastMessage("§l§a" . $args[0]);

			return true;
		}
	}
}