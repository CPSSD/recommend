<?php

class Essentials{

	public function mediaRecommend($db,$userID,$type){
		$sql = "SELECT mediaName FROM likes WHERE userID={$userID}";
		
		$retval = $db->query($sql);
		echo "{\"{$type}\":[";
		$tick = 0;
		while($row = $retval->fetchArray()){
		    if ($tick != 0){
			echo ",";
		    }
		    $tick++;
		    echo json_encode($this->createArrayFromData($sql, $row));
		}
		echo "]}";
		
	}

	public function likes($db,$type,$page){
		$pageParam = intval($page);
		$offset = $pageParam * 30;
		$maxPage = floor($this->getMaxID($type) / 30);

		$sql = "SELECT name,id,image FROM {$type} ORDER BY name LIMIT 30 OFFSET {$offset}";
		$retval = $db->query($sql);
		echo "{\"{$type}\":[";
		$tick = 0;
		while($row = $retval->fetchArray()){
			if ($tick != 0){
				echo ",";
			}
			$tick++;
			echo json_encode($this->createArrayFromData($sql, $row));
		}
		echo "]}";
	}


	public function search($db,$type,$search){
		if (strpos($search,'%20') !== false){
			$sub = explode(' ',$search,2);
			echo $sub[0];
			echo " "; 
			echo $sub[1];
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


	public function getMaxID($type){
		$db = new SQLite3('database.db');
		$maxID = "SELECT id FROM `{$type}` WHERE id = (SELECT MAX(id) FROM {$type})";
		$result = $db->query($maxID);
		$maxID = $result->fetchArray();
		$maxID = intval($maxID["id"]);
		return $maxID;
	}

	public function createArrayFromData($sql, $row){
		$array = array();
		
		$data = explode("SELECT ", $sql)[1];
		$data = explode(" FROM", $data)[0];
		$data = explode(",", $data);
		
		$array["status"] = "okay";
		foreach ($data as $var_name) {
			$array[$var_name] = $row[$var_name];
		}
		
		return $array;
	}
	
	public function cleanDate($date){
		for ($i = 1; $i <= 2; $i++){
			if($date[$i] < 10 && $date[$i][0] != "0"){
				$date[$i] = "0".$date[$i];
			}
		}
		return $date;
	}
	
	public function getPrettyDate($date){
		$timestamp = strtotime($date);
		$prettydate = date("D, M jS", $timestamp);
		return $prettydate;
	}
	
	public function getDateOffset($date1, $offset, $direction){
		$date2 = $date1;
		// When Going Backwards.
		if ($direction == "back"){
			// If we dont need to go back a month.
			if ($date2[2] > $offset){
				$date2[2] = "" . ($date1[2] - $offset);
			} else { // if we do need to go back a month.
				// Get current days in said month.
				$date2[1] -= 1;
				$timestamp = strtotime($date1[0] . "-" . ($date1[1]-1) . "-" . $date1[2]);
				$daysInMonth = date("t", $timestamp);
				$date2[2] = "" . ($date1[2] - $offset + $daysInMonth);
			}
			// When Going Forwards.
		} else if($direction == "forward"){ 
			$timestamp = strtotime($date1[0] . "-" . $date1[1] . "-" . $date1[2]);
			$daysInMonth = date("t", $timestamp);
			// If total date + offset exceeds total days in month.
			if(($date2[2]+$offset-1) > $daysInMonth){
				$date2[1] = "" . ($date2[1]+1);
				$date2[2] = "" . (($date2[2]+$offset-1) - $daysInMonth);
			} else {
				$date2[2] = "" . ($date1[2] + $offset - 1);
			}
				
		}
		return $this->cleanDate($date2);
	}

	public function generateDates($date1){
		$timestamp = strtotime($date1);
		$date1 = explode("-", $date1);
		$dayOffset = date("N", $timestamp)-1+7;
		$date1 = $this->getDateOffset($date1, $dayOffset, "back");
		$date2 = $this->getDateOffset($date1, 7*5, "forward");
		$date_list = [];
		$tick = 0;
		
		# Add in year support eventually.
		$tick = 0;
		$y = $date1[0];
		for ($m = $date1[1]; $m <= $date2[1]; $m++) {
			if($m < 10 && $m[0] != "0") {
				$m = "0".$m;
			}
			$timestamp = strtotime("{$date1[0]}-{$m}-1");
			$endDate = date("t", $timestamp);
			if($m == $date2[1]){
				$endDate = $date2[2];
			}
			$startDate = 1;
			if($m == $date1[1]){
				$startDate = $date1[2];
			}
			for ($d = $startDate; $d <= $endDate; $d++){
				if($d < 10 && $d[0] != "0") {
					$d = "0".$d;
				}
				$date_list[$tick] = "{$y}-{$m}-{$d}";
				$tick++;
			}
		}
		
		return $date_list;
	}

	public function getList($organise,$page,$type,$db){
		$pageParam = intval($page);
		$offset = $pageParam * 24;
		$maxPage = floor($this->getMaxID($type) / 24);
		
		if($type == "films"){
			if($organise == "rating"){
				$sql = "SELECT name,date,image,rating,id FROM `{$type}` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$offset}";
			} else if($organise == "date"){
				$sql = "SELECT name,date,rating,image,id FROM `{$type}` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}";
			} else {
				$sql = "SELECT name,date,image,rating,id FROM `{$type}` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}";
			}		
		}else {
			if($organise == "name"){
				$sql = "SELECT name,image,rating,id FROM `{$type}` ORDER BY {$organise} LIMIT 24 OFFSET {$offset}";
			} else{
				$sql = "SELECT name,image,rating,id FROM `{$type}` ORDER BY {$organise} DESC LIMIT 24 OFFSET {$offset}";
			}		
		}	

		$retval = $db->query($sql);
		echo "{\"{$type}\":[";
		$tick = 0;
		while($row = $retval->fetchArray()){
		    if ($tick != 0){
			echo ",";
		    }
		    $tick++;
		    echo json_encode($this->createArrayFromData($sql, $row));
		}
		echo "]}";

	}

}
