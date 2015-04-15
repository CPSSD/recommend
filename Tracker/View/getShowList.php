<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
	<title>Tracker - ShowList</title>
	<body>
		<?php

			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/View/Util.php');
			require_once('Tracker/config.php'); 
			$organise = $_GET["organise"];
			$page = $_GET["page"];
            $order = $_GET["order"];
			
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&organise={$organise}&page={$page}&order={$order}");
			$obj = json_decode($json, true);
			
			$type = 'tv_shows';
			$column = 0;
			$row = 0;
			$per_row = 4;	
			$util = new Util();
		?>
        <?php include_once('Tracker/View/navbar.php');?>
        
			<?php
			echo "<div class='show_container'>";
			echo "{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&organise={$organise}&page={$page}&order={$order}";
			# Displays info for each show.
			foreach($obj['tv_shows'] as $show){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShow.php?type=tv_shows&id=" . $show['id'] . "&season=1'>";
				echo "<img class='cover' src='" . $show['image'] . "'/>";
				echo "<p><b>Name:</b> " . $show['name'] . "<br/>";
				echo "<b>Rating:</b> " . $show['rating'] . " stars.</p>";
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
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise={$organise}&page=" . $util->checkNextPage('tv_shows',$page-1,$organise) . "'>Previous Page</a> |";
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise={$organise}&page=" . $util->checkNextPage('tv_shows',$page+1,$organise) . "'> Next Page.</a>";
		?>
		</div>
	</body>
</html>
