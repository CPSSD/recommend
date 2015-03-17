<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/showStyle.css" />
	<title>Tracker - Show</title>
	<body>
		<?php
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/View/Util.php'); 
			require_once('Tracker/config.php');
			$id = $_GET["id"];
			$season = $_GET["season"];
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&id={$id}&season={$season}");
			$obj = json_decode($json, true);
			$seasonUp = strval(intval($season)+1); 
			$seasonDown = strval(intval($season)-1); 
			$util = new Util();
			$type = "tv_shows";

			echo "<div class='navigation'>";
				echo "<a href=".$util->checkNextSeason($seasonDown,$id).">Previous Season |</a>";
				echo "<a href=".$util->checkNextSeason($seasonUp,$id)."> Next Season</a>";
			echo "</div>";
		?>
			<div class='organise'>
				<div style='float:right;margin-right:175px:'>
				<p>Media Type: <select onChange='window.location.href=this.value;'>
	 				<option value=''>--</option>
					<?php echo "<option value='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
	 				echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";?>
				</select>
				</div>
			</div>
		
			<div class='show_container'>
					<div class='image' style='text-align:left'>
					<?php echo "<img class='cover' src='" . $obj['image'] . "'/>";
					echo "<p><b>Rating:</b> " . $obj['rating'] . " stars.</p>";
					echo "<p><b>Age:</b> " . $obj['age'] . ".</p>";
					echo "<p><b>Genre:</b> " . $obj['genre'] . ".</p>";
					echo "</div>";
					echo "<div style='width:500px;float:right;'>";
					
					echo "<h3 class='title'><em> Name: </em><u>" . $obj['name'] . "</u></h2>";
					foreach($obj['show'] as $show){
						echo "<div class='episode'>";
							echo "<p><em>Episode " .$show['episode']. ":</em> " .$show['tile']. ". " .$show['date']. "<br></p>";
						echo"</div>";
					}?>
					</div>
			</div>	

		<div style='margin-left:180px;float:left;';>
			<?php echo "<form action='../track.php?type={$type}&id={$id}' method='post'>";?>
    				Would you like to track this show? 
    				<input type='submit' name='formSubmit' value='Track' /> 
			</form>
		</div>	

		<div style='float:right;margin-right:180px'>
			<?php echo "<form action='../insertLikes.php?type={$type}&id={$id}' method='post'>";?>
    				<?php echo "Would you like to use this to get Recommendations?";?>
    				<?php echo "<input type='checkbox' name='film[]' value='".$obj['name']."' /><input type='submit' value='Submit'>"; ?>
			</form>
		</div>		
	</body>
</html>
