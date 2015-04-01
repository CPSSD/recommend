<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Essentials.php'); 

class TV extends SQLite3{

    public function userShowLikes(){
    
    }

	public function searchShows($db,$type,$search){
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
				$row = $retval->fetchArray();
				if ($row != null){
					if ($episode_tick != 0){
						echo ",";
					}
					if($row["season"] < 10 && $row["season"][0] != "0") {
						$row["season"] = "0".$row["season"];
					}
					if($row["episode"] < 10 && $row["episode"][0] != "0") {
						$row["episode"] = "0".$row["episode"];
					}
					echo '{"show":"'.$show_data["name"].'",';
					echo '"show-id":'.$show_data["id"].',';
					echo '"season":"'.$row["season"].'",';
					echo '"episode":"'.$row["episode"].'",';
					echo '"title":"'.$row["title"].'"}';
					$episode_tick+=1;
				}
			}
			$show_tick += 1;
			$episode_tick = 0;
			echo "]}";
		}
		echo "}";
	}

	public function getShow($db,$type,$id,$season){
        $essen = new Essentials();
        if($essen->getMaxID($type,$db) >= $id && $id > 0){
		    $retval = $db->query("SELECT * FROM `tv_shows` WHERE id = {$id}");
		    $row = $retval->fetchArray();
		    $table = $row["location"];
		    $show_data = $row;

		    $sql2 = "SELECT * FROM {$table} WHERE season = {$season}";
		    $result = $db->query($sql2);
		
		    echo '{"name":"' . $show_data['name'] . '",';
		    echo '"id":"' . $show_data['id'] . '",';
		    echo '"rating":"' . $show_data['rating'] . '",';
            echo '"genre":"' . $show_data['genre'] . '",';
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
	}

	public function getShowList($db,$type,$organise,$page,$order){
		$pageParam = intval($page);
		$offset = $pageParam * 24;
        if($organise == "1"){
            $organise = "name";
            $sql = "SELECT name,image,genre,rating,id FROM `{$type}` ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
        }else{
            $organise = "rating";
            $sql = "SELECT name,image,genre,rating,id FROM `{$type}` ORDER BY {$organise} {$order} LIMIT 24 OFFSET {$offset}";
        }
		
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
}
