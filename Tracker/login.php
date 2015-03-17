<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/config.php');

function getNumRows(){
	$db = new SQLite3('database.db');
	$rows = $db->query("SELECT COUNT(*) as count FROM users");
	$row = $rows->fetchArray();
	$numRows = $row['count'];
	return $numRows;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$username = $_POST['username'];
	$password = $_POST['password'];

	$db = new SQLite3('database.db');
	
	$stmt = $db->prepare('SELECT Id,username FROM `users` WHERE username = :username AND password = :password');
	$stmt->bindValue(':password',$password,SQLITE3_TEXT);
	$stmt->bindValue(':username',$username,SQLITE3_TEXT);
	$result = $stmt->execute();

	$rows = $result->fetchArray();
	$id = $rows['Id'];
	$username = $rows['username'];

	if($rows){
		session_start();
		$_SESSION['userID'] = $id;	
		$_SESSION['username'] = $username;
		$url = "{$GLOBALS['ip']}/Tracker/View/getLikes.php?type=films&page=0";
		header( "Location: $url" );

	}else{
		echo "Incorrect password or username";
	}
}

?>
