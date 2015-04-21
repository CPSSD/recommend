<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
    <title>View Likes</title>
    <body>

        <?php
            set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
            include_once('Tracker/config.php');
            include_once('Tracker/View/Util.php');
            $jsonFilms = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&userLikes={$_SESSION['userID']}");
            $jsonShows = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&userLikes={$_SESSION['userID']}");
            $objFilms = json_decode($jsonFilms, true);

            $objShows = json_decode($jsonShows, true);
            include_once('Tracker/View/navbar.php');
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();
        ?>        
        <div class='show_container'>
        <?php
            foreach($objFilms['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?type=films&id=" . $movie['mediaID'] . "'>";
				echo "<div class='cover_title'><p class='cover_title'>". $movie['mediaName'] . "</p></div>";
				echo "<img class='cover' src='" . $movie['mediaImage'] . "'/>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
			}
			
            foreach($objShows['tv_shows'] as $show){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShow.php?type=films&season=1&id=" . $show['mediaID'] . "'>";
				echo "<img class='cover' src='" . $show['mediaImage'] . "'/>";
				echo "<p><b>Name:</b> " . $show['mediaName'] . "<br />";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
            }
        ?>

        </div>
    </body>
</html>

