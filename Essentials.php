<?php

/*
__PocketMine Plugin__
name=Essentials
description=Essentials
version=0.0.1
author=KsyMC
class=Essentials
apiversion=7
*/

class Essentials implements Plugin{
	private $api, $forge;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function __destruct(){}
	
	public function init(){
		$this->api->event("server.close", array($this, "handler"));
		$this->api->addHandler("plugin.forge.api", array($this, "handler"), 1);
		
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("player.quit", array($this, "handler"), 5);
		$this->api->addHandler("player.flying", array($this, "handler"), 5);
		$this->api->addHandler("player.move", array($this, "handler"), 5);
		$this->api->addHandler("player.death", array($this, "handler"), 5);
		$this->api->addHandler("player.teleport", array($this, "handler"), 5);
		$this->api->addHandler("player.block.place", array($this, "handler"), 5);
		$this->api->addHandler("player.block.break", array($this, "handler"), 5);
		$this->api->addHandler("player.block.place.spawn", array($this, "handler"), 5);
		$this->api->addHandler("player.block.break.spawn", array($this, "handler"), 5);
		$this->path = $this->api->plugin->createConfig($this, array(
			"chat-format" => "<{DISPLAYNAME}> {MESSAGE}",
			"login-after-commands" => array(
				"help",
				"say",
				"login",
				"register",
			),
			"login-after-move" => true,
			"allow-explosion" => false,
			"blacklist" => array(
				"placement" => '8,9,10,11,46,95',
				"usage" => 327,
				"break" => 7,
			),
			"blocklog-displays" => 5,
			"creative-item" => array(
				COBBLESTONE.":0",
				STONE_BRICKS.":0",
				STONE_BRICKS.":1",
				STONE_BRICKS.":2",
				MOSS_STONE.":0",
				WOODEN_PLANKS.":0",
				BRICKS.":0",
				STONE.":0",
				DIRT.":0",
				GRASS.":0",
				CLAY_BLOCK.":0",
				SANDSTONE.":0",
				SANDSTONE.":1",
				SANDSTONE.":2",
				SAND.":0",
				GRAVEL.":0",
				TRUNK.":0",
				TRUNK.":1",
				TRUNK.":2",
				NETHER_BRICKS.":0",
				NETHERRACK.":0",
				COBBLESTONE_STAIRS.":0",
				WOODEN_STAIRS.":0",
				BRICK_STAIRS.":0",
				SANDSTONE_STAIRS.":0",
				STONE_BRICK_STAIRS.":0",
				NETHER_BRICKS_STAIRS.":0",
				QUARTZ_STAIRS.":0",
				SLAB.":0",
				SLAB.":1",
				SLAB.":2",
				SLAB.":3",
				SLAB.":4",
				SLAB.":5",
				QUARTZ_BLOCK.":0",
				QUARTZ_BLOCK.":1",
				QUARTZ_BLOCK.":2",
				COAL_ORE.":0",
				IRON_ORE.":0",
				GOLD_ORE.":0",
				DIAMOND_ORE.":0",
				LAPIS_ORE.":0",
				REDSTONE_ORE.":0",
				GOLD_BLOCK.":0",
				IRON_BLOCK.":0",
				DIAMOND_BLOCK.":0",
				LAPIS_BLOCK.":0",
				OBSIDIAN.":0",
				SNOW_BLOCK.":0",
				GLASS.":0",
				GLOWSTONE_BLOCK.":0",
				NETHER_REACTOR.":0",
				WOOL.":0",
				WOOL.":7",
				WOOL.":6",
				WOOL.":5",
				WOOL.":4",
				WOOL.":3",
				WOOL.":2",
				WOOL.":1",
				WOOL.":15",
				WOOL.":14",
				WOOL.":13",
				WOOL.":12",
				WOOL.":11",
				WOOL.":10",
				WOOL.":9",
				WOOL.":8",
				LADDER.":0",
				TORCH.":0",
				GLASS_PANE.":0",
				WOODEN_DOOR.":0",
				TRAPDOOR.":0",
				FENCE.":0",
				FENCE_GATE.":0",
				BED.":0",
				BOOKSHELF.":0",
				PAINTING.":0",
				WORKBENCH.":0",
				STONECUTTER.":0",
				CHEST.":0",
				FURNACE.":0",
				TNT.":0",
				DANDELION.":0",
				CYAN_FLOWER.":0",
				BROWN_MUSHROOM.":0",
				RED_MUSHROOM.":0",
				CACTUS.":0",
				MELON_BLOCK.":0",
				SUGARCANE.":0",
				SAPLING.":0",
				SAPLING.":1",
				SAPLING.":2",
				LEAVES.":0",
				LEAVES.":1",
				LEAVES.":2",
				SEEDS.":0",
				MELON_SEEDS.":0",
				DYE.":15",
				IRON_HOE.":0",
				IRON_SWORD.":0",
				BOW.":0",
				SIGN.":0",
			),
			"creative-item-op" => array(
				COBBLESTONE.":0",
				STONE_BRICKS.":0",
				STONE_BRICKS.":1",
				STONE_BRICKS.":2",
				MOSS_STONE.":0",
				WOODEN_PLANKS.":0",
				BRICKS.":0",
				STONE.":0",
				DIRT.":0",
				GRASS.":0",
				CLAY_BLOCK.":0",
				SANDSTONE.":0",
				SANDSTONE.":1",
				SANDSTONE.":2",
				SAND.":0",
				GRAVEL.":0",
				TRUNK.":0",
				TRUNK.":1",
				TRUNK.":2",
				NETHER_BRICKS.":0",
				NETHERRACK.":0",
				COBBLESTONE_STAIRS.":0",
				WOODEN_STAIRS.":0",
				BRICK_STAIRS.":0",
				SANDSTONE_STAIRS.":0",
				STONE_BRICK_STAIRS.":0",
				NETHER_BRICKS_STAIRS.":0",
				QUARTZ_STAIRS.":0",
				SLAB.":0",
				SLAB.":1",
				SLAB.":2",
				SLAB.":3",
				SLAB.":4",
				SLAB.":5",
				QUARTZ_BLOCK.":0",
				QUARTZ_BLOCK.":1",
				QUARTZ_BLOCK.":2",
				COAL_ORE.":0",
				IRON_ORE.":0",
				GOLD_ORE.":0",
				DIAMOND_ORE.":0",
				LAPIS_ORE.":0",
				REDSTONE_ORE.":0",
				GOLD_BLOCK.":0",
				IRON_BLOCK.":0",
				DIAMOND_BLOCK.":0",
				LAPIS_BLOCK.":0",
				OBSIDIAN.":0",
				SNOW_BLOCK.":0",
				GLASS.":0",
				GLOWSTONE_BLOCK.":0",
				NETHER_REACTOR.":0",
				WOOL.":0",
				WOOL.":7",
				WOOL.":6",
				WOOL.":5",
				WOOL.":4",
				WOOL.":3",
				WOOL.":2",
				WOOL.":1",
				WOOL.":15",
				WOOL.":14",
				WOOL.":13",
				WOOL.":12",
				WOOL.":11",
				WOOL.":10",
				WOOL.":9",
				WOOL.":8",
				LADDER.":0",
				TORCH.":0",
				GLASS_PANE.":0",
				WOODEN_DOOR.":0",
				TRAPDOOR.":0",
				FENCE.":0",
				FENCE_GATE.":0",
				BED.":0",
				BOOKSHELF.":0",
				PAINTING.":0",
				WORKBENCH.":0",
				STONECUTTER.":0",
				CHEST.":0",
				FURNACE.":0",
				TNT.":0",
				DANDELION.":0",
				CYAN_FLOWER.":0",
				BROWN_MUSHROOM.":0",
				RED_MUSHROOM.":0",
				CACTUS.":0",
				MELON_BLOCK.":0",
				SUGARCANE.":0",
				SAPLING.":0",
				SAPLING.":1",
				SAPLING.":2",
				LEAVES.":0",
				LEAVES.":1",
				LEAVES.":2",
				SEEDS.":0",
				MELON_SEEDS.":0",
				DYE.":15",
				IRON_HOE.":0",
				IRON_SWORD.":0",
				BOW.":0",
				SIGN.":0",
			),
		));
		$this->config = $this->api->plugin->readYAML($this->path."config.yml");
	}
	
