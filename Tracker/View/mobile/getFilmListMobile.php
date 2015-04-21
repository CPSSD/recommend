<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Start Recommend">

<title>
  
    Start Recommended
  
</title>

<!-- Google+ Authorship Information -->
<link rel="author" href="https://plus.google.com/+Startbootstrap">
<link rel="publisher" href="https://plus.google.com/+Startbootstrap">

<!-- Canonical -->
<link rel="canonical" href="http://startbootstrap.com/">

<!-- Fav Icon and Apple Touch Icons -->
<link rel="icon" href="/Tracker/View/assets/img/ico/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="/Tracker/View/assets/img/ico/touch-icon-iphone.png">
<link rel="apple-touch-icon" sizes="76x76" href="/Tracker/View/assets/img/ico/touch-icon-ipad.png">
<link rel="apple-touch-icon" sizes="120x120" href="/Tracker/View/assets/img/ico/touch-icon-iphone-retina.png">
<link rel="apple-touch-icon" sizes="152x152" href="/Tracker/View/assets/img/ico/touch-icon-ipad-retina.png">

<!-- Open Graph -->
<meta property="og:title" content="Bootstrap">
<meta property="og:site_name" content="Bootstrap">
<meta property="og:type" content="website">
<meta property="og:description" content="Bootstrap!">
<meta property="og:image" content="http://startbootstrap.com/assets/img/og/startbootstrap-logo.jpg">
<meta property="og:url" content="http://startbootstrap.com/">

<!-- Fonts -->
<link href="http://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400italic,700italic,400,700" rel="stylesheet" type="text/css">
<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<!-- Bootstrap Core CSS -->
<link href="/Tracker/View/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">

<!-- Start Bootstrap Custom CSS -->
<link href="/Tracker/View/assets/css/startbootstrap.css" rel="stylesheet" type="text/css">

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Webmaster Tools Site Verification -->
<meta name="google-site-verification" content="hq9-_keIZdTZt7arZ91T-gSqn8ANii362mlcn0zCgno">
</head>

<body>
<!-- Navigation -->



<header class="marquee">
    <div class="container">
        <span class="brand-name">Start Recommended</span>
        <h1>Find the recommendation of your favorites movies</h1>
        
        </div>
    </div>
</header>

<!-- Page Content -->
<div class="container">
		<?php
            set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
	        require_once('Tracker/View/Util.php'); 
	        require_once('Tracker/config.php');
	        $organise = $_GET["organise"];
	        $page = $_GET["page"];
            $order = $_GET["order"];
			
		    $json = file_get_contents("{$GLOBALS['ip']}Tracker/index.php?type=films&organise={$organise}&page={$page}&order={$order}");
		    $obj = json_decode($json, true);
            if(!$obj){
                $_SESSION["message"] = "Your Page Value is too high or too low!!";
			    $url = "{$GLOBALS["ip"]}Tracker/View/displayMessage.php";
			    header( "Location: $url" );
            }
		    $type = 'films';
		    $column = 0;
		    $row = 0;
		    $per_row = 4;
		    $util = new Util();
		?>
     <?php include_once('Tracker/View/mobile/navbarMobile.php');?>   
		<div class="row home-intro text-center">
			<div class="col-lg-12">
				<h2 class="tagline"></h2>
			   
			</div>
		</div>
	
    <div class="row previews">
			
			<?php echo "<div class='show_container'>";
			# Displays info for each movie.
			foreach($obj['films'] as $movie){
			?>
			<div class="col-lg-4 col-sm-6">
				<div class="thumbnail">
					<a href="<?=$GLOBALS["ip"]?>Tracker/View/mobile/getFilmMobile.php?type=films&id=<?=$movie["id"]?>" class="post-image-link">
						<p><img src="<?= $movie["image"] ?>" class="img-responsive" alt="<?= $movie["name"] ?>" /></p>
					</a>
					<div class="caption">
						<h3><?= $movie["name"] ?></h3>
						<p><?= $movie["date"] ?></p>
						<p><?= $movie["rating"] ?></p>
					</div>
				</div>
			</div>
			<?php } ?>
    </div>

    </div>
</div>
<div class="navigation">
		<?php
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/mobile/getFilmListMobile.php?organise={$organise}&page=" . $util->checkNextPage('films',$page-1,$organise) . "'>Previous Page</a> |";
			echo "<a href='{$GLOBALS["ip"]}Tracker/View/mobile/.php?organise={$organise}&page=" . $util->checkNextPage('films',$page+1,$organise) . "'> Next Page.</a>";
		?>
		</div>

<!-- Footer -->
<div class="cta-mail">
        <!-- End MailChimp Signup Form -->
    </div>
</div>

<footer>
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