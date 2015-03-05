<?php

set_include_path('/var/www/html');
require_once($_SERVER['DOCUMENT_ROOT'].'/Tracker/Model/Essentials.php'); 

class TV extends SQLite3{

	public function getShow($id,$season){
		$db = new SQLite3('tv_shows.db');
		$retval = $db->query("SELECT name,rating,image,location FROM `tv_shows` WHERE id = {$id}");
		$row = $retval->fetchArray();
		$table = $row["location"];
		$show_data = $row;

		$sql2 = "SELECT * FROM {$table} WHERE season = {$season}";
		$result = $db->query($sql2);
		
		echo '{"name":"' . $show_data['name'] . '",';
		echo '"rating":"' . $show_data['rating'] . '",';
		echo '"image":"' . $show_data['image'] . '",';
		echo '"age":"Unknown",';
		echo '"genre":"Unknown",';
		echo "\"show\":[";
		$tick = 0;
		$initial_air_date = null;
		while($row = $result->fetchArray()){
		    if ($tick != 0){
				echo ",";
		    } else {
				$initial_air_date = $row['date'];
			}
		    $tick++;
		    echo json_encode(array("status" => "okay",
		                           "tile" => $row["title"],
		                           "season" => $row["season"],
		                           "episode" => $row["episode"],
								   "date" => $row["date"]));
		}
		echo "], ";
		echo '"date":"' . $initial_air_date . '"}';
	}

	public function getShowList($organise, $page){
		$db = new SQLite3('tv_shows.db');
		if($organise == "2"){
		    $organise = "rating";
		    $sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$page}"; //**rating
		} else if ($organise == "1"){
		    $organise = "name";
		    $sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}"; //**rating
		} else if ($organise == "0"){
		    $organise = "name";
		    $sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}"; //**rating
		} else {
			$organise = "name";
		    $sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}"; //**rating
		}
		$type = "tv_shows";
		$essen = new Essentials();
		$essen->getList($organise,$page,$type,$db);
	}
}
