<!-- Fonts -->
<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400italic,700italic,400,700" rel="stylesheet" type="text/css">
<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Core CSS -->
<link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">

<!-- Start Bootstrap Custom CSS -->
<link href="/assets/css/startbootstrap.css" rel="stylesheet" type="text/css">

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Webmaster Tools Site Verification -->
<meta name="google-site-verification" content="hq9-_keIZdTZt7arZ91T-gSqn8ANii362mlcn0zCgno">
</head>

<body>

<!-- Page Content -->
<div class="container">
	<?php
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
			require_once('Tracker/View/Util.php');
            $id = $_GET["id"];
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
           
		?>
			<?php include_once('Tracker/View/mobile/navbarMobile.php');?>
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

    </div>

    </div>
</div>

<!-- Footer -->
<div class="cta-mail">
        <!-- End MailChimp Signup Form -->
    </div>
</div>

<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6 footer-left">
                <p>
                    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FIronSummitMedia&amp;width=450&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;send=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
                </p>
            </div>
        </div>
        <hr>
    </div>
</footer>


<!-- JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="/assets/js/docs.js"></script>
<script src="/assets/js/startbootstrap.js"></script>

</body>

</html>