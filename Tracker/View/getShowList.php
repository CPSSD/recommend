<?php session_start();?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ias.min.js"></script>
    <script src="js/submitlikes.js"></script>
    <script src="js/scroll.js"></script>
</head>
	<title>Tracker - ShowList</title>
	<body>
		<?php

			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/View/Util.php');
			require_once('Tracker/config.php'); 
			$organise = $_GET["organise"];
			$page = $_GET["page"];
            $order = $_GET["order"];
            $nextPage = $page+1;
            $previousPage = $page-1; 
			
			$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=tv_shows&organise={$organise}&page={$page}&order={$order}");
			$obj = json_decode($json, true);
			$db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db'); 
			$type = 'tv_shows';
			$column = 0;
			$row = 0;
			$per_row = 4;	
			$util = new Util();
		?>
        <?php include_once('Tracker/View/navbar.php');?>
        
			<?php
			echo "<div class='show_container'>";
			# Displays info for each show.
			foreach($obj['tv_shows'] as $show){
				echo "<div class='image'>";
				echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShow.php?type=tv_shows&id=" . $show['id'] . "&season=1'>";
				echo "<img class='cover' src='" . $show['image'] . "'/>";
                    echo "<div class='likeButton'>";
                        if(isset($_SESSION["userID"])){
                            if(!$util->rowExists($db,"likes",$movie["id"])){
                                echo "<form id='like' name='like'>";
                                    echo "<input id='title' type='hidden' value='".$movie['name']."'>";
                                    echo "<input id='id' type='hidden' value='".$movie['id']."'>";
                                    echo "<input id='image' type='hidden' value='".$movie['image']."'>";
                                    echo "<input id='type' type='hidden' value='films'>";
                                    echo "<input id='submit' type='submit' value='like'>";
                                echo "</form>";
                            }else{
                                echo "<form id='like' name='like'>";
                                    echo "<input id='title' type='hidden' value='".$movie['name']."'>";
                                    echo "<input id='id' type='hidden' value='".$movie['id']."'>";
                                    echo "<input id='image' type='hidden' value='".$movie['image']."'>";
                                    echo "<input id='type' type='hidden' value='films'>";
                                    echo "<input id='submit' type='submit' value='unlike'>";
                                echo "</form>";
                            }
                        }
                    echo "</div>";
				echo "<p><b>Name:</b> " . $show['name'] . "<br/>";
				echo "<b>Rating:</b> " . $show['rating'] . " stars.</p>";
				echo "</a></div>";
				$column++;
				if($column >= $per_row){
					$column=0;
					$row++;
					echo "<br />";
				}
			}?>
		    <div class="navigation" style="text-align:center;">
                <?php echo "<a href='{$GLOBALS["ip"]}Tracker/View/getShowList.php?type={$type}&organise={$organise}&page={$nextPage}&order={$order}'></a>";?>
		    </div>
        </div>
	</body>
</html>
