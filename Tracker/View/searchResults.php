<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - Search Results</title>
	<body>
	<?php 
		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/config.php');
		if(isset($_GET['searchFilm'])){
			$film = $_GET['searchFilm'];
			$url = "{$GLOBALS["ip"]}Tracker/index.php?type=films&searchFilm={$film}";
			$newURL = str_replace(' ','%20',$url);
			$json = file_get_contents($newURL);
			$type = "films";
			$x = "Film";
		}else if(isset($_GET['searchShow'])){
			$show = $_GET['searchShow'];
			$url = "{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&searchShow={$show}";
			$newURL = str_replace(' ','%20',$url);
			$json = file_get_contents($newURL);
			$type = "tv_shows";
			$x = "Show";
		}

		$obj = json_decode($json, true);

		if( empty($obj[$type]) ){
			$_SESSION['message'] = "No results for your selected search";
			//$url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			//header( "Location: $url" );
		}
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
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/get{$x}.php?id=" . $movie['id'] . "&season=1'>";
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
