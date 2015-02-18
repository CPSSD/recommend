<html>
<link rel="stylesheet" type="text/css" href="css/style.css" />
	<title>Tracker - Homepage</title>
	<body>
		<?php
			$id = 7;
			if(!empty($_GET['id'])){
				$id = $_GET['id'];
			}
			$json = file_get_contents('http://localhost:25565/Tracker_Server/getFilm.php?id=' . $id);
			$movie = json_decode($json, true);
			echo "<div class='show_container' style='padding-bottom:100px'>";
				echo "<div class='image' style='text-align:right'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
					echo "<div style='text-align:left;margin-left:20px'>";
					echo "<p><b>Release Date:</b> " . $movie['date'] . "</p>";
					echo "<p><b>Runtime:</b> " . $movie['runtime'] . " minutes.</p>";
					echo "<p><b>Rating:</b> " . $movie['rating'] . " stars.</p>";
					echo "<p><b>Age:</b> " . $movie['age'] . ".</p>";
					echo "</div>";
				echo "</div>";
				
				echo "<div style='width:600px;float:left;text-align:center'>";
					echo "<h2 class='title'>" . $movie['name'] . "</h1>";
					echo "<div style='width:300px;text-align:center;margin-left:150px'>";
						echo "<br /><p>" . $movie['synopsis'] . "</p>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
		?>
		<div class="navigation">
			<p>
			<!-- To be added !-->
			</p>
		</div>
	</body>
</html>