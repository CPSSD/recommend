<?php session_start();?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ias.min.js"></script>
    <script src="js/submitlikes.js"></script>
    <script src="js/toTop.js"></script>
    <script src="js/scroll.js"></script>
</head>

    <title>View Likes</title>
    <body>

        <?php
            set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
            include_once('config.php');
            include_once('View/Util.php');
            $jsonFilms = file_get_contents("{$GLOBALS["ip"]}index.php?type=films&userLikes={$_SESSION['userID']}");
            $jsonShows = file_get_contents("{$GLOBALS["ip"]}index.php?type=tv_shows&userLikes={$_SESSION['userID']}");
            $objFilms = json_decode($jsonFilms, true);

            $objShows = json_decode($jsonShows, true);
            include_once('View/navbar.php');
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();

            if(!$objShows && !$objFilms){
                $_SESSION["message"] = "You haven't liked anything yet, get liking!!";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            } 
        ?>        
        <div class='show_container'>
        <?php
            foreach($objFilms['films'] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}View/getFilm.php?type=films&id=" . $movie['mediaID'] . "'>";
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
				echo "<a href='{$GLOBALS["ip"]}View/getShow.php?type=films&season=1&id=" . $show['mediaID'] . "'>";
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

