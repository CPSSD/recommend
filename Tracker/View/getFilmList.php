<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - FilmList</title>
	<body>
		<?php

		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/View/Util.php'); 
		require_once('Tracker/config.php');
            	$organise = $_GET["organise"];
            	$page = $_GET["page"];
			
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=film&organise={$organise}&page={$page}");
			$obj = json_decode($json, true);
			$type = 'film';
			
			$column = 0;
			$row = 0;
			$per_row = 4;
			$util = new Util();
			?>
			
			<div class='organise'>
				<div style='float:center;margin-right:175px:'>
					<form action='searchResults.php' method='get'>
					<input type='text' name='searchFilm' placeholder='Search Films'>
					<input type='submit'>
					</form>
				</div>
				<div style='float:right;margin-right:175px:'>
				<?php
				echo "<p>Media Type: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>{$type}</option>";
					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
 					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
				echo "</select>";
				echo "</div>";
				echo "<p>Organise By: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>--</option>";
					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=0&page=0'>Name</option>";
 					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Release Date</option>";
 					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=2&page=0'>Rating</option>";
				echo "</select>";
			echo "</div>";
			
			echo "<div class='login'>";
				if ($_SESSION['userID']){
					echo "{$_SESSION['username']} is logged in";
					echo "<p></p>";
					echo "<a href='logout.php' >Logout</a>";
				}else{
					echo "<a href='signUp.html'><b>Sign up</b></a>";
					echo "<p>Or Login below:</p>";
					echo "<form name='login' method='POST' action= '../login.php'>";
					echo "<p>Username: <input type='text' name='username'></p>";
					echo "<p>Password : <input type='text' name='password'></p>";
					echo "<p><input type='submit' name='submit' value='login'></p>";
				}
			echo "</div>";
			
			
			echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?id=" . $movie['id'] . "'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "<p><b>Name:</b> " . $movie['name'] . "</p>";
				echo "<p><b>Date:</b> " . $movie['date'] . "</p>";
				echo "<p><b>Rating:</b> " . $movie['rating'] . " stars.</p>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
			}
			echo "</div>";
		?>
		<div class="navigation">
		<?php
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('film',$page-1,$organise) . "'>Previous Page</a> |";
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('film',$page+1,$organise) . "'> Next Page.</a>";
		?>
		</div>
	</body>
</html>
