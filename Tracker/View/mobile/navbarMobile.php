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

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header" >
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Start Recommended</a>
        </div>
		<!-- Collect the nav links, forms, and other content for toggling -->     
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
             <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Media">Media <b class="caret"></b></a>
                    <ul class="dropdown-menu">
						  <ul> 
                        <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/getShowList.php?type=tv_shows&organise=3&page=0&order=DESC'>TV Shows</a></li>";
                        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/getFilmListMobile.php?type=films&organise=1&page=0&order=ASC'>Films</a></li>";?>
				    </ul>
						</ul>
                </li>
                <li>
                    <a href="" title="Entretainment tracket">Entretainment tracker</a>
                </li>
				<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Search">Search <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                       </li>
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
                    </ul>
                </li>
				<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Sort">Sort by <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                       </li>
					  <ul>
						 <?php echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=1&page=0&order=ASC'>By Name(A-Z)</a></li>";
                            echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=1&page=0&order=DESC'>By Name(Z-A)</a></li>";
					        if ($type == "Film")
                            {
                                echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=2&page=0&order=ASC'>By Release Date(Old-New)</a></li>";
					            echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=2&page=0&order=DESC'>By Release Date(New-Old)</a></li>";
                            }
					        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=3&page=0&order=ASC'>By Rating(Low-High)</a></li>";
					        echo "<li><a href='{$GLOBALS["ip"]}Tracker/View/mobile/get{$x}ListMobile.php?organise=3&page=0&order=DESC'>By Rating(High-Low)</a></li>";?>
				    </ul>
                    </li>
                    </ul>
                </li>
				<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="User">User <b class="caret"></b></a>
                    <ul class="dropdown-menu">
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
                            echo "<li><a href='signUpMobile.html'>Sign Up</a></li>";
					        echo "<li><a href='loginMobile.php'>Log In</a></li>";
					    }
					?>
                    </li>
                    </ul>
                </li>
              
              
            </ul>
  
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>