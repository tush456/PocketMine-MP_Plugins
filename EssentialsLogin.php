<?php

/*
__PocketMine Plugin__
name=EssentialsLogin
description=EssentialsLogin
version=0.0.1
author=KsyMC
class=EssentialsLogin
apiversion=7
*/

class EssentialsLogin implements Plugin{
	private $api, $config, $password, $logined, $forget;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->logined = array();
		$this->forget = array();
	}
	
	public function __destruct(){}
	
	public function init(){
		$this->api->event("server.close", array($this, "handler"));
		$this->api->addHandler("plugin.forge.api", array($this, "handler"), 1);
		$this->api->addHandler("api.cmd.command", array($this, "handler"), 5);
		
		$this->api->addHandler("player.join", array($this, "handler"), 5);
		$this->api->addHandler("tile.update", array($this, "handler"), 10);
		$this->api->addHandler("player.flying", array($this, "handler"), 10);
		$this->api->addHandler("player.move", array($this, "handler"), 10);
		$this->api->addHandler("player.interact", array($this, "handler"), 10);
		$this->api->addHandler("player.block.touch", array($this, "handler"), 10);
		$this->api->addHandler("player.block.activate", array($this, "handler"), 10);
		
		if(file_exists("./plugins/Essentials/Logindata.dat")){
			$this->password = unserialize(file_get_contents("./plugins/Essentials/Logindata.dat"));
		}
		//$this->api->handle("plugin.login.status", array("password" => $this->password, "logined" => $this->logined, "forget" => $this->forget));
		
		$this->api->console->register("password", "<remove|change> <player> [password]", array($this, "commandHandler"));
		$this->config = $this->api->plugin->readYAML("./plugins/Essentials/config.yml");
	}
	
	public function handler(&$data, $event){
		switch($event){
			case "server.close":
				file_put_contents("./plugins/Essentials/Logindata.dat", serialize($this->password));
				break;
			case "player.join":
				$this->logined[$data->__get("iusername")] = false;
				$this->forget[$data->__get("iusername")] = 0;
				break;
			case "tile.update":
				if($data->class === TILE_SIGN){
					$player = $this->api->player->get($data->data["creator"]);
					$line = $data->data["Text1"].$data->data["Text2"].$data->data["Text3"].$data->data["Text4"];
					if($this->logined[$player->__get("iusername")] === false and $line{0} !== "/"){
						$player->sendChat("Please login first.");
						$this->api->tileentity->remove($data->id);
						$player->level->setBlock(new Vector3 ($data->x, $data->y, $data->z), BlockAPI::get(AIR));
						return false;
					}
				}
				break;
			case "player.flying":
				if($this->logined[$data->__get("iusername")] === false){
					$username = $data->__get("username");
					$x = $data->entity->x;
					$y = $data->level->getSpawn()->getY();
					$z = $data->entity->z;
					$this->api->player->tppos($username, $x, $y, $z);
					$data->sendChat("Please login first.");
					return true;
				}
				break;
			case "player.move":
				if($this->config["login-after-move"] === false){
					return false;
				}
				break;
			case "player.interact":
				$player = $this->api->player->getByEID($data["entity"]->eid);
				if($this->logined[$player->__get("iusername")] === false){
					$player->sendChat("Please login first.");
					return false;
				}
				break;
			case "player.block.touch":
				if($this->logined[$data["player"]->__get("iusername")] === false and $this->signCheck($data["type"], $data["item"], $data["target"]) === false){
					$data["player"]->sendChat("Please login first.");
					return false;
				}
				break;
			case "player.block.activate":
				/*if($this->logined[$data["player"]->__get("iusername")] === false and $data["target"]->isActivable === true){
					$data["player"]->sendChat("Please login first.");
					return false;
				}*/
				break;
			case "plugin.forge.api":
				$data["CommandAPI"]->register("register", "<password>", array($this, "commandHandler"));
				$data["CommandAPI"]->register("login", "<password>", array($this, "commandHandler"));
				$data["CommandAPI"]->register("logout", "", array($this, "commandHandler"));
				$data["CommandAPI"]->register("password", "<remove|change> <player> <password>", array($this, "commandHandler"));
				break;
			case "api.cmd.command":
				if($this->logined[$data["issuer"]->__get("iusername")] !== true){
					foreach($this->config["login-after-commands"] as $cmd){
						if($cmd === $data["cmd"]){
							return true;
						}
					}
					return false;
				}
				break;
		}
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
	
	public function commandHandler($cmd, $params, $issuer, $alias){
		$output = "";
		switch($cmd){
			case "password":
				$p = strtolower($params[0]);
				switch($p){
					case "remove":
						$player = strtolower($params[1]);
						if($player === ""){
							$output .= "Usage: /password <remove> <player>\n";
							break;
						}
						if(!isset($this->password[$player])){
							$output .= "Player \"$player\" does not exist.\n";
							break;
						}
						unset($this->password[$player]);
						$player = $this->api->player->get($player);
						if($player !== false){
							$this->logined[$player->__get("iusername")] = false;
							$player->sendChat("Your password has been initialized.");
						}
						break;
					case "change":
						$player = strtolower($params[1]);
						$password = strtolower($params[2]);
						if($player === "" or $password === ""){
							$output .= "Usage: /password <remove> <player>\n";
							break;
						}
						if(!isset($this->password[$player])){
							$output .= "Player \"$player\" does not exist.\n";
							break;
						}
						if(strlen($password) < 4 or strlen($password) > 10){
							$output .= "Too short or too long. (4 - 10)\n";
							break;
						}
						$this->password[$player] = $password;
						$player = $this->api->player->get($player);
						if($player !== false){
							$this->logined[$player->__get("iusername")] = false;
							$player->sendChat("Your password has been changed.");
						}
						break;
					default:
						$output .= "Usage: /password <remove|change> <player> <password>\n";
						break;
				}
				break;
			case "register":
				$password = strtolower($params[0]);
				if(trim($password) === ""){
					$output .= "Usage: /register <password>\n";
					break;
				}
				if(!isset($this->password[$issuer->__get("iusername")])){
					if(strlen($password) >= 4 and strlen($password) <= 10){
						$this->password[$issuer->__get("iusername")] = urlencode($password);
						$output .= "Register success.\n";
					}else{
						$output .= "Too short or too long. (4 - 10)\n";
					}
				}else{
					$output .= "You have already registered.\n";
				}
				break;
			case "login":
				$password = strtolower($params[0]);
				if(trim($password) === ""){
					$output .= "Usage: /login <password>\n";
					break;
				}
				if($this->logined[$issuer->__get("iusername")] === true){
					$output .= "You have already login\n";
					break;
				}
				if(!isset($this->password[$issuer->__get("iusername")])){
					$output .= "You did not even register.\n";
					break;
				}
				if(urldecode($this->password[$issuer->__get("iusername")]) === $password){
					$this->logined[$issuer->__get("iusername")] = true;
					$output .= "Login success!\n";
				}else{
					++$this->forget[$issuer->__get("iusername")];
					if($this->forget[$issuer->__get("iusername")] === 5){
						$this->api->ban->commandHandler("kick", array($issuer->__get("iusername"), "forgot the password"), "console", false);
					}
					$output .= "Login failed. ".$this->forget[$issuer->__get("iusername")]."/5\n";
				}
				break;
			case "logout":
				if($this->logined[$issuer->__get("iusername")] === false){
					$output .= "Please login first.";
				}
				$this->logined[$issuer->__get("iusername")] = false;
				$this->forget[$issuer->__get("iusername")] = 0;
				$output .= "You have been logout.\n";
				break;
		}
		return $output;
	}
}