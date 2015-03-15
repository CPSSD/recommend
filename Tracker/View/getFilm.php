<?php session_start();?>

<html>
<link rel="stylesheet" type="text/css" href="css/style.css" />
	<title>Tracker - Film</title>
	<body>
		<?php
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
                        $id = $_GET["id"];
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=film&id={$id}"); 
			$movie = json_decode($json, true);
			$id = $movie['id'];
			$type = "films";
			echo "<div class='show_container' style='padding-bottom:100px'>";
				echo "<div class='image' style='text-align:left'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "</div>";
				
				echo "<div style='width:600px;float:right;text-align:center'>";
					echo "<h2 class='title'>" . $movie['name'] . "</h1>";
					echo "<div style='width:300px;text-align:left;margin-left:150px'>";
						echo "<p><b>Synopsis: </b> " . $movie['synopsis'] . "</p>";
						echo "<p><b>Release Date:</b> " . $movie['date'] . "</p>";
						echo "<p><b>Runtime:</b> " . $movie['runtime'] . " minutes.</p>";
						echo "<p><b>Starring:</b> " . $movie['starring'] . "</p>";
						echo "<p><b>Directed By:</b> " . $movie['director'] . "</p>";
						echo "<p><b>Release Date:</b> " . $movie['date'] . "</p>";
						echo "<p><b>Rating:</b> " . $movie['rating'] . " stars.</p>";
						echo "<p><b>Age:</b> " . $movie['age'] . ".</p>";
					echo "</div>";
				echo "</div>";
			echo "</div>";	
		echo "<div style=margin-left:180px;>";
			echo "<form action='../track.php?type={$type}&id={$id}' method='post'>";
    				echo "Would you like to track this film? ";
    				echo "<input type='submit' name='formSubmit' value='Track' />"; 
			echo "</form>";
		echo "</div>";
		?>

	</body>
</html>
