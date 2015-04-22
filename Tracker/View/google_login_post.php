<?php
	session_start();
		
	set_include_path("{$_SERVER['DOCUMENT_ROOT']}");

	require_once('config.php');
	$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/database.db');
	
	function login($db, $google_id, $email) {
		$stmt = $db->prepare("SELECT Id,username FROM `users` WHERE google_id = '{$google_id}'");
		#$stmt->bindValue(':id',$google_id,SQLITE3_TEXT);
		$result = $stmt->execute();
		
		$rows = $result->fetchArray();
		$id = $rows['Id'];
		$username = $rows['username'];

		if($rows){
			$_SESSION['userID'] = $id;	
			$_SESSION['username'] = $username;
			echo "{$GLOBALS['ip']}";
		}else{
			echo "Something went terribly wrong...";
			createUser($db, $id, $email);
		}
	}
	
	function createUser($db, $id, $email) {
		# Creates the table if its not already there.
		$sql = "CREATE TABLE IF NOT EXISTS users(Id INTEGER PRIMARY KEY, fullname TEXT, username TEXT, email TEXT, password TEXT, google_id TEXT)";
		$result = $db->query($sql);
		
		# Make sure the user doesn't exist.
		$sql = "SELECT * FROM 'users' WHERE google_id = '{$id}'";
		$result = $db->query($sql);
		$row = $result->fetchArray();
		
		if ($row == false || $row == 0) {
			$fullname = $_POST['name'];
			$email = $_POST['email'];
			$username = $email;
			$password = $_POST['id'];
			$sql = "INSERT INTO users (fullname,username,email,password,google_id) VALUES ('{$fullname}', '{$username}', '{$email}', '{$password}', '{$id}')";
			$result = $db->query($sql);
		}
		login($db, $id, $email);
	}

	createUser($db, $_POST['id'], $_POST['email']);
?>

