<?php

class Essentials{

	public function search($db,$type){
		$sql = "SELECT name,image,id FROM `{$type}` ORDER BY name DESC LIMIT 24 OFFSET {$offset}";
	}

	public function getMaxID(){
		$db = new SQLite3('films.db');
		$maxID = 'SELECT * FROM films ORDER BY id DESC LIMIT 1';
		$result = $db->query($maxID);
		$maxID = $result->fetchArray();
		$maxID = intval($maxID["id"]);
		return $maxID;
	}

	public function getList($organise,$page,$type,$db){
		$pageParam = intval($page);
		$offset = $pageParam * 24;
		$maxPage = floor($this->getMaxID() / 24);

		if($organise == "rating"){
			$sql = "SELECT name,image,date,rating,id FROM `{$type}` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$offset}";
		}else if($type == "films"){
			$sql = "SELECT name,date,rating,image,id FROM `{$type}` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}";
		}else{
			$sql = "SELECT name,image,id FROM `{$type}` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}";
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
					   //"date" => $row["date"],
		                           //"rating" => $row["rating"],
					   "id" => $row["id"],
					   "image" => $row["image"]));
		}
		echo "]}";

	}

}
