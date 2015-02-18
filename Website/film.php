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
			
			echo "<div>";
			echo "<h2 class='title'>" . $movie['name'] . "</h1>";
			echo "</div>";
			echo "<div class='image'>";
			echo "<img class='cover' src='" . $movie['image'] . "'/>";
			echo "</div>";
		?>
		<div class="navigation">
			<p>
			<!-- To be added !-->
			</p>
		</div>
	</body>
</html>