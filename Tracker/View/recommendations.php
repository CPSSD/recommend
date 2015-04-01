<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
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

        <?php include_once('Tracker/View/navbar.php');?>
			
			<?php echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj1['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?type=films&id=" . $movie['id'] . "'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "<p><b>Name:</b> " . $movie['name'] . "<br />";
				echo "<b>Date:</b> " . $movie['date'] . "<br />";
				echo "<b>Rating:</b> " . $movie['rating'] . " stars.</p>";
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
