<?php

set_include_path('/var/www/html');
require_once('Tracker/Model/Essentials.php'); 

class TV extends SQLite3{

	public function getShow($id,$season){
		$db = new SQLite3('tv_shows.db');
		$retval = $db->query("SELECT location FROM `tv_shows` WHERE id = {$id}");
		$row = $retval->fetchArray();
		$table = $row["location"];

		$sql2 = "SELECT * FROM {$table} WHERE season = {$season}";
		$result = $db->query($sql2);
		echo "{\"show\":[";
		$tick = 0;
		while($row = $result->fetchArray()){
		    if ($tick != 0){
			echo ",";
		    }
		    $tick++;
		    echo json_encode(array("status" => "okay",
		                           "tile" => $row["title"],
		                           "season" => $row["season"],
		                           "episode" => $row["episode"],
					   "date" => $row["date"]));
		}
		echo "]}";
	}

	public function getShowList($organise,$page){
		$db = new SQLite3('tv_shows.db');
		/*if($organise == "2"){
		    $organise = "rating";
		    $sql = "SELECT name,image,id FROM `tv_shows` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$offset}"; //**rating
		}else if ($organise == "1"){
		    $organise = "name";
		    $sql = "SELECT name,image,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}"; //**rating
		}*/  //  	** NO RATINGS IN TABLES YET **
		$type = "tv_shows";
		$organise = "name";
		$essen = new Essentials();
		$essen->getList($organise,$page,$type,$db);
	}
}
