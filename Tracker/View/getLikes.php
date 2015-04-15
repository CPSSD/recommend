<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/likeStyle.css" />
	<title>Tracker - GetLikes</title>
	<body>
	<?php
		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/View/Util.php'); 
		require_once('Tracker/config.php');
        $page = $_GET["page"];
		$type = $_GET['type'];
		if($type == "films"){
			$media = "film";
		} else {
            $media = "tv_shows";
        }

		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type={$type}&organise=3&page={$page}&order=DESC");    
		$obj = json_decode($json, true);
		
		$column = 0;
		$row = 0;
		$per_row = 3;
		$util = new Util();
        include_once('Tracker/View/navbar.php');	
		$tick = 0;
		echo "<div class='show_container'>";
			echo "<form method='post' action ='../Model/insertLikes.php?type={$type}&page={$page}'>";
			echo "<table style='width:100%;text-align:left;'>";
			echo "<tr>";
			echo "<div class='submit'><input class='submit' type='submit' value='Submit your likes!'></div>";
			foreach($obj[$type] as $movie){
				$name = $movie['name'];
				if (strlen($name) > 20){
					$name = substr($name,0,20) . "...";			
				}
				echo "<td><div class='image'><input type='checkbox' id='{$tick}' name='film[]' value='".$movie['name']."&&&".$movie['id']."&&&".$movie['image']."'>";	
                echo "<label for='{$tick}'><img class='cover' src='" . $movie['image'] . "'/><p></p>"; 
				echo "<div class='movie-title'><p class='movie-title'>" .$name."</p>";
				echo "</div></label></div>";		
				echo "</td>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "</tr>";
					//echo "<br />";
				}
				$tick++;
			}
			echo "</table>";
			?>
			<input class='submit' type='submit' value='Submit your likes!'>
			</form>		
			
		</div>
		<div class="navigation">
		<?php
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getLikes.php?type=films&page=" . $util->checkNextLike($type,$page-1) . "'>Previous Page</a> |";
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/getLikes.php?type=films&page=" . $util->checkNextLike($type,$page+1) . "'> Next Page.</a>";
		?>
	
		</div>
	</body>
</html>
