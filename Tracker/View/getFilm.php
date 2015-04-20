<?php session_start();?>

<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<title>Tracker - Film</title>
	<body>
		<?php
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
			require_once('Tracker/View/Util.php');
            $id = $_GET["id"];
			$uid = 0;
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=films&id={$id}"); 
			$movie = json_decode($json, true);
            if(!$movie){
                $_SESSION["message"] = "No Film with that ID";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }
			$id = $movie['id'];
			$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db'); 
			$util = new Util();
			
			$genre = substr($movie['genre'],8,-2);
			$genre = str_replace("+",", ",$genre);
			$type = "films";
            include_once("Tracker/View/navbar.php");
		?>
			<div class='show_container' style='padding-bottom:100px'>
				<div class='image' style='float:left'>
				<?php echo "<img class='cover' src='" . $movie['image'] . "'/>";?>
				</div>
				
				<div style='width:600px;float:right;text-align:center;'>
					<?php echo "<h2 class='title'>" . $movie['name'] . "</h2>";?>
					<div style='width:300px;text-align:left;margin-left:150px;'>
						<?php echo "<p><b>Synopsis: </b> " . $movie['synopsis'] . "</p>";
						echo "<p><b>Release Date:</b> " . $movie['date'] . "</p>";
						echo "<p><b>Runtime:</b> " . $movie['runtime'] . " minutes.</p>";
						echo "<p><b>Genre: </b> " . $genre . ".</p>";
						echo "<p><b>Starring:</b> " . $movie['starring'] . "</p>";
						echo "<p><b>Directed By:</b> " . $movie['director'] . "</p>";
						echo "<p><b>Rating:</b> " . $movie['rating'] . " stars.</p>";
						echo "<p><b>Age:</b> " . $movie['age'] . ".</p>";?>
					</div>
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
    					echo "Would you like to untrack this film?";
    					echo "<input type='submit' name='formSubmit' value='Untrack' />";
				echo "</form>";
			}?>
		</div>
		<div style='float:right;margin-right:180px'>
			<?php if(!$util->rowExists($db,"likes"))
            {
                echo "<form action='../Model/insertLikes.php?type={$type}&id={$id}' method='post'>";
        	        echo "Like Film to use for Recommendations!";
        			echo "<input type='checkbox' name='film[]' value='".$movie['name']."&&&".$movie['id']."&&&".$movie['image']."'>";
                    echo "<input type='submit' value='Submit'>";   
                echo "</form>";  
            }else{
                echo "<form action='../Model/insertLikes.php?type={$type}&id={$id}' method='post'>";
        	        echo "Don't like it anymore?";
        			echo "<input type='checkbox' name='film[]' value='".$movie['name']."&&&".$movie['id']."&&&".$movie['image']."'>";
                    echo "<input type='submit' value='Unlike'>"; 
                echo "</form>"; 
            }?>
			</form>
		</div>	
	</body>
</html>
