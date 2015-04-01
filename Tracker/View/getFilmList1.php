<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - FilmList</title>
	<body>
		<?php

		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/View/Util.php'); 
		require_once('Tracker/config.php');
            	$organise = $_GET["organise"];
            	$page = $_GET["page"];
			
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&organise={$organise}&page={$page}");
			$obj = json_decode($json, true);
			$type = 'films';
			
			$column = 0;
			$row = 0;
			$per_row = 4;
			$util = new Util();
			?>
			
			<!-- <nav>
			    <ul class='navbar'>
			        <li><a href="#">Media</a>
				    <ul> 
				        <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</a></li>";
				        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</a></li>";?>
				    </ul>
				</li>
			        <li><a href="#">Sort By</a>
				    <ul>
				        <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=0&page=0'>By Name</a></li>";
					echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>By Release Date</a></li>";
					echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=2&page=0'>By Rating</a></li>";?>
				    </ul>
				</li>
				<li>Entertainment Tracker</li>
			        <li><a href="#">Search</a>
				    <ul>
					<?php echo "<li>QUICK Search</li>";
					echo "<li>Advanced Search Options</li>";?>
				    </ul>
				</li>
			        <li><a href="#">User</a>
				    <ul>
					<?php 
					if (isset($_SESSION['userID'])){
					    echo "<li><a href='logout.php' >Logout</a></li>";
					}else{
					    echo "<li><a href='login.html'>Log In</a></li>";
					}
					?>
				    </ul>
				</li>
			    </ul>
			</nav>-->
			
			<?php echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?id=" . $movie['id'] . "'>";
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
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('films',$page-1,$organise) . "'>Previous Page</a> |";
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise={$organise}&page=" . $util->checkNextPage('films',$page+1,$organise) . "'> Next Page.</a>";
		?>
		</div>
	</body>
</html>
