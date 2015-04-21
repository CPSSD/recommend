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
	<title>Tracker - ShowList</title>
	<body>
		<?php

			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('View/Util.php');
			require_once('config.php'); 
			$organise = $_GET["organise"];
			$page = $_GET["page"];
            $order = $_GET["order"];
            $nextPage = $page+1;
            $previousPage = $page-1; 
			
			$json = file_get_contents("{$GLOBALS["ip"]}index.php?type=tv_shows&organise={$organise}&page={$page}&order={$order}");
			$obj = json_decode($json, true);
			
			$type = 'tv_shows';
			$column = 0;
			$row = 0;
			$per_row = 4;	
			$util = new Util();
		?>
        <?php include_once('View/navbar.php');?>
        
			<?php
			echo "<div class='show_container'>";
			# Displays info for each show.
			foreach($obj['tv_shows'] as $show){
				echo "<div class='image'>";
					echo "<a href='{$GLOBALS["ip"]}View/getShow.php?type=tv_shows&id=" . $show['id'] . "&season=1'>";
					echo "<div class='cover_title'><p class='cover_title'>". $show['name'] . "</p></div>";
					echo "<img class='cover' src='" . $show['image'] . "'/>";
					if($show['rating'] != "Unknown"){
						$show['rating'] = $show['rating'] . " stars";
					}
					echo "<div class='cover_info'><p class='cover_info'><b>Rating:</b> " . $show['rating'] . "</p></div>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
			}?>
		    <div class="navigation" style="text-align:center;">
                <?php echo "<a href='{$GLOBALS["ip"]}View/getShowList.php?type={$type}&organise={$organise}&page={$nextPage}&order={$order}'></a>";?>
		    </div>
        </div>
	</body>
</html>
