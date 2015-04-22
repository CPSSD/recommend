<?php

class Util{

 	function checkNextSeason($season,$id){
		
		$json = file_get_contents("{$GLOBALS["ip"]}index.php?type=tv_shows&id={$id}&season={$season}");		

		if(strpos($json,'okay') !== false){
			return true;
		}else{
			return false;
		}
	}

    function nextSeason($season,$id){
        return "{$GLOBALS["ip"]}View/getShow.php?id={$id}&season={$season}";
    }

	function checkNextPage($type,$page,$organise,$order){
		$json = file_get_contents("{$GLOBALS["ip"]}index.php?type={$type}&organise={$organise}&page={$page}&order={$order}");
		if(strpos($json,'okay') !== false){
			return true;
		}else{
			return false;				
		}
	}
   
	function checkNextLike($type,$page){
		$json = file_get_contents("{$GLOBALS["ip"]}index.php?type={$type}&page={$page}");
		if(strpos($json,'okay') !== false){
			return $page;
		}else{
			return '0';				
		}
	}

	function rowExists($db,$table,$type,$id){
		if(!isset($_SESSION['userID'])){
			return false;
		}
		$stmt = $db->prepare("SELECT userID,mediaID,mediaTable FROM `{$table}` WHERE userID = :userID AND mediaID = :mediaID AND mediaTable = :mediaTable");
		$stmt->bindValue(':mediaTable',$type,SQLITE3_TEXT);
		$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
		$stmt->bindValue(':mediaID',$id,SQLITE3_TEXT);
		$result = $stmt->execute();
		$row = $result->fetchArray();
		if($row){
			return true;
		}else return false;
	}
}
