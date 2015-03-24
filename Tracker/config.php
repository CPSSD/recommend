<?php

class Config{

	public $ip;

	public function __construct($ip){
		$this->ip = $ip;
	}
}

$config = new Config("http://cpssd5-web.computing.dcu.ie/");
$ip = $config->ip;
$GLOBALS["ip"] = $ip;
?>
