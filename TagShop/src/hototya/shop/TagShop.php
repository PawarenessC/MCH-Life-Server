<?php
namespace hototya\shop;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;

use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\Player;

use onebone\economyapi\EconomyAPI;

class TagShop extends PluginBase implements Listener
{

    const SHOP_TITLE = "§a[TagShop]§f ";

    private $fid;
    private $api;
    private $prebuy = [];
    private $config;
    private $shop;

    public function onEnable()
    {
        if (!file_exists($this->getDataFolder())) mkdir($this->getDataFolder(), 0744, true);
        $this->fid = mt_rand(0, 999999);
        $this->api = EconomyAPI::getInstance();
        $this->config = new Config($this->getDataFolder() . "nametag.json", Config::JSON);
        $this->shop = new Config($this->getDataFolder() . "shop.json", Config::JSON);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        if ($this->config->exists($name)) {
            $tag = $this->config->get($name);
            $player->setNameTag("[" . $tag . "§r§f] " . $name);
            $player->setDisplayName("[" . $tag . "§r§f] " . $name);
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        $blockId = $block->getId();
        if ($blockId === Item::SIGN || $blockId === Block::SIGN_POST || $blockId === Block::WALL_SIGN) {
            $player = $event->getPlayer();
            $sign = $player->getLevel()->getTile($block);
            if (!($sign instanceof Sign)) return;
            $text = $sign->getText();
            if ($text[0] === self::SHOP_TITLE) {
                $pos = $block->x . ":" . $block->y . ":" . $block->z;
                if ($this->shop->exists($pos)) {
                    if ($player->isOp()) {
                        $this->shop->remove($pos);
                        $this->shop->save();
                        $player->sendMessage(self::SHOP_TITLE . "称号ショップを撤去しました。");
                    } else {
                        $event->setCancelled();
                        $player->sendMessage(self::SHOP_TITLE . "§eOP以外壊すことはできません。");
                    }
                }
            }
        }
    }

    public function onTap(PlayerInteractEvent $event)
    {
        $block = $event->getBlock();
        $blockId = $block->getId();
        if ($blockId === Item::SIGN || $blockId === Block::SIGN_POST || $blockId === Block::WALL_SIGN) {
            $player = $event->getPlayer();
            $sign = $player->getLevel()->getTile($block);
            if (!($sign instanceof Sign)) return;
            $text = $sign->getText();
            $pos = $block->x . ":" . $block->y . ":" . $block->z;
            if ($text[0] === self::SHOP_TITLE) {
                if ($this->shop->exists($pos)) {
                    $price = (int) str_replace("§6値段 : ", "", $text[2]);
                    if ($price <= $this->api->myMoney($player)) {
                        $tag = str_replace("§f称号 : ", "", $text[1]);
                        $data = [
                            "type" => "modal",
                            "title" => self::SHOP_TITLE,
                            "content" => "称号 " . $tag . "§f を購入しますか？",
                            "button1" => "はい",
                            "button2" => "いいえ"
                        ];
                        $event->setCancelled();
                        $this->createWindow($player, $data);
                        $this->prebuy[$player->getName()] = [$tag, $price];
                    } else {
                        $event->setCancelled();
                        $player->sendMessage(self::SHOP_TITLE . "お金が足りないため称号の購入はキャンセルされました。");
                    }
                }
            } elseif ($text[0] === "tagshop") {
                if ($player->isOp()) {
                    if (!empty($text[1]) && is_numeric($text[2])) {
                        $sign->setText(self::SHOP_TITLE, "§f称号 : " . $text[1], "§6値段 : " . $text[2], $text[3]);
                        $this->shop->set($pos);
                        $this->shop->save();
                        $player->sendMessage(self::SHOP_TITLE . "称号ショップを作成しました。");
                    } else {
                        $player->sendMessage(self::SHOP_TITLE . "§e書き方が違います。看板の通りに記述してください。");
                        $sign->setText("tagshop", "売る称号", "値段", "コメント（無くてもよい）");
                    }
                }
            }
        }
    }

    public function onReceive(DataPacketReceiveEvent $event)
    {
        $pk = $event->getPacket();
        if ($pk instanceof ModalFormResponsePacket) {
            if ($pk->formId === $this->fid) {
                $data = $pk->formData;
                if ($data === "true\n") {
                    $player = $event->getPlayer();
                    $name = $player->getName();
                    $tag = $this->prebuy[$name][0];
                    $price = $this->prebuy[$name][1];
                    $this->config->set($name, $this->prebuy[$name][0]);
                    $this->config->save();
                    $this->api->reduceMoney($player, $price);
                    $player->setNameTag("[" . $tag . "§f] " . $name);
                    $player->setDisplayName("[" . $tag . "§f] " . $name);
                    $player->sendMessage(self::SHOP_TITLE . "称号の購入が完了しました。");
                    unset($this->prebuy[$name]);
                }
            }
        }
    }

    private function createWindow(Player $player, $data)
    {
        $pk = new ModalFormRequestPacket();
        $pk->formId = $this->fid;
        $pk->formData = json_encode($data, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_UNESCAPED_UNICODE);
        $player->dataPacket($pk);
    }
}
