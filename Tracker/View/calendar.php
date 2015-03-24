

<html>
<link rel="stylesheet" type="text/css" href="css/calendarStyle.css" />
	<title>Tracker - Calendar</title>
	<body>
	
		<h1 class="title">Calendar</h1>
		<?php
			set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
			require_once('Tracker/config.php');
			session_start();
			$date1 = date("Y-m-d");
			$media = $_GET["q"];
			if (isset($_SESSION['userID'])){
				$uid = $_SESSION['userID'];
				$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=calendar&date={$date1}&media={$media}&uid={$uid}");
			} else {
				$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=calendar&date={$date1}&media={$media}");
			}
			$data = json_decode($json, true);
			$tick = 0;
			foreach ($data as $section){
				if ($tick % 7 == 0){
					echo '<div class="calendar-week">';
				}
				echo '<div class="calendar-day">';
					$extraTitle = "";
					$extraContent = "";
					if ($section["date"] == $date1){
						$extraTitle = " calendar-title-current";
						$extraContent = " calendar-content-current";
					}
					echo "<div class=\"calendar-title-default{$extraTitle}\">{$section['pretty-date']}</div>";
					echo "<div class=\"calendar-content-default{$extraContent}\">";
						echo "<ul class=\"calendar-list\">";
						if ($media == "tv"){
							foreach ($section["episodes"] as $episode){
								echo "<li><a href=\"{$GLOBALS["ip"]}Tracker/View/getShow.php?id={$episode['show-id']}&season=1\"><div>S{$episode['season']}E{$episode['episode']} - {$episode['show']}</div></a></li>";
							}
							if ($section["episodes"] == null){
								echo "<li class=\"empty\"><a class=\"empty\"><div>Track More Shows?</div></a></li>";
							}
						} else if($media = "film"){
							foreach ($section["movies"] as $movie){
								echo "<li><a href=\"{$GLOBALS["ip"]}Tracker/View/getFilm.php?id={$movie['id']}\" style=\"text-align:center\"><div>{$movie['name']}</div></a></li>";
							}
							if ($section["movies"] == null){
								echo "<li class=\"empty\"><a class=\"empty\"><div>Track More Movies?</div></a></li>";
							}
						}
						echo '</ul>';
					echo '</div>';
				echo '</div>';
				if ($tick % 7 == 6){
					echo '</div>';
				}
				$tick += 1;
			}
		?>
		
		<!--
		<div class="navigation">
			To be added
		</div>
		!-->
	</body>
</html>
