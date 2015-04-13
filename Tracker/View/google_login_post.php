<?php
	session_start();
	$_SESSION['username'] = $_POST['id'];
	$_SESSION['name'] = $_POST['name'];
	$_SESSION['email'] = $_POST['email'];
	echo "{$_SESSION['name']} signed in with Google+";
?>