<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/connect.php');

$conn = new DatabaseConnection();

$organiseParam = $_GET["organise"];
$pageParam = $_GET["page"];

$page = intval($pageParam);
$offset = $page * 24; 


switch($organiseParam){
    case 0:
        $organise = "name";
        break;
    case 1:
        $organise = "date";
        break;
    case 2:
        $organise = "rating";
        break;
}

$paramArray = explode('&',$_SERVER["QUERY_STRING"]);

$maxID = 'SELECT * FROM films ORDER BY id DESC LIMIT 1';
$result = mysql_query($maxID);
$maxID = mysql_fetch_assoc($result);

$maxID = intval($maxID["id"]);
$maxPage = floor($maxID / 24);

if (!isset($organiseParam) || !isset($pageParam)){
    echo json_encode(array("status" => "error", "code" => 101, "message" => "Missing parameter in request"));
}else if ($organiseParam > 2 || $organiseParam == "" || $pageParam > $maxPage || $pageParam = ""){
    echo json_encode(array("status" => "error","code" => 102, "message" => "Incorrect code in parameter"));
}else if (count($paramArray) > 2){
    echo json_encode(array("status" => "error","code" => 103, "message" => "Unknown parameter in request"));
}else {
    $sql = "SELECT name,image,date,rating,id FROM `films` ORDER BY $organise LIMIT 24 OFFSET $offset";
}

$retval = mysql_query($sql);
echo "{\"movies\":[";
$tick = 0;
while($row = mysql_fetch_assoc($retval)){
  if ($tick != 0){
	echo ",";
  }
  $tick++;
  echo json_encode(array("status" => "okay",
                         "name" => $row["name"],
                         "date" => $row["date"],
                         "rating" => $row["rating"],
			 "id" => $row["id"],
			 "image" => $row["image"]));
}
echo "]}";

mysql_close($conn);
?>
