<?php

set_include_path('/var/www/html');
require_once($_SERVER['DOCUMENT_ROOT'].'/Tracker/Model/Essentials.php'); 

class Film extends SQLite3{

    public function getFilmList($organise,$page){
        $db = new SQLite3('films.db');
        
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
        $db = new SQLite3('films.db');
		$essen = new Essentials();
        if($essen->getMaxID() >= $id && $id > 0){
        $sql = "SELECT * FROM `films` WHERE id = '".$id."'";
        $result = $db->query($sql);
	    while($row = $result->fetchArray()){
            echo json_encode(array("status" => "okay",
                                   "name" => $row["name"],
                                   "date" => $row["date"],
								   "runtime" => $row["runtime"],
                                   "rating" => $row["rating"],
								   "starring" => $row["starring"],
								   "director" => $row["director"],
								   "synopsis" => $row["synopsis"],
								   "image" => $row["image"],
                                   "age" => $row["age"]));
            }
        }
    }
}
?>
