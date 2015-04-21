<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
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
            //echo $json;
            /*if(!$obj){
                $_SESSION["message"] = "No Show with that ID";
			    $url = "{$GLOBALS['ip']}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }    */      

			$seasonUp = $season+1; 
			$seasonDown = $season-1; 
			$util = new Util();
			$type = "tv_shows";
            $genre = $obj['genre'];
            $genre = str_replace("+",", ",$genre);
            include_once("Tracker/View/navbar.php");
		?>
		
			<div class='show_container'>
					<div class='image' style='float:left'>
					    <?php echo "<img class='cover' src='" . $obj['image'] . "'>";?>
                            <div class='show_info'>
                                <?php echo "<p class='show_info'>".$obj['total_seasons']." Seasons</p>";
                                      echo "<p class='show_info'>".$obj['total_episodes']." Episodes</p>";
                                      if($genre){
                                          echo "<p class='show_info double_info'>".$genre."</p>";
                                      }
                                      if($obj['rating'] != 0){
                                          echo "<p class='show_info double_info'>".$obj['rating']." Stars</p>";
                                      }
									  echo "<br />";
									  
									  // Tracking
									  if(!$util->rowExists($db,"track","tv_shows")){
											echo "<form action='../Model/track.php?type=tv_shows&id={$id}' method='post'>";
												echo "<label for='track'><p class='show_info button'>Track</p></label>";
												echo "<input style='position:absolute;visibility:hidden' id='track' type='submit' name='formSubmit' value='Track' />";
											echo "</form>";
										} else {
											echo "<form action='../Model/track.php?type=tv_shows&id={$id}' method='post'>";
												echo "<a href'#'><label for='track'><p class='show_info button'>Untrack</p></label></a>";
												echo "<input style='position:absolute;visibility:hidden' id='track' type='submit' name='formSubmit' value='Untrack' />";
											echo "</form>";
										}
										
										// Likes
										if(!$util->rowExists($db,"likes","tv_shows")){
											// Track
											echo "<form action='../Model/insertLikes.php?type=tv_shows&id={$id}' method='post'>";
												echo "<label for='like'><p class='show_info button'>Like</p></label>";
												echo "<input style='position:absolute;visibility:hidden' id='like' type='submit' name='formSubmit' value='like' />";
											echo "</form>";  
										} else {
											echo "<form action='../Model/insertLikes.php?type=tv_shows&id={$id}' method='post'>";
												echo "<label for='like'><p class='show_info button'>Unlike</p></label>";
												echo "<input style='position:absolute;visibility:hidden' id='like' type='submit' name='formSubmit' value='like' />";
											echo "</form>";  
										}
										
							function generate_table($obj){
								$s = "";
								foreach($obj['show'] as $show){
								   $s = $s . "<tr>";
										if($show['season'] < 10) { $show['season'] = "0".$show['season']; }
										if($show['episode'] < 10) { $show['episode'] = "0".$show['episode']; }
										$s = $s . "<td>S".$show['season']."E".$show['episode']."</td>";
										$s = $s . "<td>".$show['title']."</td>";
										$s = $s . "<td>".$show['date']."</td>";
									$s = $s .  "</tr>";
								}
								return $s;
                            }		
									?>         
                            </div>
                    </div>
										
                    <div class='info'>
                        <?php 
							echo "<div class='title'><h2 class='title'>".$obj['name']."</h2></div>";
                            echo "<div class='summary'><p class='summary''>" .$obj['synopsis']. "</p></div>";
							  
                            if($util->checkNextSeason($seasonDown,$id)){
                                echo "<p class='show_info button'><b><a href=".$util->nextSeason($seasonDown,$id).">Last Season</a></b></p>";
                            } else {
							    echo "<p class='show_info button inactive'>Last Season</p>";
                           }
                            echo "<a href='#' onclick='change()'><div class='show_table_info button' id='show_hide'><b>Show Season Information...</b></div></a>";
							if($util->checkNextSeason($seasonUp,$id)){
                                echo "<p class='show_info button'><b><a href=".$util->nextSeason($seasonUp,$id).">Next Season</a></b></p>";
                            } else {
								echo "<p class='show_info button inactive'>Next Season</p>";
                            }
						?>
						<script>
						var visible = true;
						
						function init(){
							if("<?php if ($season == 1){ echo "True"; } else { echo "False"; } ?>" == "True"){
								console.log("Boop");
							} else {	
								console.log("not boop");
								change();
							}
						}
						function change(){
							if(visible){
								hide();
							} else {
								show();
							}
							visible = !visible;
						}
						
						function show(){
							document.getElementById("show_hide").innerHTML = "Hide Season Information";
							var table = document.getElementById("table");
							table.innerHTML = " ";//<a href='#' onclick='change()'><tr>Show Season Information...</tr></a>";
							table.style.height = "10px";
						}
						
						function hide(){
							document.getElementById("show_hide").innerHTML = "Hide Season Information";
							var table = document.getElementById("table")
							table.innerHTML = " <?php $test = "<tr><th>Episode</th><th>Title</th><th>Release</th></tr>" . generate_table($obj); echo $test; ?>";
							table.style.height = "100px";
						}
						</script>
					
                        <table class='info' id='table'>
                            <?php 
							?>
                        </table>
                    </div>
			</div>
		</body>
</html>
