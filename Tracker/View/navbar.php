<?php
$query = $_SERVER['QUERY_STRING'];
$query = substr($query,5,5);
if($query == "films"){
    $x = "Film";
    $type = "films";
}else{ 
    $x = "Show";
    $type = "tv_shows";
}
?>        

<link rel="stylesheet" type="text/css" href="css/navStyle.css" />
        <nav>
            <ul class='navbar'>
                <li><a href="#">Media</a>
                    <ul> 
                        <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getShowList.php?type=tv_shows&organise=3&page=0&order=DESC'>TV Shows</a></li>";
                        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?type=films&organise=1&page=0&order=ASC'>Films</a></li>";?>
				    </ul>
				</li>
			        <li><a href="#">Sort By</a>
				    <ul>
				        <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=1&page=0&order=ASC'>By Name(A-Z)</a></li>";
                            echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=1&page=0&order=DESC'>By Name(Z-A)</a></li>";
					        if ($type == "Film")
                            {
                                echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=2&page=0&order=ASC'>By Release Date(Old-New)</a></li>";
					            echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=2&page=0&order=DESC'>By Release Date(New-Old)</a></li>";
                            }
					        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=3&page=0&order=ASC'>By Rating(Low-High)</a></li>";
					        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/get{$x}List.php?organise=3&page=0&order=DESC'>By Rating(High-Low)</a></li>";?>
				    </ul>
				</li>
				</li>
				<li><a href="">Entertainment Tracker</a></li>
			    <li><a href="">Search</a>
				    <ul>
					    <?php echo "<li><form action='searchResults.php?type=films' method='POST'>";
					            echo "<input type='text' name='search' placeholder='Film QuickSearch'>";
                                echo "<input type='hidden' name='type' value='films'>";
					            echo "<input type='submit'>";
					        echo "</form></li>";
                            echo "<li><form action='searchResults.php?type=tv_shows' method='POST'>";
					            echo "<input type='text' name='search' placeholder='TV Show QuickSearch'>";
                                echo "<input type='hidden' name='type' value='tv_shows'>";
					            echo "<input type='submit'>";
					        echo "</form></li>";
					    echo "<li><a href=''>Advanced Search Options</a></li>";?>
				    </ul>
				</li>
					<?php 
					    if (isset($_SESSION['userID'])){
							echo "<li><a href='#'>{$_SESSION['username']}</a>";
							echo "<ul>";
					        echo "<li><a href='logout.php'>Logout</a></li>";
                            echo "<li><a href='recommendations.php?type={$type}&recommendations={$_SESSION['userID']}'>Recommendations</a></li>";
                            echo "<li><a href='calendar.php?q=films'>Personal Calendar</a></li>";
                            echo "<li><a href='viewLikes.php?type=films&userID={$_SESSION['userID']}'>View Likes</a></li>";
					    }else{
							echo "<li><a href='#'>User</a>";
							echo "<ul>";
                            echo "<li><a href='signUp.html'>Sign Up</a></li>";
					        echo "<li><a href='login.html'>Log In</a></li>";
					    }
					?>
                    </li>
				    </ul>
				</li>
            </ul>

        </nav>
