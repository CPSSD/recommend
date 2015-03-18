<?php

//set_include_path('/var/www/html');
echo $_SERVER['DOCUMENT_ROOT'];
//require_once("{$_SERVER['DOCUMENT_ROOT']}/Tracker/config.php");

class Util{

 	function checkNextSeason($season,$id){
		
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&id={$id}&season={$season}");		

		if(strpos($json,'okay') !== false){
			return "{$GLOBALS["ip"]}Tracker/View/getShow.php?id={$id}&season={$season}";
		}else{
			return "{$GLOBALS["ip"]}Tracker/View/getShow.php?id={$id}&season=1";
		}
	}

	function checkNextPage($type,$page,$organise){
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type={$type}&organise={$organise}&page={$page}");

		if(strpos($json,'okay') !== false){
			return $page;
		}else{
			return '0';				
		}
	}

	function checkNextLike($type,$page){
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type={$type}&page={$page}");
		if(strpos($json,'okay') !== false){
			return $page;
		}else{
			return '0';				
		}
	}
}
