<html>
<link rel="stylesheet" type="text/css" href="css/style.css" />
	<title>Tracker - Homepage</title>
	<body>
		<?php
			$page = 7;
			if(!empty($_GET['p'])){
				$page = $_GET['p'];
				if($page < 0 || $page > 9){
					$page = 7;
				}
			}
			$json = file_get_contents('http://localhost:25565/Tracker_Server/getFilmList.php?organise=0&page=' . $page);
			$obj = json_decode($json, true);
			
			$column = 0;
			$row = 0;
			$per_row = 4;
			
			
			echo "<div class='show_container'>";
			# Displays the image for each movie.
			foreach($obj['movies'] as $movie){
				echo "<div class='image'>";
				echo "<a href='http://localhost:25565/Tracker/Film.php?id=" . $movie['id'] . "'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
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
			<p><?php
				echo "<a href='http://localhost:25565/Tracker/?p=" . ($page-1) . "'>Previous Page</a> | ";
				echo "<a href='http://localhost:25565/Tracker/?p=" . ($page+1) . "'>Next Page.</a>";
				?>
			</p>
		</div>
	</body>
</html>