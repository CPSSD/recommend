<?php session_start();?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ias.min.js"></script>
    <script type="text/javascript">
        //if($(window).scrollTop() == $(document).height() - $(window).height()*0.7){
            $(document).ready(function() {
            	// Infinite Ajax Scroll configuration
                jQuery.ias({
                    container : '.show_container', // main container where data goes to append
                    item: '.image', // single items
                    pagination: '.navigation', // page navigation
                    next: '.navigation a', // next page selector
                    loader: '<img src="css/ajax-loader.gif">', // loading gif
                    triggerPageThreshold: 3 // show load more if scroll more than this
                });
            });
        //}
    </script>
</head>

    <title>Tracker - FilmList</title>
        <body>
        <?php
            set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
	        require_once('View/Util.php'); 
	        require_once('config.php');
	        $organise = $_GET["organise"];
	        $page = $_GET["page"];
            $order = $_GET["order"];
			$uid = 0;
			if(isset($_SESSION["userID"])){
				$uid = $_SESSION["userID"];
			}
		    $json = file_get_contents("{$GLOBALS["ip"]}index.php?type=films&organise={$organise}&page={$page}&order={$order}&uid={$uid}");
            $nextPage = $page+1;
		    $obj = json_decode($json, true);
            /*if(!$obj){
                $_SESSION["message"] = "You're Page Value is too high or too low!!";
			    $url = "{$GLOBALS['ip']}View/displayMessage.php";
			    header( "Location: $url" );
            }*/      
		?>

        <?php include_once('View/navbar.php');?>
			
			<div class='show_container'>
            <?php
		    $type = 'films';
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();  
			    # Displays info for each movie.
			    foreach($obj['films'] as $movie){
				    echo "<div class='image'>";
				    echo "<a href='{$GLOBALS["ip"]}View/getFilm.php?type=films&id=" . $movie['id'] . "'>";
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
                        //echo "<p>hello</p>";
					    $column=0;
					    $row++;
					    echo "<br>";
				    }
			}?>
            <div class="ias_trigger">
                <a href="#">Load more Items</a>
            </div>
		
		    <div class="navigation" >
			            <?php echo "<a href='{$GLOBALS["ip"]}View/getFilmList.php?type={$type}&organise={$organise}&page={$nextPage}&order={$order}'></a>";?>
            </div>
	    </div>
	</body>
</html>
