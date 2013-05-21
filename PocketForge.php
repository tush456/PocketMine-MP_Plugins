<?php

/*
__PocketMine Plugin__
name=PocketForge
description=PocketForge
version=0.0.1
author=KsyMC
class=PocketForge
apiversion=7
*/

class PocketForge implements Plugin{
	private $api, $php;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function __destruct(){}
	
	public function init(){
		foreach(scandir("./plugins/extension/") as $php){
			if(strpos($php, ".php") !== false){
				require_once(FILE_PATH."/plugins/extension/".$php);
				$name = str_replace(".php", "", $php);
				$this->php[$name] = new $name($this->api);
			}
		}
		$this->api->handle("plugin.forge.api", $this->php);
	}
}