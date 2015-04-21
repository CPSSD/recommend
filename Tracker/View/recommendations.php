<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<title>Tracker - Recommendations</title>
	<body>
		<?php
			$id = $_SESSION['userID'];
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
            include_once('Tracker/View/Util.php');
            $type = $_GET['type'];
			$json1 = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&recommendations=$id");
            $json2 = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&recommendations=$id");
			$obj1 = json_decode($json1, true);
            $obj2 = json_decode($json2, true);
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();
		?>

        <?php //include_once('Tracker/View/navbar.php');?>
			
			<?php echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj1['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?type=films&id=" . $movie['id'] . "'>";
				echo "<div class='cover_title'><p class='cover_title'>". $movie['name'] . "</p></div>";
					echo "<img class='cover' src='" . $movie['image'] . "'/>";
					if($movie['rating'] != "Unknown"){
						$movie['rating'] = $movie['rating'] . " stars";
					}
					echo "<div class='cover_info'><p class='cover_info'><b>Rating:</b> " . $movie['rating'] . "";
					echo "<br /><b>Date:</b> " . $movie['date'] . "<br></p></div>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
            }
            foreach($obj2['tv_shows'] as $show){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShow.php?type=films&season=1&id=" . $show['id'] . "'>";
				echo "<img class='cover' src='" . $show['image'] . "'/>";
				echo "<p><b>Name:</b> " . $show['name'] . "<br />";
				echo "<b>Date:</b> " . $show['date'] . "<br />";
				echo "<b>Rating:</b> " . $show['rating'] . " stars.</p>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}             
            }			
			echo "</div>";?>

		</div>
	</body>
</html>
