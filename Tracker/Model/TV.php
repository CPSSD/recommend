<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Essentials.php'); 

class TV extends SQLite3{

	public function showLikes($page){
		$db = new SQLite3('database.db');
		$type = 'tv_shows';
		$essen = new Essentials();
		$essen->likes($db,$type,$page);
	}

	public function searchShow($show){
		$db = new SQLite3('database.db');
		$type = "tv_shows";
		$essen = new Essentials();
		$essen->search($db,$type,$show);
	}
	
	public function getEpisodes($id_list, $date1) {
		$db = new SQLite3('database.db');
		$episode_tick = 0;
		$show_tick = 0;
		$essen = new Essentials();
		$date_list = $essen->generateDates($date1);
		echo "{";
		foreach($date_list as $date){
			if ($show_tick != 0){
				echo ",";
			}
			echo '"'.$date.'":{"date":"'.$date.'",';
			echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
			echo '"episodes":[';
			foreach($id_list as $id){
				$retval = $db->query("SELECT id,name,location FROM 'tv_shows' WHERE id = {$id}");
				$row = $retval->fetchArray();
				$table = $row["location"];
				$show_data = $row;
				$retval = $db->query("SELECT season,episode,title FROM '{$table}' WHERE date = \"{$date}\"");
				while (($row = $retval->fetchArray()) != null){
					if ($episode_tick != 0){
						echo ",";
					}
					if($row["season"] < 10 && $row["season"][0] != "0") {
						$row["season"] = "0".$row["season"];
					}
					if($row["episode"] < 10 && $row["episode"][0] != "0") {
						$row["episode"] = "0".$row["episode"];
					}
					echo json_encode(array(	"status" => "okay",
											"show" => $show_data["name"],
											"show-id" => $show_data["id"],
											"season" => $row["season"],
											"episode" => $row["episode"],
											"title" => $row["title"]
											));
					$episode_tick+=1;
				}
			}
			$show_tick += 1;
			$episode_tick = 0;
			echo "]}";
		}
		echo "}";
	}

	public function getShow($id,$season){
		$db = new SQLite3('database.db');
		$retval = $db->query("SELECT * FROM `tv_shows` WHERE id = {$id}");
		$row = $retval->fetchArray();
		$table = $row["location"];
		$show_data = $row;

		$sql2 = "SELECT * FROM {$table} WHERE season = {$season}";
		$result = $db->query($sql2);
		
		echo '{"name":"' . $show_data['name'] . '",';
		echo '"id":"' . $show_data['id'] . '",';
		echo '"rating":"' . $show_data['rating'] . '",';
		echo '"image":"' . $show_data['image'] . '",';
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
		$db = new SQLite3('database.db');
		if($organise == "2"){
			$organise = "rating";
			$sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$page}";
		} else if ($organise == "1"){
			$organise = "name";
			$sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}";
		} else if ($organise == "0"){
			$organise = "date";
			$sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}";
		} else {
			$organise = "name";
			$sql = "SELECT name,image,rating,id FROM `tv_shows` ORDER BY {$organise} LIMIT 24 OFFSET {$page}";
		}
		$type = "tv_shows";
		$essen = new Essentials();
		$essen->getList($organise,$page,$type,$db);
	}
}
