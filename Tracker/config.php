<?php

class Config{

	public $ip;

	public function __construct($ip){
		$this->ip = $ip;
	}
}

$config = new Config("http://localhost/");
$ip = $config->ip;
$GLOBALS["ip"] = $ip;
?>
