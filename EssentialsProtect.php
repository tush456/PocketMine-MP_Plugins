<?php

/*
__PocketMine Plugin__
name=EssentialsProtect
description=EssentialsProtect
version=0.0.1
author=KsyMC
class=EssentialsProtect
apiversion=7
*/

class EssentialsProtect implements Plugin{
	private $api, $config, $protect;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->protect = array("chest" => false);
	}
	
	public function __destruct(){}
	
	public function init(){
		$this->api->event("server.close", array($this, "handler"));
		$this->api->addHandler("plugin.forge.api", array($this, "handler"), 1);
		
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("tile.update", array($this, "handler"), 7);
		$this->api->addHandler("player.flying", array($this, "handler"), 7);
		$this->api->addHandler("player.block.break", array($this, "handler"), 7);
		$this->api->addHandler("player.block.place", array($this, "handler"), 7);
		$this->api->addHandler("player.block.touch", array($this, "handler"), 7);
		$this->api->addHandler("player.block.activate", array($this, "handler"), 7);
		
		if(file_exists("./plugins/Essentials/Protectdata.dat")){
			$this->protect = unserialize(file_get_contents("./plugins/Essentials/Protectdata.dat"));
		}
		
		$this->config = $this->api->plugin->readYAML("./plugins/Essentials/config.yml");
	}
	
	public function handler(&$data, $event){
		switch($event){
			case "server.close":
				//file_put_contents("./plugins/Essentials/Protectdata.dat", serialize($this->save));
				break;
			case "plugin.forge.api":
				$data["CommandAPI"]->register("blundo", "<player>", array($this, "defaultCommands"));
				break;
			case "entity.explosion":
				if($this->config["allow-explosion"] === false){
					return true;
				}
				break;
			case "tile.update":
				break;
			case "player.block.place":
				if($data["item"]->getID() === CHEST){
					$this->protect["chest"][$data["player"]->__get("iusername")][] = new Protect ($this->api, $data["player"]->__get("iusername"), new Position ($data["block"]->x, $data["block"]->y, $data["block"]->z, $data["player"]->level));
					break;
				}
				if($this->api->ban->isOp($data["player"]->__get("iusername")) === false){
					$items = BlockAPI::fromString($this->config["blacklist"]["placement"], true);
					foreach($items as $item){
						if($data["item"]->getID() === $item->getID() and $data["item"]->getMetadata() === $item->getMetadata()){
							return false;
						}
					}
				}
				break;
			case "player.block.break":
				if($data["target"]->getID() === CHEST){
					$t = $this->get(new Position($data["target"]->x, $data["target"]->y, $data["target"]->z, $data["player"]->level));
					if($t !== false){
						if($this->api->ban->isOp($data["player"]->__get("iusername"))){
							$t->__destruct();
							break;
						}
						$break = $t->onBreak($output, $data["player"]);
						if($output != ""){
							$data["player"]->sendChat($output);
						}
						return $break;
					}
					break;
				}
				if($this->api->ban->isOp($data["player"]->__get("iusername")) === false){
					$items = BlockAPI::fromString($this->config["blacklist"]["break"], true);
					foreach($items as $item){
						if($data["target"]->getID() === $item->getID() and $data["target"]->getMetadata() === $item->getMetadata()){
							return false;
						}
					}
				}
				break;
			case "player.block.activate":
				if($data["target"]->getID() === CHEST){
					$output = "";
					$t = $this->get(new Position($data["target"]->x, $data["target"]->y, $data["target"]->z, $data["player"]->level));
					if($data["item"]->getID() === STICK and ($this->api->ban->isOp($data["player"]->__get("iusername")) or $t->owner === $data["player"]->__get("iusername"))){
						$t->protectChange($output);
						if($output != ""){
							$data["player"]->sendChat($output);
						}
						return false;
					}
					if($t !== false){
						$open = $t->onOpen($output, $data["player"]);
						if($output != ""){
							$data["player"]->sendChat($output);
						}
						return $open;
					}
				}elseif($data["target"]->getID() === WOOD_DOOR_BLOCK){
					return false;
				}
				break;
		}
	}
	
	public function get(Position $pos){
		foreach($this->protect["chest"] as $array){
			foreach($array as $t){
				if($pos->__toString() === $t->pos->__toString()){
					return $t;
				}
			}
		}
		return false;
	}
	
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "blundo":
				break;
		}
		return $output;
	}
}

class Protect{
	public $owner, $api, $protected, $pos;
	public function __construct(ServerAPI $api, $player, Position $pos){
		$this->api = $api;
		$this->protected = false;
		$this->owner = $player;
		$this->pos = $pos;
		$this->init();
	}
	
	public function __destruct(){}
	
	public function init(){}
	
	public function onOpen(&$output, $player){
		if($this->check($output, $player->__get("iusername"))){
		}else{
			return false;
		}
		return true;
	}
	
	public function onBreak(&$output, $player){
		if($this->check($output, $player->__get("iusername"))){
			$this->__destruct();
		}else{
			return false;
		}
		return true;
	}
	
	public function check(&$output, $target){
		if($this->protected === true){
			if($this->owner !== $target){
				$owner = $this->api->player->get($this->owner);
				$output = "You are not the owner of the Chest. Owner : ".$owner;
				return false;
			}else{
				$output = "My chest!";
			}
		}else{
			$output = "This is a public chest.";
		}
		return true;
	}
	
	public function protectChange(&$output){
		if($this->protected === false){
			$owner = $this->api->player->get($this->owner);
			$output = "The chest can only open the ".$owner.".";
			$this->protected = true;
		}else{
			$output = "This is now public chest.";
			$this->protected = false;
		}
	}
}