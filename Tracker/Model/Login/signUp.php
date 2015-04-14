<?php session_start();

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/config.php');
$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db');

function createUserTable($db){
	$sql = "CREATE TABLE IF NOT EXISTS users(Id INTEGER PRIMARY KEY,fullname TEXT,username TEXT,email TEXT,password TEXT, google_id TEXT)";
	$result = $db->query($sql);
}

function newUser($db){
	$fullname = $_POST['name'];
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$sql = "INSERT INTO users (fullname,username,email,password,google_id) VALUES ('{$fullname}','{$username}','{$email}','{$password}','none')";
	$result = $db->query($sql);
	  echo "Your registration is complete";
}

function signUp($db){
	if(!empty($_POST['username'])){
		$sql = "SELECT * FROM `users` WHERE username = '{$_POST['username']}'";
		$result = $db->query($sql);
		$row = $result->fetchArray();
		if($row == false || $row == 0){
			newUser($db);
			return true;
		}else{
			return false;
		}
	}
}

if(isset($_POST['submit'])){
	createUserTable($db);
	if(SignUp($db)){
		$url = "{$GLOBALS['ip']}Tracker/View/login.html";
		header( "Location: $url" );	
	}else{
		$_SESSION["message"] = "Username already in use please try another!";
		$url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
		header( "Location: $url" );
	}
}

?>
