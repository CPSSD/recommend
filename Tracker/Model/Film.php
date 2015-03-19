<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Essentials.php'); 

class Film extends SQLite3{
	
	//returns recommended films to the View Pages to be displayed
	public function filmRecommendations($userID){
		$db = new SQLite3('database.db');
		$type = "films";
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&filmLikesToRecommend={$userID}");
		$obj = json_decode($json, true);
		//gets all liked films and puts them in an array
		$films = array();
		foreach($obj['films'] as $movie){
			$films[] = $movie['mediaName'];	
		}
		
		//gathers the genres of these films
		$genre = array();
		foreach($films as $film){
			$sql = "SELECT genre FROM films WHERE name='{$film}'";
			$result = $db->query($sql);
			while($row = $result->fetchArray()){
				$row = substr($row['genre'],8,-2);
				$rows = explode("+",$row);
				foreach($rows as $string){
					$genre[] = $string;
				}
			}
		}
		//selects the highest valued genre
		$genre = array_count_values($genre);
		$max = max($genre);
		$key = array_search($max, $genre);	

		$essen = new Essentials();
		$sql = "SELECT id,synopsis,name,date,rating,starring,director,genre,image,age FROM films WHERE genre LIKE '%{$key}%' ORDER BY rating DESC LIMIT 6";
		
		$retval = $db->query($sql);
		echo "{\"{$type}\":[";
		$tick = 0;
		while($row = $retval->fetchArray()){
		    if ($tick != 0){
			echo ",";
		    }
		    $tick++;
		    echo json_encode($essen->createArrayFromData($sql, $row));
		}
		echo "]}";
	}
	
	
	
	public function filmLikesToRecommend($userID){
		$db = new SQLite3('database.db');
		$type = "films";
		$essen = new Essentials();
		$essen->mediaLikesToRecommend($db,$userID,$type);
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
