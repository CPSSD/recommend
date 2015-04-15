<?php
set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Film.php'); 
include_once('Tracker/Model/TV.php'); 

class Essentials{

	public $film;
	public $tv_show;

	public function __construct(){
		$this->film = new Film('database.db');
		$this->tv_show = new TV('database.db');
	}

	public function get($db,$type,$id,$season){
		if($type == "films"){
			$this->film->getFilm($db,$type,$id);
		}else{
			$this->tv_show->getShow($db,$type,$id,$season);
		}
	}

	// function to return all of a particular media a user likes
	public function userLikes($db,$type,$userID){   
        $sql = "SELECT mediaName,mediaTable,mediaImage,mediaID FROM likes WHERE userID={$userID} AND mediaTable LIKE '%{$type}%'";
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

    public function recommendations($db,$type,$userID){
		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type={$type}&userLikes={$userID}");
		$obj = json_decode($json, true);

		//gets all liked films and puts them in an array
		$films = array();
		foreach($obj[$type] as $movie){
			$films[] = $movie['mediaName'];	
		}
		//var_dump($films);
		//gathers the genres of these films
		$genre = array();
		foreach($films as $film){
			$sql = "SELECT genre FROM {$type} WHERE name='{$film}'";
			$result = $db->query($sql);
			while($row = $result->fetchArray()){
                $row = $row['genre'];
                if($type == "films"){
	    			$temp = substr($row,8,-2);
                }	
    		    $rows = explode("+",$temp);
				foreach($rows as $string){
					$genre[] = $string;
				}
			}
		}
        //var_dump($genre);
		//selects the highest valued genre
		$genre = array_count_values($genre);
		$max = max($genre);
		$key = array_search($max, $genre);	

        if($type == "films"){
		    $sql = "SELECT id,name,date,rating,image FROM {$type} WHERE genre LIKE '%{$key}%' ORDER BY rating DESC LIMIT 4";
		}else {
            $sql = "SELECT id,name,rating,image FROM {$type} WHERE genre LIKE '%{$key}%' ORDER BY rating DESC LIMIT 4";
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

	public function search($db,$type,$search){
		$search = str_replace('"','\"',$search);		
        if($type == "films"){
            $this->film->searchFilms($db,$type,$search);
        }else{
            $this->tv_show->searchShows($db,$type,$search);
        }
	}

    public function advancedSearch($db,$type,$param,$rating){
        if($type == "films"){
            $this->film->advancedFilmSearch($db,$type,$param,$rating);
        }else{
            $this->tv_show->advancedShowSearch($db,$type,$param,$rating);
        }
    }

	public function getMaxID($type,$db){
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
	
	public function generateMonthDates($year){
		$date1 = explode("-", "{$year}-01-01");
		$date2 = explode("-", "{$year}-12-31");
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
		$date_list[$tick] = "0000-00-00";
		
		return $date_list;
	}
	public function generateDates($date1, $offset, $backwards_offset, $depth){
		$timestamp = strtotime($date1);
		$date1 = explode("-", $date1);
		$dayOffset = date("N", $timestamp)-1+$backwards_offset;
		$date1 = $this->getDateOffset($date1, $dayOffset, "back");
		$date2 = $this->getDateOffset($date1, $offset*$depth, "forward");
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

	public function getList($db,$type,$organise,$page,$order){
		//$maxPage = floor($this->getMaxID($type,$db) / 24);

		if ($type == "films"){
			$this->film->getFilmList($db,$type,$organise,$page,$order);
		}else{
			$this->tv_show->getShowList($db,$type,$organise,$page,$order);
		}
	}

}
