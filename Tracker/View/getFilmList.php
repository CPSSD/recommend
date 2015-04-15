<?php session_start();?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
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
	        require_once('Tracker/View/Util.php'); 
	        require_once('Tracker/config.php');
	        $organise = $_GET["organise"];
	        $page = $_GET["page"];
            $order = $_GET["order"];
            $nextPage = $page+1;
			
		    $json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&organise={$organise}&page={$page}&order={$order}");
		    $obj = json_decode($json, true);
            /*if(!$obj){
                $_SESSION["message"] = "You're Page Value is too high or too low!!";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }*/      
		?>

        <?php include_once('Tracker/View/navbar.php');?>
			
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
				    echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilm.php?type=films&id=" . $movie['id'] . "'>";
				    echo "<img class='cover' src='" . $movie['image'] . "'/>";
				    echo "<p><b>Name:</b> " . $movie['name'] . "<br>";
				    echo "<b>Date:</b> " . $movie['date'] . "<br>";
				    echo "<b>Rating:</b> " . $movie['rating'] . " stars.</p>";
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
			            <?php echo "<a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?type={$type}&organise={$organise}&page={$nextPage}&order={$order}'></a>";?>
            </div>
	    </div>
	</body>
</html>
