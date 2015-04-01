<?php session_start();


set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db');
require_once('Tracker/config.php');

function createLikeTable($db){
	$sql = "CREATE TABLE IF NOT EXISTS likes(userID INTEGER, mediaTable TEXT, mediaName TEXT, mediaID INTEGER, mediaImage TEXT)";
	$result = $db->query($sql);
}

function insert($db,$mediaName,$mediaID,$mediaImage){	
	$stmt = $db->prepare('INSERT INTO likes(userID,mediaTable,mediaName,mediaID,mediaImage) VALUES (:userID, :mediaTable, :mediaName, :mediaID, :mediaImage)');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaName',$mediaName,SQLITE3_TEXT);
	$stmt->bindValue(':mediaID',$mediaID,SQLITE3_INTEGER);
	$stmt->bindValue(':mediaImage',$mediaImage,SQLITE3_TEXT);
	$result = $stmt->execute();
}

function delete($db,$mediaName){
	$stmt = $db-> prepare('DELETE FROM likes WHERE userID = :userID AND mediaName = :mediaName AND mediaTable = :mediaTable');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaName',$mediaName,SQLITE3_TEXT);
	$result = $stmt->execute();
}

function rowExists($db,$mediaName){
	$stmt = $db->prepare('SELECT userID, mediaName,mediaTable FROM `likes` WHERE userID = :userID AND mediaName = :mediaName AND mediaTable = :mediaTable');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaName',$mediaName,SQLITE3_TEXT);
	$result = $stmt->execute();
	$row = $result->fetchArray();
	if($row){
		return true;
	}else return false;
}

$type = $_GET['type'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userID'])){
	createLikeTable($db);
	foreach($_POST['film'] as $media){
        $mediaInfo = explode("&&&",$media);
		if(!rowExists($db,$mediaInfo[0])){
			insert($db,$mediaInfo[0],$mediaInfo[1],$mediaInfo[2]);
		}else{
            delete($db,$mediaInfo[0]);
        }	
	}
}

$url = $_SERVER['HTTP_REFERER'];
header( "Location: $url" );

?>

