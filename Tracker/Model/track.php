<?php session_start();


set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/database.db');
require_once('config.php');


function createTrackTable($db){
	$sql = "CREATE TABLE IF NOT EXISTS track(mediaTable TEXT,userID INTEGER,mediaID TEXT)";
	$result = $db->query($sql);
}

function insert($db){
	$stmt = $db->prepare('INSERT INTO track(mediaTable, userID, mediaID) VALUES (:mediaTable, :userID , :mediaID)');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaID',$_GET['id'],SQLITE3_TEXT);
	$result = $stmt->execute();
}

function delete($db){
	$stmt = $db-> prepare('DELETE FROM track WHERE userID = :userID AND mediaID = :mediaID AND mediaTable = :mediaTable');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaID',$_GET['id'],SQLITE3_TEXT);
	$result = $stmt->execute();
}

function rowExists($db){
	$stmt = $db->prepare('SELECT userID, mediaID,mediaTable FROM `track` WHERE userID = :userID AND mediaID = :mediaID AND mediaTable = :mediaTable');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaID',$_GET['id'],SQLITE3_TEXT);
	$result = $stmt->execute();
	$row = $result->fetchArray();
	if($row){
		return true;
	}else return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userID'])){
	createTrackTable($db);
	if(rowExists($db)){
		delete($db);
	}else{ 
		insert($db);
	}
}

header('Location: ' . $_SERVER['HTTP_REFERER']);

?>
