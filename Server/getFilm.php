<?php
require_once('connect.php');

$conn = new DatabaseConnection();

$id = $_GET["id"];

//makes array of the query string
$paramArray = explode('&',$_SERVER["QUERY_STRING"]);

$maxID = 'SELECT * FROM films ORDER BY id DESC LIMIT 1';

$result = mysql_query($maxID);
$maxID = mysql_fetch_assoc($result);
$maxID = intval($maxID["id"]);
$idInt = intval($id);

//checks for valid input, if valid - selects id.
if(!isset($id)){
    echo json_encode(array("status" => "error", "code" => 101, "message" => "Missing parameter in request"));
}else if( $idInt < 1 || $idInt > $maxID ){
    echo json_encode(array("status" => "error","code" => 102, "message" => "Incorrect code in parameter"));
}else if(count($paramArray) > 1){
    echo json_encode(array("status" => "error","code" => 103, "message" => "Unknown parameter in request"));
}else{
    $sql = "SELECT * FROM `films` WHERE id = '".$id."'";
}

$retval = mysql_query($sql);

while($row = mysql_fetch_assoc($retval)){

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

mysql_close($conn);

?>
