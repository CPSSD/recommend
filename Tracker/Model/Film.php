<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/Model/Essentials.php'); 

class Film extends SQLite3{
	
	public function filmRecomend($userID){
		$db = new SQLite3('database.db');
		$type = "films";
		$essen = new Essentials();
		$essen->mediaRecommend($db,$userID,$type);
	}

	public function filmLikes($page){
		$db = new SQLite3('database.db');
		$type = 'films';
		$essen = new Essentials();
		$essen->likes($db,$type,$page);
	}

	public function searchFilm($film){
		$db = new SQLite3('database.db');
		$type = "films";
		$essen = new Essentials();
		$essen->search($db,$type,$film);
	}
	
	public function getEpisodes($id_list, $date1) {
		$db = new SQLite3('database.db');
		$film_tick = 0;
		$essen = new Essentials();
		$date_list = $essen->generateDates($date1);
		echo "{";
		foreach($date_list as $date){
			if ($film_tick != 0){
				echo ",";
			}
			echo "\"{$date}\": {";
			echo "\"date\":\"{$date}\",";
			echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
			echo "\"movies\": [";
			$sql = "SELECT * FROM 'films' WHERE date = \"{$date}\"";
			$result = $db->query($sql);
			while($row = $result->fetchArray()){
				if (in_array($row["id"], $id_list)){
					echo "{\"name\":\"{$row["name"]}\",";
					echo "\"id\":\"{$row["id"]}\"}";
				}
			}
			echo "]}";
			$film_tick++;
		}
		echo "}";
	}

	public function getFilmList($organise,$page){
		$db = new SQLite3('database.db');
        
		if($organise == "2"){
			$organise = "rating";
		}else if ($organise == "1"){
			$organise = "date";
		}else if ($organise == "0"){
			$organise = "name";
		}
		$type = "films";
		$essen = new Essentials();
		$essen->getList($organise,$page,$type,$db);
	}

	public function getFilm($id){
		$db = new SQLite3('database.db');
		$essen = new Essentials();
		if(/*$essen->getMaxID() >= $id &&*/ $id > 0){
			$sql = "SELECT * FROM `films` WHERE id = '".$id."'";
			$result = $db->query($sql);
			while($row = $result->fetchArray()){
				echo json_encode(array( "status" => "okay",
							"id" => $row["id"],
							"name" => $row["name"],
							"date" => $row["date"],
							"runtime" => $row["runtime"],
							"rating" => $row["rating"],					   
							"starring" => $row["starring"],
							"director" => $row["director"],
							"genre" => $row["genre"],
							"synopsis" => $row["synopsis"],
							"image" => $row["image"],
							"age" => $row["age"]));
			}
		}
	}
}
?>
