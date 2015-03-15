<?php session_start();

$db = new SQLite3('database.db');
set_include_path('/var/www/html');
require_once('Tracker/config.php');


function createTrackTable($db){
	$db = new SQLite3('database.db');
	$sql = "CREATE TABLE IF NOT EXISTS track(mediaTable TEXT,userID INTEGER,mediaID TEXT)";
	$result = $db->query($sql);
}

function insert($db){
	$db = new SQLite3('database.db');	
	$stmt = $db->prepare('INSERT INTO track(mediaTable, userID, mediaID) VALUES (:mediaTable, :userID , :mediaID)');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaID',$_GET['id'],SQLITE3_TEXT);
	$result = $stmt->execute();
}

function rowExists($db){
	$db = new SQLite3('database.db');
	$stmt = $db->prepare('SELECT userID, mediaID,mediaTable FROM `track` WHERE userID = :userID AND mediaID = :mediaID AND mediaTable = :mediaTable');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaID',$_GET['id'],SQLITE3_TEXT);
	$result = $stmt->execute();
	$row = $result->fetchArray();
	var_dump($row);
	if($row){
		return true;
	}else return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userID'])){
	createTrackTable();
	if(rowExists()){
		echo "row exists";
	}else insert();
}

if($_GET['type'] == "films"){
	$x = "Film";
}else $x = "Show";

$url = "{$GLOBALS['ip']}/Tracker/View/get{$x}List.php?type={$_GET['type']}&organise=0&page=0";
header( "Location: $url" );

?>
