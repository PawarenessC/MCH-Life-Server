<?php

namespace machi;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class Main extends PluginBase implements Listener{

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	    $this->getLogger()->info("§a[起動] §bNoName§aを起動しました。");
	    $this->getLogger()->warning("§c改造や、二次配布は禁止です。 §bBy まっちだよ～(｡･ω･｡)");
	}

	public function onDisable(){
		$this->getLogger()->info("§c[終了] §bNoName§aを終了しています...");
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		if(!$sender instanceof Player) {
			$this->getLogger()->warning("§cゲーム内で実行して下さい。");
			return true;
		}
		switch (strtolower($command->getName())) {
			case "noname":
			$player = $this->getServer()->getOnlinePlayers();
			if ($sender->getNameTag() === "") {
				foreach ($player as $player) {
					$this->getLogger()->info("§l§bNoName §aが§e無効§aになりました。");
					$player->sendMessage("§l§bＮｏＮａｍｅ §aが§e無効§aになりました。");
					$player->sendMessage("");
					$player->sendMessage("§l§b称号§aは元に戻っていません。");
					$player->sendMessage("§l§b称号§aを表示させるには§eリログ§aする必要があります。");
					$player->setNameTag($player->getName());
				}
			}else {
				foreach ($player as $player) {
					$this->getLogger()->info("§l§bNoName §aが§e有効§aになりました。");
					$player->sendMessage("§l§bＮｏＮａｍｅ §aが§e有効§aになりました。");
					$player->setNameTag("");
				}
			}
			break;
			
		}return true;
	}
}