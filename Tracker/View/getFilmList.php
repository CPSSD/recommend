<?php session_start();?>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="css/material.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery-ias.min.js"></script>
    <script src="js/submitlikes.js"></script>
    <script src="js/toTop.js"></script>
    <script src="js/scroll.js"></script>
</head>

    <title>Tracker - FilmList</title>
        <body>
        <?php
            set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
	        require_once('View/Util.php'); 
	        require_once('config.php');
	        $organise = $_GET["organise"];
	        $page = $_GET["page"];
            $order = $_GET["order"];
            $_POST = array();
			$uid = 0;
			if(isset($_SESSION["userID"])){
				$uid = $_SESSION["userID"];
			}
		    $json = file_get_contents("{$GLOBALS["ip"]}index.php?type=films&organise={$organise}&page={$page}&order={$order}&uid={$uid}");
            $nextPage = $page+1;
		    $obj = json_decode($json, true);
            /*if(!$obj){
                $_SESSION["message"] = "You're Page Value is too high or too low!!";
			    $url = "{$GLOBALS['ip']}View/displayMessage.php";
			    header( "Location: $url" );
            }*/      
		?>

        <?php include_once('View/navbar.php');?>
			
			<div class='show_container'>
            <?php
		    $type = 'films';
            $db = new SQLite3($_SERVER['DOCUMENT_ROOT'].'/Tracker/database.db'); 
		    $column = 0;
            $p = $_GET["page"];
            $index = $p*24;
            
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();  
			    # Displays info for each movie.
			    foreach($obj['films'] as $movie){
				    echo "<div class='image'>";
					$liked = "";
					if($util->rowExists($db,"likes","films",$movie['id'])){
						$liked = " liked";
					}
					
				    echo "<a href='{$GLOBALS["ip"]}View/getFilm.php?type=films&id=" . $movie['id'] . "'>";
				   	echo "<div class='cover_title'><p class='cover_title{$liked}'>". $movie['name'] . "</p></div>";
					echo "<img class='cover' src='" . $movie['image'] . "'/>";
                    if(isset($_SESSION["userID"])){
                        echo "<div class='likeButton'>";
                            if(!$util->rowExists($db,"likes","films",$movie["id"])){
                                echo "<form class='like' id='like' name='like'>";
                                    echo "<input id='title' type='hidden' value='".$movie['name']."'>";
                                    echo "<input id='id' type='hidden' value='".$movie['id']."'>";
                                    echo "<input id='image' type='hidden' value='".$movie['image']."'>";
                                    echo "<input id='type' type='hidden' value='films'>";
                                    echo "<input id='submit' type='submit' value='Like'>";
                                echo "</form>";
                            }else{
                                echo "<form class ='like' id='like' name='like'>";
                                    echo "<input id='title' type='hidden' value='".$movie['name']."'>";
                                    echo "<input id='id' type='hidden' value='".$movie['id']."'>";
                                    echo "<input id='image' type='hidden' value='".$movie['image']."'>";
                                    echo "<input id='type' type='hidden' value='films'>";
                                    echo "<input id='submit' type='submit' value='Unlike'>";
                                echo "</form>";
                            }
                        echo "</div>";
                        }
					if($movie['rating'] != "Unknown"){
						$movie['rating'] = $movie['rating'] . " stars";
					}
					echo "<div class='cover_info{$liked}'><p class='cover_info{$liked}'><b>Rating:</b> " . $movie['rating'] . "";
					echo "<br /><b>Date:</b> " . $movie['date'] . "<br></p></div>";
				    echo "</a></div>";
                    $index++;
				    $column++;
				    if($column >= $per_row){
					    $column=0;
					    $row++;
					    echo "<br>";
				    }
			    }?>

            <div class="ias_trigger">
                <a href="#">Load more Items</a>
            </div>

            <div id="back-top"><!-- scroll top button -->
                <a href="#top"><span></span>Back to Top</a>
            </div>
		
		    <div class="navigation" >
			    <?php echo "<a href='{$GLOBALS["ip"]}View/getFilmList.php?type={$type}&organise={$organise}&page={$nextPage}&order={$order}'></a>";
                    echo "<script type='text/javascript'>"; 
                        echo "$.getScript('js/submitlikes.js')";
                    echo "</script>";                      
                ?>          
            </div>
	    </div>
	</body>
</html>
