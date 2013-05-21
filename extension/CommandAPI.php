<?php

class CommandAPI{
	private $api, $help, $cmds, $alias;
	public function __construct(ServerAPI $api){
		$this->api = $api;
		$this->help = array();
		$this->cmds = array();
		$this->alias = array();
		$this->init();
	}
	
	public function __destruct(){}
	
	public function init(){
		$this->api->addHandler("tile.update", array($this, "handler"), 1);
	}
	
	public function handler(&$data, $event){
		switch($event){
			case "tile.update":
				if($data->class === TILE_SIGN){
					$line = $data->data["Text1"].$data->data["Text2"].$data->data["Text3"].$data->data["Text4"];
					if($line{0} === "/"){
						$player = $this->api->player->get($data->data["creator"]);
						$this->run(str_replace("/", "", $line), $player);
						$player->level->setBlock(new Vector3 ($data->data["x"], $data->data["y"], $data->data["z"]), BlockAPI::get(AIR));
						$this->api->tileentity->remove($data->id);
					}
				}
				break;
		}
	}
	
	public function alias($alias, $cmd){
		$this->alias[strtolower(trim($alias))] = trim($cmd);
		return true;
	}
	
	public function register($cmd, $help, $callback){
		if(!is_callable($callback)){
			return false;
		}
		$cmd = strtolower(trim($cmd));
		$this->cmds[$cmd] = $callback;
		$this->help[$cmd] = $help;
		ksort($this->help, SORT_NATURAL | SORT_FLAG_CASE);
	}
	
	public function getHelp($params, $issuer){
		if(isset($params[0]) and !is_numeric($params[0])){
			$c = trim(strtolower($params[0]));
			if(isset($this->help[$c]) or isset($this->alias[$c])){
				$c = isset($this->help[$c]) ? $c : $this->alias[$c];
				if($this->api->dhandle("api.cmd.command", array("cmd" => $c, "parameters" => array(), "issuer" => $issuer, "alias" => false)) === false){
					return false;
				}
				$output .= "Usage: /$c ".$this->help[$c]."\n";
				return $output;
			}
		}
		$cmds = array();
		foreach($this->help as $c => $h){
			if($this->api->dhandle("api.cmd.command", array("cmd" => $c, "parameters" => array(), "issuer" => $issuer, "alias" => false)) === false){
				continue;
			}
			$cmds[$c] = $h;
		}
		$max = ceil(count($cmds) / 5);
		$page = (int) (isset($params[0]) ? min($max, max(1, intval($params[0]))):1);						
		$output .= "- Showing help page $page of $max (/help <page>) -\n";
		$current = 1;
		foreach($cmds as $c => $h){
			$curpage = (int) ceil($current / 5);
			if($curpage === $page){
				$output .= "/$c ".$h."\n";
			}elseif($curpage > $page){
				return $output;
			}
			++$current;
		}
		return $output;
	}
	
	public function run($line, $issuer, $alias = false){
		if($line != ""){
			$params = explode(" ", $line);
			$cmd = strtolower(array_shift($params));
			if(isset($this->alias[$cmd])){
				$this->run($this->alias[$cmd]." ".implode(" ", $params), $issuer, $cmd);
				return;
			}
			console("[CMD API] \x1b[33m".$issuer->__get("username")."\x1b[0m issued server command: ".ltrim("$alias ")."/$cmd ".implode(" ", $params));
			if(isset($this->cmds[$cmd]) and is_callable($this->cmds[$cmd])){
				if($this->api->dhandle("api.cmd.command", array("cmd" => $cmd, "parameters" => $params, "issuer" => $issuer, "alias" => $alias)) === false){
					$output = "You don't have permission to use this command.\n";
				}else{
					$output = @call_user_func($this->cmds[$cmd], $cmd, $params, $issuer, $alias);
				}
			}elseif($this->api->dhandle("api.cmd.command.unknown", array("cmd" => $cmd, "params" => $params, "issuer" => $issuer, "alias" => $alias)) !== false){
				$output = "Command doesn't exist! Use /help\n";
			}
			if($output != ""){
				$issuer->sendChat(trim($output, "\n"));
			}
			return $output;
		}
	}
}