<?php

class Util{

 	function checkNextSeason($season,$id){
		
		$json = file_get_contents("http://localhost/Tracker/index.php?type=tv_shows&id={$id}&season={$season}");		

		if(strpos($json,'okay') !== false){
			return "http://localhost/Tracker/View/getShow.php?id={$id}&season={$season}";
		}else{
			return "http://localhost/Tracker/View/getShow.php?id={$id}&season=1";
		}
	}

	function checkNextPage($type,$page,$organise){
		$json = file_get_contents("http://localhost/Tracker/index.php?type={$type}&organise={$organise}&page={$page}");

		if(strpos($json,'okay') !== false){
			return $page;
		}else{
			return '0';				
		}
	}
}
