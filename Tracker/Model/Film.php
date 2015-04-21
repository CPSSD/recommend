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

    public function advancedFilmSearch($db,$type,$param,$rating){
        $params = explode(",",$param);
        $sql = "SELECT name,image,id,rating FROM `{$type}` WHERE director LIKE '%{$params[0]}%' AND starring LIKE '%{$params[1]}%' AND genre LIKE '%{$params[2]}%' AND rating > {$rating}";

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

	public function searchFilms($db,$type,$search){
        $sql = "SELECT name,image,id,rating FROM `{$type}` WHERE name LIKE '%{$search}%' ORDER BY name DESC LIMIT 24";

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
	
	public function getFilmsFromDay($id_list, $date, $db){
		$data = [];
		$sql = "SELECT * FROM 'films' WHERE date = \"{$date}\"";
		$result = $db->query($sql);
		$tick = 0;
		while($row = $result->fetchArray()){
			if (in_array($row["id"], $id_list)){
				$data[$tick] = "{\"name\":\"{$row["name"]}\",\"id\":\"{$row["id"]}\"}";
				$tick ++;
			}
		}
		return $data;
	}
	
	public function generateCalendarDay($date, $id_list, $db, $essen){
		echo "{";
		for($i = 0; $i <= 2; $i++){
			echo '"hidden'.$i.'": { "date":"2000-01-01", "pretty-date": "hidden", "episodes": []},';
		}
		$show_tick = 0;
		$episode_data = $this->getFilmsFromDay($id_list, $date, $db);
		if ($show_tick != 0){
			echo ",";
		}
		echo '"'.$date.'":{"date":"'.$date.'",';
		echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
		echo '"movies":[';
		$episode_tick = 0;
		foreach ($episode_data as $episode){
			if ($episode_tick != 0){
				echo ",";
			}
			echo $episode;
			$episode_tick += 1;
		}
		echo "]}}";
		$total_episode_count = 0;
		$show_tick += 1;
		$episode_tick = 0;
	}
	
	public function generateCalendar($date1, $id_list, $db, $essen, $offset, $offset_backwards, $depth){
		$date_list = $essen->generateDates($date1, $offset, $offset_backwards, $depth);
		$episode_tick = 0;
		$show_tick = 0;
		echo "{";
		foreach($date_list as $date){
			$episode_data = $this->getFilmsFromDay($id_list, $date, $db);
			if ($show_tick != 0){
				echo ",";
			}
			echo '"'.$date.'":{"date":"'.$date.'",';
			echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
			echo '"movies":[';
			$episode_tick = 0;
			foreach ($episode_data as $episode){
				if ($episode_tick != 0){
					echo ",";
				}
				echo $episode;
				$episode_tick += 1;
			}
			echo "]}";
			$total_episode_count = 0;
			$show_tick += 1;
			$episode_tick = 0;
		}
		echo "}";
	}
		
	public function generateYearCalendar($year, $id_list, $db, $essen){
		echo "{";
		$total_episode_count = 0;
		$date_list = $essen->generateMonthDates($year);
		$m = "01";
		$total_episode_count = 0;
		foreach ($date_list as $date){
			$cur_m = explode("-", $date)[1];
			if($cur_m == $m){
				$episode_data = $this->getFilmsFromDay($id_list, $date, $db);
				$total_episode_count += sizeof($episode_data);
			} else if($cur_m != "01"){
				echo "\"{$m}\": {";
				echo '"date":"'.$m.'",';
				echo '"pretty-date":"'.date('F', strtotime($year."-".$m."-01")).'",';
				echo '"status": "okay","count": '.$total_episode_count;
				echo "}";
				if($m != "12"){
					echo ",";
				}
				$m = $cur_m;
				$total_episode_count = 0;
			}
		}
		echo "}";
	}
	
	public function getEpisodes($id_list, $date1, $range) {
		$db = new SQLite3('database.db');
		$essen = new Essentials();
		if ($range == "year"){
			$this->generateYearCalendar(explode("-", $date1)[0], $id_list, $db, $essen);
		} else if ($range == "week"){
			$this->generateCalendar($date1, $id_list, $db, $essen, 7, 0, 1);
		} else if ($range == "day"){
			$this->generateCalendarDay($date1, $id_list, $db, $essen);
		} else {
			$this->generateCalendar($date1, $id_list, $db, $essen, 7, 7, 5);
		} 
	}
	
	public function getFilmList($db,$type,$organise,$page,$order,$uid){
        $essen = new Essentials();
        $maxPage = floor($essen->getMaxID($type,$db) / 24);
		$pageParam = intval($page);
		$offset = $pageParam * 24;
		$likeList = $essen->getLikeList($db, $type, $uid);
		
		if($organise == "1"){
            $organise = "name";
			$sql = "SELECT name,date,image,rating,id FROM `{$type}` WHERE date != 'None' ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
		} else if($organise == "2"){
            $organise = "date";
			$sql = "SELECT name,date,rating,image,id FROM `{$type}` WHERE date != 'None' ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
		} else {
            $organise = "rating";
			$sql = "SELECT name,date,image,rating,id FROM `{$type}` WHERE date != 'None' ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
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
				$row['liked'] = in_array($row['id'], $likeList);
		        echo json_encode($essen->createArrayFromData("name,date,image,rating,id,liked", $row));
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
