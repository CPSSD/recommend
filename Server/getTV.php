<?php

$db = new SQLite3('tv_shows.db');;

$id = $_GET["id"];

$sql = "SELECT location FROM `tv_shows` WHERE id = '".$id."'";
$retval = $db->query($sql);
$row = $retval->fetchArray();

$table = $row["location"];

$a = "SELECT * FROM '".$table."'";
$b = $db->query($a);

while($c = $b->fetchArray()){

  echo json_encode(array("status" => "okay",
                         "id" => $c["id"],
                         "season" => $c["season"],
                         "episode" => $c["episode"],
                         "title" => $c["title"],
                         "date" => $c["date"]));

}

?>
