<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/recommend.css" />
	<title>Tracker - Recommendations</title>
	<body>
		<?php
			$id = $_SESSION['userID'];
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&filmRecommendations=$id");
			//var_dump($json);
			$obj = json_decode($json, true);
			$column = 0;
			$row = 0;
			$per_row = 1;
		?>

		<?php echo "<div class='show_container'>";
			echo "<table style='border-spacing:1.5em;top:15px'>"; 
				echo "<caption><h2>Recommendations</h></caption>";
					foreach($obj['films'] as $movie){
						echo "<div class='image'>";
						echo "<tr><td><a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?id=" . $movie['id'] . "'>";
						echo "<img class='cover' src='" . $movie['image'] . "'/></td></a></img>";
						echo "<td><p><b>Name:</b> " . $movie['name'] . "</p>";
						echo "<p><b>Date:</b> " . $movie['date'] . "</p>";
						echo "<p><b>Starring:</b> " . $movie['starring'] . "</p>";
						echo "<p><b>Rating:</b> " . $movie['rating'] . " stars.</p>";
						echo "<p><b>Directed By:</b> " . $movie['director'] . "</p>";
						echo "<p><b>Synopsis:</b>" . $movie['synopsis'] . "</p></tr></td>";
						$column++;
						if($column >= $per_row){
							$column=0;
							$row++;
							echo "<br />";
						}
					}
					echo "</div>";
			echo "</table>";
		echo "</div";
						echo "div style='float:left;'";
						echo "<p>Go to List: <select onChange='window.location.href=this.value;'>";
		 					echo "<option value=''>--</option>";
							echo "<option value='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
		 					echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
						echo "</select>";
						echo "</div>";
					;?>
				
			</table>
		</div>
	</body>
</html>
