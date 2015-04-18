<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/styleList.css" />
    <title>Process Search</title>
    <body>
            <?php
                set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
                include_once('Tracker/View/navbar.php');
		        require_once('Tracker/config.php');
                require_once('Tracker/View/Util.php');
                $type = $_POST["type"];
                if($type == "films"){
                    $x = "Film";
                }else{
                    $x = "Show";
                }
                $param = $_POST["params"];
                $params = implode(",",$param);
                if($_POST["rating"] == ""){
                    $rating = "0";
                }else{
                    $rating = $_POST["rating"];         
                }
		        $url = "{$GLOBALS["ip"]}Tracker/index.php?type={$type}&param={$params}&rating={$rating}";
		        $newURL = str_replace(' ','%20',$url);
		        $json = file_get_contents($newURL);
                $column = 0;
		        $row = 0;
		        $per_row = 4;
		        $util = new Util();
		        $obj = json_decode($json,true);

		        if( empty($obj[$type]) ){
			        $_SESSION["message"] = "No results for your selected search";
			        $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			        header( "Location: $url" );
		        }

		 echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj[$type] as $movie){
				echo "<div class='image'>";
                echo $movie['id'];
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/get{$x}.php?type={$type}&id=" . $movie['id'] . "&season=1'>";
				echo "<img class='cover' src='" . $movie['image'] . "'/>";
				echo "<p><b>Name:</b> " . $movie['name'] . "<br />";
				//echo "<b>Date:</b> " . $movie['date'] . "<br />";
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
    </body>
</html>
