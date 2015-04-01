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
	
	public function getEpisodesFromDay($id_list, $date, $db){
		$data = [];
		$episode_tick = 0;
		foreach($id_list as $id){
			$retval = $db->query("SELECT id,name,location FROM 'tv_shows' WHERE id = {$id}");
			$row = $retval->fetchArray();
			$table = $row["location"];
			$show_data = $row;
			$retval = $db->query("SELECT season,episode,title FROM '{$table}' WHERE date = \"{$date}\"");
			while (($row = $retval->fetchArray()) != null){
				if($row["season"] < 10 && $row["season"][0] != "0") {
					$row["season"] = "0".$row["season"];
				}
				if($row["episode"] < 10 && $row["episode"][0] != "0") {
					$row["episode"] = "0".$row["episode"];
				}
				$data[$episode_tick] = json_encode(array(	"status" => "okay",
										"show" => $show_data["name"],
										"show-id" => $show_data["id"],
										"season" => $row["season"],
										"episode" => $row["episode"],
										"title" => $row["title"]
										));
				$episode_tick+=1;
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
		$episode_data = $this->getEpisodesFromDay($id_list, $date, $db);
		if ($show_tick != 0){
			echo ",";
		}
		echo '"'.$date.'":{"date":"'.$date.'",';
		echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
		echo '"episodes":[';
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
			$episode_data = $this->getEpisodesFromDay($id_list, $date, $db);
			if ($show_tick != 0){
				echo ",";
			}
			echo '"'.$date.'":{"date":"'.$date.'",';
			echo '"pretty-date":"'.$essen->getPrettyDate($date).'",';
			echo '"episodes":[';
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
				$episode_data = $this->getEpisodesFromDay($id_list, $date, $db);
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
