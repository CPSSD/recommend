<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - FilmList</title>
	<body>
		<?php
                        $organise = $_GET["organise"];
                        $page = $_GET["page"];
			
			$json = file_get_contents("http://localhost/Tracker/index.php?type=film&organise={$organise}&page={$page}");
			$obj = json_decode($json, true);
			
			$column = 0;
			$row = 0;
			$per_row = 4;
			
			echo "<div class='organise'>";
				echo "<div style='float:center;margin-right:175px:'>";
					echo "<form action='users.php' method='GET'>";
					echo "<input id='search' type='text' placeholder='Enter Film'>";
					echo "<input id='submit' type='submit' value='Search'>";
					echo "</form>";
				echo "</div>";
				echo "<div style='float:right;margin-right:175px:'>";
				echo "<p>Media Type: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>--</option>";
					echo "<option value='http://localhost/Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
 					echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
				echo "</select>";
				echo "</div>";
				echo "<p>Organise By: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>{--}</option>";
					echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=0&page=0'>Name</option>";
 					echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=1&page=0'>Release Date</option>";
 					echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=2&page=0'>Rating</option>";
				echo "</select>";
			echo "</div>";
			
			echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='http://localhost/Tracker/View/getFilm.php?type=film&id=" . $movie['id'] . "'>";
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
			<p><?php
				echo "<a href='http://localhost/Tracker/View/getFilmList.php?organise={$organise}&page=" . ($page-1) . "'>Previous Page</a> |";
				echo "<a href='http://localhost/Tracker/View/getFilmList.php?organise={$organise}&page=" . ($page+1) . "'>Next Page.</a>";
				?>
			</p>
		</div>
	</body>
</html>
