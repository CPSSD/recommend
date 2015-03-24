<?php session_start();

$db = new SQLite3('database.db');
set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/config.php');

function createLikeTable($db){
	$db = new SQLite3('database.db');
	$sql = "CREATE TABLE IF NOT EXISTS likes(mediaTable TEXT,userID INTEGER,mediaName TEXT)";
	$result = $db->query($sql);
}

function insert($db,$mediaName){
	$db = new SQLite3('database.db');	
	$stmt = $db->prepare('INSERT INTO likes(mediaTable, userID, mediaName) VALUES (:mediaTable, :userID , :mediaName)');
	$stmt->bindValue(':mediaTable',$_GET['type'],SQLITE3_TEXT);
	$stmt->bindValue(':userID',$_SESSION['userID'],SQLITE3_INTEGER);
	$stmt->bindValue(':mediaName',$mediaName,SQLITE3_TEXT);
	$result = $stmt->execute();
}

function rowExists($db,$mediaName){
	$db = new SQLite3('database.db');
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
		if(!rowExists($db,$media)){
				var_dump($_POST['film']);
			insert($db,$media);
		}	
	}
}

$url = $_SERVER['HTTP_REFERER'];
header( "Location: $url" );

?>

