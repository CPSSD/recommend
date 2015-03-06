
<html>
<link rel="stylesheet" type="text/css" href="css/showStyle.css" />
	<title>Tracker - Show</title>
	<body>
		<?php
set_include_path('/var/www/html');
require_once('Tracker/View/Util.php'); 
            $id = $_GET["id"];
			$season = $_GET["season"];
			$json = file_get_contents("http://localhost/Tracker/index.php?type=tv_shows&id={$id}&season={$season}");
			$obj = json_decode($json, true);
			$seasonUp = strval(intval($season)+1); 
			$seasonDown = strval(intval($season)-1); 
			$util = new Util();

			echo "<div class='navigation'>";
				echo "<a href=".$util->checkNextSeason($seasonDown,$id).">Previous Season |</a>";
				echo "<a href=".$util->checkNextSeason($seasonUp,$id)."> Next Season</a>";
			echo "</div>";

			echo "<div class='organise'>";
				echo "<div style='float:right;margin-right:175px:'>";
				echo "<p>Media Type: <select onChange='window.location.href=this.value;'>";
	 				echo "<option value=''>--</option>";
					echo "<option value='http://localhost/Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
	 				echo "<option value='http://localhost/Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
				echo "</select>";
				echo "</div>";
			echo "</div>";

			echo "<div class='show_container'>";
					echo "<div class='image' style='text-align:left'>";
					echo "<img class='cover' src='" . $obj['image'] . "'/>";
					echo "<p><b>Rating:</b> " . $obj['rating'] . " stars.</p>";
					echo "<p><b>Age:</b> " . $obj['age'] . ".</p>";
					echo "<p><b>Genre:</b> " . $obj['genre'] . ".</p>";
					echo "</div>";
					echo "<div style='width:500px;float:right;'>";
					
					echo "<h3 class='title'><em> Name: </em><u>" . $obj['name'] . "</u></h2>";
					foreach($obj['show'] as $show){
						echo "<div class='episode'>";
							echo "<p><em>Episode " .$show['episode']. ":</em> " .$show['tile']. ". " .$show['date']. "<br></p>";
						echo "</div>";
					}
			echo "</div>"			
		?>
	</body>
</html>
