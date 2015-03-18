<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/config.php');
$db = new SQLite3('database.db');

function createUserTable($db){
	$sql = "CREATE TABLE IF NOT EXISTS users(Id INTEGER PRIMARY KEY,fullname TEXT,username TEXT,email TEXT,password TEXT)";
	$result = $db->query($sql);
}



function newUser($db){
	$fullname = $_POST['name'];
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$sql = "INSERT INTO users (fullname,username,email,password) VALUES ('{$fullname}','{$username}','{$email}','{$password}')";
	$result = $db->query($sql);
	  echo "Your registration is complete";
}

function signUp($db){
	if(!empty($_POST['username'])){
		$sql = "SELECT * FROM `users`// WHERE username = '{$_POST['username']}'";
		$result = $db->query($sql);
		$rows = count ($result); 
		if($result == false || $rows == 0){
			newUser($db);
		}else{
			echo "Sorry.. That username is already in use";
		}
	}
}

if(isset($_POST['submit'])){
	createUserTable($db);
	SignUp($db);
	$url = "{$GLOBALS['ip']}Tracker/View/login.html";
	header( "Location: $url" );
}

?>
