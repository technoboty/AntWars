<?php

namespace antwars2;

use antwars2\Main;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\World;
use pocketmine\world\Position;

class Arena implements Listener{

    public $plugin;

    private $phase = 0;
    private $game_type = 0;
    private $loot = 0;
    public $players = 0;

    public $choice;
    public $world;

    const PHASE_LOBBY = 0;
    const PHASE_GAME = 1;
    const PHASE_LIGHT1 = 2;
    const PHASE_LIGHT2 = 3;
    const PHASE_LIGHT3 = 4;
    const PHASE_DEATHMATH = 5;

    public function __construct(Main $plugin,World $world,$choice){
        $this->plugin = $plugin;
        $this->world = $world;
        $this->choice = $choice;
    }
    public function onJoin(Player $player){
        $this->players++;
        $position = new Position(256, 95, 256, $this->world);
        $this->world->loadChunk(256, 256);
        $player->teleport($position);
    }
    public function onDisconnect(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        $level = $event->getPlayer()->getWorld()->getFolderName();
        if($level == $this->world->getFolderName()){
            $this->players--;
            $player->setSpawn(Server::getInstance()->getWorldManager()->getWorldByName("world")->getSpawnLocation());
            $player->getInventory()->clearAll();
        }
    }
    public function onDeathPlayers(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        $nick = strtolower($event->getPlayer()->getName());
        $level = $event->getPlayer()->getWorld()->getFolderName();
        if($level == $this->world->getFolderName()){
            $this->players--;
            $player->sendTitle("Вы проиграли!");
            $player->getInventory()->clearAll();
            $world_2 = $this->plugin->getServer()->getWorldManager()->getWorldByName("world");
            $position = new Position(-270, 44, -673, $world_2);
            $world_2->loadChunk(-270, -673);
            $player->teleport($position);
        }
    }
    public function onBreak(BlockBreakEvent $event){ #доделать!
        $id = $event->getBlock()->getId();
        $pos = $event->getBlock()->getPosition();
        if ($this->choice == 1 || $this->choice == 2) {
            if ($event->getBlock()->getPosition()) {
                if ($id == 14) {
                    $drops[] = new Item(Item::get(266, 0, 1));
                    $event->setDrops($drops);
                } elseif ($id == 15) {
                    $drops[] = new Item(Item::get(265, 0, 1));
                    $event->setDrops($drops);
                } elseif ($id == 12) {
                    $rand = mt_rand(1, 2);
                    if ($rand == 1) {
                        $drops[] = new Item(Item::get(288, 0, 1));
                        $event->setDrops($drops);
                    } else {
                        $drops[] = new Item(Item::get(46, 0, 1));
                        $event->setDrops($drops);
                    }
                }
            }
        }
    }
}