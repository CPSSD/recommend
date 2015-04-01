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
            $order = $_GET["order"];
			
		    $json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&organise={$organise}&page={$page}&order={$order}");
		    $obj = json_decode($json, true);
            if(!$obj){
                $_SESSION["message"] = "You're Page Value is too high or too low!!";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }
		    $type = 'films';
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();
		?>

        <?php include_once('Tracker/View/navbar.php');?>
			
			<?php echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj['films'] as $movie){
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
