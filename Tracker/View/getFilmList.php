<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - FilmList</title>
	<body>
		<?php

set_include_path('/var/www/html');
require_once('Tracker/View/Util.php'); 
            $organise = $_GET["organise"];
            $page = $_GET["page"];
			
			$json = file_get_contents("http://localhost/Tracker/index.php?type=film&organise={$organise}&page={$page}");
			$obj = json_decode($json, true);
			$type = 'film';
			
			$column = 0;
			$row = 0;
			$per_row = 4;
			$util = new Util();
			
			echo "<div class='organise'>";
				echo "<div style='float:center;margin-right:175px:'>";
					echo "<form name='form1' action='searchResults.php' method='get'>";
					echo "<input id='search' type='text' placeholder='Enter Show'>";
					echo "<input id='submit' type='submit' value='Search'>";
					echo "</form>";/* ** To be added ** */
				echo "</div>";
				echo "<div style='float:right;margin-right:175px:'>";
				echo "<p>Media Type: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>{$type}</option>";
					echo "<option value='http://localhost/Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
 					echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
				echo "</select>";
				echo "</div>";
				echo "<p>Organise By: <select onChange='window.location.href=this.value;'>";
 					echo "<option value=''>--</option>";
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
				<?php
			echo "<a href='http://localhost/Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('film',$page-1,$organise) . "'>Previous Page</a> |";
			echo "<a href='http://localhost/Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('film',$page+1,$organise) . "'> Next Page.</a>";
		?>
		</div>
	</body>
</html>
