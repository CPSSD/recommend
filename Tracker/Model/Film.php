<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Essentials.php'); 
include_once('Tracker/config.php'); 

class Film extends SQLite3{
	
	//returns recommended films to the View Pages to be displayed
	public function filmRecommendations($db,$type,$userID){
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&recommendations={$userID}");
        var_dump($json);
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
	
	public function userFilmLikes($db,$type,$userID){
        
		$sql = "SELECT mediaName,mediaTable,mediaImage,mediaID FROM likes WHERE userID={$userID} AND mediaTable LIKE '%{$type}%'";

        $essen = new Essentials();
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

	public function searchFilms($db,$type,$search){
        if (strpos($search,'%20') !== false){
			$sub = explode(' ',$search,2);
			$sql = "SELECT name,image,id,rating FROM `{$type}` WHERE name LIKE '%{$sub[0]}%' AND name LIKE '%{$sub[1]}%' ORDER BY name DESC LIMIT 24";	
		}else{
			$sql = "SELECT name,image,id,rating FROM `{$type}` WHERE name LIKE '%{$search}%' ORDER BY name DESC LIMIT 24";	
		}

		$retval = $db->query($sql);
		echo "{\"{$type}\":[";
 		$tick = 0;
 		while($row = $retval->fetchArray()){
 		    if ($tick != 0){
 			echo ",";
 		    }
 		    $tick++;
		    echo json_encode(array("status" => "okay",
		                           "name" => $row["name"],
		                           "rating" => $row["rating"],
					               "id" => $row["id"],
					               "image" => $row["image"]));
		}
 		echo "]}";
	}
	
	public function getEpisodes($db,$id_list, $date1) {
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

	public function getFilmList($db,$type,$organise,$page,$order){
        $essen = new Essentials();
        $maxPage = floor($essen->getMaxID($type,$db) / 24);
		$pageParam = intval($page);
		$offset = $pageParam * 24;
        
		if($organise == "1"){
            $organise = "name";
			$sql = "SELECT name,date,image,rating,id FROM `{$type}` ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
		} else if($organise == "2"){
            $organise = "date";
			$sql = "SELECT name,date,rating,image,id FROM `{$type}` ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
		} else {
            $organise = "rating";
			$sql = "SELECT name,date,image,rating,id FROM `{$type}` ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
		}
        if($page <= $maxPage){
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
        }else{
            echo "You're Page Value is too high or too low!!";
        }
	}

	public function getFilm($db,$type,$id){
		$essen = new Essentials();
		if($essen->getMaxID($type,$db) >= $id && $id > 0){
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
		}else{
            echo "No Film with that ID";
        }
	}
}
?>
