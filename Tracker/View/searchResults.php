<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - Search Results</title>
	<body>
	<?php 
		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/config.php');
		if(isset($_GET['searchFilm'])){
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&searchFilm={$_GET['searchFilm']}");
			$type = "films";
		}else if(isset($_GET['searchShow'])){
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&searchShow={$_GET['searchShow']}"); 
			$type = "tv_shows";
		}else{
			echo "Your search returned no results";
		}

		$obj = json_decode($json, true);	
		$column = 0;
		$row = 0;
		$per_row = 4;
				
		echo "<div class='organise'>";
			echo "<p>Go To: <select onChange='window.location.href=this.value;'>";
 				echo "<option value=''>--</option>";
				echo "<option value='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
 				echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
			echo "</select>";
		echo "</div>";
		echo "<div class='show_container'>";
			foreach($obj[$type] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShow.php?type=tv_show&season=1&id=" . $movie['id'] . "'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "<p><b>Name:</b> " . $movie['name'] . "</p>";
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

	</body>
</html>
