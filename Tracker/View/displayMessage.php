<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/message.css" />
	<title>Tracker - Message</title>
	<body>
		<div style='margin-left:175px;'>
	<?php
		set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
		require_once('Tracker/config.php');
		
		echo "<p>Go To List: <select onChange='window.location.href=this.value;'>";
 			echo "<option value=''>--</option>";
			echo "<option value='{$GLOBALS["ip"]}Tracker/View/getShowList.php?organise=1&page=0'>TV Shows</option>";
 			echo "<option value='{$GLOBALS["ip"]}Tracker/View/getFilmList.php?organise=1&page=0'>Films</option>";
		echo "</select>";
	?>
		</div>
	<?php
		echo "<div class='show_container'>";
			$msg = $_SESSION['message'];
			echo $msg;
		echo "</div>";
	?>
	</body>
</html>
