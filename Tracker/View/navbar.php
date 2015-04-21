<?php
$query = $_SERVER['QUERY_STRING'];
$query = substr($query,5,5);
$type = 0;
if(isset($_GET["type"])){
	$type = $_GET["type"];
}
if($query == "films"){
    $x = "Film";
    $media = "Films";
}else{ 
    $x = "Show";
    $media = "Tv Shows";
}
$order = 0;
if(isset($_GET["order"])){
	$order = $_GET["order"];
}
$organise = 0;
if(isset($_GET["organise"])){
	$organise = $_GET["organise"];
}

if($order == "ASC"){
    if($organise == 1){
        $sort = "Name(A-Z)";
    }else if($organise == 2){
        $sort = "Release Date(Old - New)";
    }else{
        $sort = "Rating(Low - High)";
    }
}else{
    if($organise == 1){
        $sort = "Name(Z-A)";
    }else if($organise == 2){
        $sort = "Release Date(New - Old)";
    }else{
        $sort = "Rating(High - Low)";
    }
}


set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('config.php');

?>        

<link rel="stylesheet" type="text/css" href="css/material_navbar.css" />
        <nav>
            <ul class='navbar'>
                <li><a href="#"><?php echo $media;?></a>
                    <ul> 
                        <?php echo "<li><a href='{$GLOBALS["ip"]}View/getShowList.php?type=tv_shows&organise=3&page=0&order=DESC'>TV Shows</a></li>";
                        echo "<li><a href='{$GLOBALS["ip"]}View/getFilmList.php?type=films&organise=1&page=0&order=ASC'>Films</a></li>";?>
				    </ul>
				</li>
			        <li><a href="#"><?php echo $sort; ?></a>
				    <ul>
				        <?php echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=1&page=0&order=ASC'>By Name(A-Z)</a></li>";
                            echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=1&page=0&order=DESC'>By Name(Z-A)</a></li>";
					        if ($media == "Films")
                            {
                                echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=2&page=0&order=ASC'>By Release Date(Old-New)</a></li>";
					            echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=2&page=0&order=DESC'>By Release Date(New-Old)</a></li>";
                            }
					        echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=3&page=0&order=ASC'>By Rating(Low-High)</a></li>";
					        echo "<li><a href='{$GLOBALS["ip"]}View/get{$x}List.php?type={$type}&organise=3&page=0&order=DESC'>By Rating(High-Low)</a></li>";?>
				    </ul>
				</li>
				</li>
				<li><a href="">Entertainment Tracker</a></li>
			    <li><a href="">Search</a>
				    <ul>
					    <?php 
                            if ($media == "Films"){
                                echo "<li><form action='searchResults.php?type=films' method='POST'>";
					            echo "<input type='text' name='search' placeholder='Film QuickSearch'>";
                                echo "<input type='hidden' name='type' value='films'>";
					            echo "<input type='submit'>";
					            echo "</form></li>";
                             }else{
                            echo "<li><form action='searchResults.php?type=tv_shows' method='POST'>";
					            echo "<input type='text' name='search' placeholder='TV Show QuickSearch'>";
                                echo "<input type='hidden' name='type' value='tv_shows'>";
					            echo "<input type='submit'>";
					        echo "</form></li>";
                            }
					    echo "<li><a href='advancedSearch.php'>Advanced Search Options</a></li>";
                        ?>
				    </ul>
				</li>
					<?php 
					    if (isset($_SESSION['userID'])){	
							include_once('google_login.php');
							echo "<div class='g-signin2' style='visibility:hidden;position:absolute;'></div>";
							echo "<li><a href='#'>{$_SESSION['username']}</a>";
							echo "<ul>";
					        echo "<li><a href='logout.php' onClick='signOut()'>Logout</a></li>";
                            echo "<li><a href='recommendations.php?type={$type}&recommendations={$_SESSION['userID']}'>Recommendations</a></li>";
                            echo "<li><a href='calendar.php?q=tv'>Personal Calendar</a></li>";
                            echo "<li><a href='post_to_calendar.php'>Push to Google Calendar</a></li>";
                            echo "<li><a href='viewLikes.php?type=films&userID={$_SESSION['userID']}'>View Likes</a></li>";
					    }else{
							echo "<li><a href='#'>User</a>";
							echo "<ul>";
                            echo "<li><a href='signUp.html'>Sign Up</a></li>";
					        echo "<li><a href='login.php'>Log In</a></li>";
					    }
					?>
                    </li>
				    </ul>
				</li>
            </ul>

        </nav>
