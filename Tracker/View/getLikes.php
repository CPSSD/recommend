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
		} else {$media = "tv_shows";}

		$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type={$type}&page={$page}");
		$obj = json_decode($json, true);
			
		$column = 0;
		$row = 0;
		$per_row = 3;
		$util = new Util();
	?>
		<div style='margin-top:20px;float:left'>
		<p>Media Type: <select onChange='window.location.href=this.value;'>";
 			<?php echo "<option value=''>{$type}</option>";
			echo "<option value='{$GLOBALS["ip"]}Tracker/View/getLikes.php?type=tv_shows&page=0'>Like TV Shows</option>";
 			echo "<option value='{$GLOBALS["ip"]}Tracker/View/getLikes.php?type=films&page=0'>Films</option>";?>
			</select>
		</div>
		<div style='margin-left:275px'>
			<?php echo "<p>Check the {$type} you like so we can make Recommendations for you!!</p>"?>
			<?php echo "<p><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?page=0&organise=0'><b>Main Page</b></a>";?>
		</div>

	<?php
		echo "<div class='show_container'>";
			echo "<form method='post' action ='../insertLikes.php?type={$type}&page={$page}'>";
			echo "<table style='width:100%;text-align:left;'>";
			echo "<tr>";
			foreach($obj[$type] as $movie){
				$name = $movie['name'];
				if (strlen($name) > 20){
					$name = substr($name,0,20) . "...";			
				}
				echo "<td><img class='cover' src='" . $movie['image'] . "'/><p></p>"; 
				echo "<div style='text-align:left;max-width:3px; white-space: nowrap;text-overflow: ellipsis;'>" .$name."";
				echo "<input type='checkbox' name='film[]' value='".$movie['name']."'>";
				echo "</div>";				
				echo "</td>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "</tr>";
					//echo "<br />";
				}
			}
			echo "</table>";
			?>
			<p>Submit likes From This Page!!<input type='submit' value='Submit'></p>
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