	public function handler(&$data, $event){
		switch($event){
			case "player.join":
				break;
			case "player.move":
				$player = $this->api->player->getByEID($data->eid);
				if($player->__get("lastMovement") < 10){
					$this->initPlayer($player);
				}
				break;
			case "player.flying":
				if($this->api->ban->isOp($data->__get("iusername")) === true){
					return true;
				}
				break;
			case "plugin.forge.api":
				$this->forge = $data;
				$data["CommandAPI"]->register("help", "[page|command name]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("say", "<message ...>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("home", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("sethome", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("delhome", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("mute", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("back", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("tree", "<tree|brich|redwood>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("clear", "", array($this, "defaultCommands"));
				// ConsoleAPI
				$data["CommandAPI"]->register("stop", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("status", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("invisible", "<on | off>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("difficulty", "<0|1|2>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("defaultgamemode", "<mode>", array($this, "defaultCommands"));
				// LevelAPI
				$data["CommandAPI"]->register("seed", "[world]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("save-all", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("save-on", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("save-off", "", array($this, "defaultCommands"));
				// PlayerAPI
				$data["CommandAPI"]->register("list", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("kill", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("gamemode", "<mode> [player]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("tp", "[target player] <destination player|w:world> OR /tp [target player] <x> <y> <z>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("spawnpoint", "[player] [x] [y] [z]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("spawn", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("lag", "", array($this, "defaultCommands"));
				// TimeAPI
				$data["CommandAPI"]->register("time", "<check|set|add> [time]", array($this, "defaultCommands"));
				// BanAPI
				$data["CommandAPI"]->register("banip", "<add|remove|list|reload> [IP|player]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("ban", "<add|remove|list|reload> [username]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("kick", "<player> [reason ...]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("whitelist", "<on|off|list|add|remove|reload> [username]", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("op", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("deop", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("sudo", "<player>", array($this, "defaultCommands"));
				// BlockAPI
				$data["CommandAPI"]->register("give", "<player> <item[:damage]> [amount]", array($this, "defaultCommands"));
				// ChatAPI
				$data["CommandAPI"]->register("tell", "<player> <private message ...>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("me", "<action ...>", array($this, "defaultCommands"));
				break;
			case "player.block.break":
				if($data["target"]->getID() === SIGN_POST or $data["target"]->getID() === WALL_SIGN){
					$t = $this->api->tileentity->get($data["target"]);
					foreach($t as $ts){
						if($ts->class === TILE_SIGN){
							$this->api->tileentity->remove($ts->id);
						}
					}
				}
				break;
			case "player.block.place.spawn":
				if($data["item"]->getID() === SIGN){
					return true;
				}
				break;
			case "player.block.break.spawn":
				if($data["target"]->getID() === SIGN_POST or $data["target"]->getID() === WALL_SIGN){
					return true;
				}
				break;
		}
	}
	
	public function initPlayer($player){
		if($player->gamemode === CREATIVE){
			foreach($player->inventory as $slot => $item){
				if($this->api->ban->isOp($player->__get("iusername"))){
					$player->setSlot($slot, BlockAPI::fromString($this->config["creative-item-op"][$slot]));
				}else{
					$player->setSlot($slot, BlockAPI::fromString($this->config["creative-item"][$slot]));
				}
			}
		}
	}
	
	public function getGM($name){
		$gm["users"] = new Config(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/users.yml", CONFIG_YAML);
		$gm["groups"] = new Config(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/groups.yml", CONFIG_YAML);
		foreach($gm["groups"]->get("groups") as $groupname => $group){
			if($group["default"] === true){
				$defaultgroup = $groupname;
				break;
			}
		}
		if(isset($gm["users"]->get("users")[$name])){
			$gm["users"] = $gm["users"]->get("users")[$name];
			$gm["groups"] = $gm["groups"]->get("groups")[$gm["users"]["group"]];
		}else{
			$gm["users"] = array(
				"group" => $defaultgroup,
				"permissions" => array(),
			);
			$gm["groups"] = $gm["groups"]->get("groups")[$defaultgroup];
		}
		return $gm;
	}
	
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "?":
			case "help":
				$output = $this->forge["CommandAPI"]->getHelp($params, $issuer);
				break;
			case "say":
				$s = implode(" ", $params);
				if(trim($s) == ""){
					$output .= "Usage: /say <message ...>\n";
					break;
				}
				$gm = $this->getGM($issuer->__get("username"));
				$this->api->chat->broadcast(str_replace(array("{DISPLAYNAME}", "{MESSAGE}", "{WORLDNAME}", "{GROUP}"), array($gm["groups"]["info"]["prefix"].$issuer->__get("username").$gm["groups"]["info"]["suffix"], $s, $issuer->level->getName(), $gm["users"]["group"]), $this->config["chat-format"]));
				break;
			case "home":
				break;
			case "sethome":
				break;
			case "delhome":
				break;
			case "mute":
				break;
			case "back":
				break;
			case "tree":
				switch(strtolower($params[0])){
					case "redwood":
						$meta = 1;
						$output .= "Redwood tree spawned.";
						break;
					case "brich":
						$meta = 2;
						$output .= "Brich tree spawned.";
						break;
					case "tree":
						$meta = 0;
						$output .= "Tree spawned.";
						break;
					default:
						$output .= "Usage: /tree <tree|brich|redwood>";
						break 2;
				}
				TreeObject::growTree($issuer->level, new Vector3 ((int)$issuer->entity->x, (int)$issuer->entity->y, (int)$issuer->entity->z), $meta);
				break;
			case "clear":
			case "stop":
			case "status":
			case "invisible":
			case "difficulty":
			case "defaultgamemode":
				$output = $this->api->console->defaultCommands($cmd, $params, $issuer, false);
				break;
			case "seed":
			case "save-all":
			case "save-on":
			case "save-off":
				$output = $this->api->level->commandHandler($cmd, $params, $issuer, false);
				break;
			case "list":
			case "kill":
			case "gamemode":
			case "tp":
			case "spawnpoint":
			case "spawn":
			case "lag":
				$output = $this->api->player->commandHandler($cmd, $params, $issuer, false);
				break;
			case "time":
				$output = $this->api->time->commandHandler($cmd, $params, $issuer, false);
				break;
			case "banip":
			case "ban":
			case "kick":
			case "whitelist":
			case "op":
			case "deop":
			case "sudo":
				$output = $this->api->ban->commandHandler($cmd, $params, $issuer, false);
				break;
			case "give":
				$output = $this->api->block->commandHandler($cmd, $params, $issuer, false);
				break;
			case "tell":
			case "me":
				$output = $this->api->chat->commandHandler($cmd, $params, $issuer, false);
				break;
		}
		return $output;
	}
}