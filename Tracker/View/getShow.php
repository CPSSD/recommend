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
            $db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db');

            if(!$obj){
                $_SESSION["message"] = "No Show with that ID";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }          

			$seasonUp = $season+1; 
			$seasonDown = $season-1; 
			$util = new Util();
			$type = "tv_shows";
            $genre = $obj['genre'];
            $genre = str_replace("+",", ",$genre);
            include_once("Tracker/View/navbar.php");

			echo "<div class='navigation'>";
                if($util->checkNextSeason($seasonDown,$id)){
                    echo "<a href=".$util->nextSeason($seasonDown,$id).">Previous Season |</a>";
                }
                if($util->checkNextSeason($seasonUp,$id)){
                    echo "<a href=".$util->nextSeason($seasonUp,$id)."> Next Season</a>";
                }
			echo "</div>";
		?>
		
			<div class='show_container'>
					<div class='image' style='text-align:left'>
					<?php echo "<img class='cover' src='" . $obj['image'] . "'/>";
					echo "<p><b>Rating:</b> " . $obj['rating'] . " stars.</p>";
					echo "<p><b>Genre:</b> " . $genre . ".</p>";
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

		<div style='margin-left:14%;float:left'>
			<?php if(!$util->rowExists($db,"track"))
			{
				echo "<form action='../Model/track.php?type={$type}&id={$id}' method='post'>";
    					echo "Would you like to track this film?";
    					echo "<input type='submit' name='formSubmit' value='Track' />";
				echo "</form>";
			}else {
				echo "<form action='../Model/track.php?type={$type}&id={$id}' method='post'>";
    					echo "Would you like to untrack this show?";
    					echo "<input type='submit' name='formSubmit' value='Untrack' />";
				echo "</form>";
			}?>
		</div>

		<div style='float:right;margin-right:180px'>
			<?php if(!$util->rowExists($db,"likes"))
            {
                echo "<form action='../Model/insertLikes.php?type={$type}&id={$id}' method='post'>";
        	        echo "Like Film to use for Recommendations!";
        			echo "<input type='checkbox' name='film[]' value='".$obj['name']."&&&".$obj['id']."&&&".$obj['image']."'>";
                    echo "<input type='submit' value='Submit'>";   
                echo "</form>";  
            }else{
                echo "<form action='../Model/insertLikes.php?type={$type}&id={$id}' method='post'>";
        	        echo "Don't like it anymore?";
        			echo "<input type='checkbox' name='film[]' value='".$obj['name']."&&&".$obj['id']."&&&".$obj['image']."'>";
                    echo "<input type='submit' value='Unlike'>"; 
                echo "</form>"; 
            }?>
			</form>
		</div>		
	</body>
</html>
