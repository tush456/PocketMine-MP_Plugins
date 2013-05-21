<?php

/*
__PocketMine Plugin__
name=GroupManager
description=GroupManager
version=0.0.1
author=KsyMC
class=GroupManager
apiversion=7
*/

class GroupManager implements Plugin{
	private $api, $users, $groups, $defaultgroup;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function __destruct(){}
	
	public function init(){
		$this->api->event("server.close", array($this, "handler"));
		$this->api->addHandler("plugin.forge.api", array($this, "handler"), 1);
		$this->api->addHandler("api.cmd.command", array($this, "handler"), 1);
		
		$this->api->addHandler("player.block.touch", array($this, "handler"), 5);
		$this->api->addHandler("player.block.place.spawn", array($this, "handler"), 5);
		$this->api->addHandler("player.block.break.spawn", array($this, "handler"), 5);
		
		$this->api->console->register("manuadd", "<player> <group>", array($this, "defaultCommands"));
		$this->api->console->register("manudel", "<player>", array($this, "defaultCommands"));
		$this->api->console->register("manwhois", "<player>", array($this, "defaultCommands"));
		$this->api->console->register("mangadd", "<group>", array($this, "defaultCommands"));
		$this->api->console->register("mangdel", "<group>", array($this, "defaultCommands"));
		$this->api->console->register("listgroups", "", array($this, "defaultCommands"));
		$this->api->console->register("mansave", "", array($this, "defaultCommands"));
		$this->api->console->register("manload", "", array($this, "defaultCommands"));
		
		if(is_dir("./plugins/GroupManager/worlds/".$this->api->getProperty("level-name")) === false){
			mkdir("./plugins/GroupManager/worlds/".$this->api->getProperty("level-name"), 0777, true);
		}
		$this->createConfig();
	}
	
	public function handler(&$data, $event){
		switch($event){
			case "server.close":
				$this->users->save();
				$this->groups->save();
				break;
			case "plugin.forge.api":
				$data["CommandAPI"]->register("manuadd", "<player> <group>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("manudel", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("manwhois", "<player>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("mangadd", "<group>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("mangdel", "<group>", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("listgroups", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("mansave", "", array($this, "defaultCommands"));
				$data["CommandAPI"]->register("manload", "", array($this, "defaultCommands"));
				break;
			case "api.cmd.command":
				$player = $this->users->get("users");
				$group = $this->groups->get("groups");
				if(isset($player[$data["issuer"]->__get("username")])){
					if(in_array($data["cmd"], $group[$player[$data["issuer"]->__get("username")]["group"]]["permissions"]) or in_array($data["cmd"], $player["permissions"])){
						return true;
					}
				}elseif(in_array($data["cmd"], $group[$this->defaultgroup]["permissions"])){
					return true;
				}
				return false;
			case "player.block.touch":
				if($this->signCheck($data["type"], $data["item"], $data["target"]) === false){
					$player = $this->users->get("users");
					$group = $this->groups->get("groups");
					if(isset($player[$data["player"]->__get("username")])){
						if($group[$player[$data["player"]->__get("username")]["group"]]["info"]["build"] === false){
							$data["player"]->sendChat("You don't have permission");
							return false;
						}
					}elseif($group[$this->defaultgroup]["info"]["build"] === false){
						$data["player"]->sendChat("You don't have permission");
						return false;
					}
				}
				break;
		}
	}
	
	public function defaultCommands($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "manuadd":
				if($params[0] == "" or $params[1] == ""){
					$output .= "Usage: /manuadd <player> <group>";
					break;
				}
				$player = $params[0];
				$group = $params[1];
				if($this->api->player->get($params[0]) !== false){
					$player = $this->api->player->get($params[0])->__get("username");
				}
				$groups = $this->groups->get("groups");
				if(!array_key_exists($group, $groups)){
					$output .= "Group \"$group\" does not exist.";
					break;
				}
				$users = $this->users->get("users");
				if(!isset($users[$player])){
					$users[$player] = array("group" => $group, "permissions" => array());
					$this->users->set("users", $users);
				}
				$users[$player]["group"] = $group;
				$this->users->set("users", $users);
				$this->api->chat->broadcast("$player has been moved to the $group group.");
				break;
			case "manudel":
				if($params[0] == ""){
					$output .= "Usage: /manudel <player>";
					break;
				}
				$player = $params[0];
				if($this->api->player->get($params[0]) !== false){
					$player = $this->api->player->get($params[0])->__get("username");
				}
				$users = $this->users->get("users");
				if(!isset($users[$player])){
					$output .= "Player \"$player\" does not exist.";
					break;
				}
				unset($users[$player]);
				$this->users->set("users", $users);
				$output .= "$player has been removed";
				break;
			case "manwhois":
				if($params[0] == ""){
					$output .= "Usage: /manwhois <player>";
					break;
				}
				$player = $params[0];
				if($this->api->player->get($params[0]) !== false){
					$player = $this->api->player->get($params[0])->__get("username");
				}
				$users = $this->users->get("users");
				if(!isset($users[$player])){
					$output .= "Player \"$player\" does not exist.";
					break;
				}
				$group = $users[$player]["group"];
				$output .= "$player belong to the $group group.";
				break;
			case "mangadd":
				if($params[0] == ""){
					$output .= "Usage: /mangadd <group>";
					break;
				}
				$group = $params[0];
				$groups = $this->groups->get("groups");
				if(isset($groups[$group])){
					$output .= "$group already exists.";
					break;
				}
				$groups[$group] = array(
					"default" => false,
					"permissions" => array(),
					"info" => array(
						"prefix" => "[$group] ",
						"build" => true,
						"suffix" => " [Jejo]",
					),
				);
				$this->groups->set("groups", $groups);
				$output .= "Has been added to the $group group.";
				break;
			case "mangdel":
				if($params[0] == ""){
					$output .= "Usage: /mangadd <group>";
					break;
				}
				$group = $params[0];
				$groups = $this->groups->get("groups");
				if(!array_key_exists($group, $groups)){
					$output .= "Group \"$group\" does not exist.";
					break;
				}
				unset($groups[$group]);
				$this->groups->set("groups", $groups);
				$output .= "$group has been removed";
				break;
			case "listgroups":
				$groups = $this->groups->get("groups");
				$output .= "Groups list : ";
				foreach($groups as $name => $group){
					$output .= "$name, ";
				}
				break;
			case "mansave":
				$this->users->save();
				$this->groups->save();
				break;
			case "manload":
				$this->reloadConfig();
				break;
		}
		return $output;
	}
	
	public function signCheck($type, $item, $target){
		if($type === "place"){
			if($item->getID() === SIGN){
				return true;
			}
		}else{
			if($target->getID() === SIGN_POST or $target->getID() === WALL_SIGN){
				return true;
			}
		}
		return false;
	}
	
	public function createConfig(){
		$this->users = new Config(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/users.yml", CONFIG_YAML);
		$this->groups = new Config(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/groups.yml", CONFIG_YAML);
		$this->reloadConfig();
	}
	
	public function reloadConfig(){
		$this->users->load(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/users.yml", CONFIG_YAML);
		$this->groups->load(DATA_PATH."/plugins/GroupManager/worlds/".$this->api->getProperty("level-name")."/groups.yml", CONFIG_YAML);
		foreach($this->groups->get("groups") as $name => $group){
			if($group["default"] === true){
				$this->defaultgroup = $name;
				console("[INFO] Default group : ".$this->defaultgroup);
				$found = true;
				break;
			}
		}
		if(!isset($found)){
			console("\x1b[31;1m[ERROR] The default group does not exist.");
		}
	}
}