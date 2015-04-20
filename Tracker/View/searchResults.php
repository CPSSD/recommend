<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<title>Tracker - Search Results</title>
	<body>
	<?php 
		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/config.php');
        $type = $_POST["type"];
        $search = $_POST["search"];
		$url = "{$GLOBALS["ip"]}Tracker/index.php?type={$type}&search={$search}";
		$newURL = str_replace(' ','%20',$url);
		$json = file_get_contents($newURL);
		$obj = json_decode($json,true);
        include_once("Tracker/View/navbar.php");


		if( empty($obj[$type]) ){
			$_SESSION["message"] = "No results for your selected search";
			$url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			header( "Location: $url" );
		}
		$column = 0;
		$row = 0;
		$per_row = 4;
				
		echo "<div class='show_container'>";
			foreach($obj[$type] as $movie){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/get{$x}.php?id=" . $movie['id'] . "&season=1'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "<p><b>Name:</b> " . $movie['name'] . "</p>";
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

	</body>
</html>
