

<html>
<link rel="stylesheet" type="text/css" href="css/calendarStyle.css" />
	<title>Tracker - Calendar</title>
	<body>
	
		<h1 class="title">Calendar</h1>
		<?php
			$date1 = date("Y-m-d");
			$media = $_GET["q"];
			$json = file_get_contents("http://localhost:25565/index.php?type=calendar&current={$date1}&media={$media}"); 
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
								echo "<li><a href=\"http://localhost:25565/View/getShow.php?id={$episode['show-id']}&season=1\"><div>S{$episode['season']}E{$episode['episode']} - {$episode['show']}</div></a></li>";
							}
						} else if($media = "film"){
							foreach ($section["movies"] as $movie){
								echo "<li><a href=\"http://localhost:25565/View/getFilm.php?id={$movie['id']}\"><div>{$movie['name']}</div></a></li>";
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
