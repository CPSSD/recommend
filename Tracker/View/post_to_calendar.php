<html>
	<head>
	<link rel="stylesheet" type="text/css" href="css/material.css" />

		<script onload="" type="text/javascript">
			var CLIENT_ID = '933303928886-3n6mtpsic6fbdvjet9hod9cpb1sbmin1.apps.googleusercontent.com';

			var SCOPES = ['https://www.googleapis.com/auth/calendar'];

			function checkAuth() {
				gapi.auth.authorize(
					{
						'client_id': CLIENT_ID,
						'scope': SCOPES,
						'immediate': true
					}, 
					handleAuthResult);
			}

			function handleAuthResult(authResult) {
				var authorizeDiv = document.getElementById('authorize-div');
				if (authResult && !authResult.error) {
					// Hide auth UI, then load Calendar client library.
					authorizeDiv.style.display = 'none';
					loadCalendarApi();
				} else {
					// Show auth UI, allowing the user to initiate authorization by
					// clicking authorize button.
					authorizeDiv.style.display = 'inline';
				}
			}

			function handleAuthClick(event) {
				gapi.auth.authorize(
					{
						client_id: CLIENT_ID, 
						scope: SCOPES, 
						immediate: false
					}, 
					handleAuthResult);
				return false;
			}

			function loadCalendarApi() {
				gapi.client.load('calendar', 'v3', processEpisodes);
			}
		
			function inJSON(element, type, json){
				for (var i = 0; i < json.events.length; i++){
					if (json.events[i].name == element){
						return true;
					}
				}
				return false;
			}
				
			function processEpisodes(){				
				<?php
				set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
				require_once('Tracker/config.php');
				$date = date("Y-m-d");
				session_start();
				if(!isset($_SESSION['userID'])){
					appendText("You are not logged in...", "li");
				}
				$uid = $_SESSION['userID'];
				$json = file_get_contents("{$GLOBALS["ip"]}Tracker/index.php?type=calendar&date={$date}&media=tv&range=month&uid={$uid}");
				$data = json_decode($json, true);
				
				$episode_list = [];
				$tick = 0;
				foreach ($data as $section) {
					foreach ($section['episodes'] as $episode){
						if(!in_array($episode['show'], $episode_list)){
							$episode_list[$tick] = $episode['show'];
							$tick ++;
						}
					}
				}
				
				foreach ($episode_list as $episode){
				?>
					getEventList("<?php echo $episode ?>");
				<?php 
				} ?>
			}
			
			function writeToCalendar(show_name, json){
				<?php
				foreach ($data as $section){
					$show_date = $section['date'];
					foreach ($section['episodes'] as $episode) {
				?>
					var delay = 200;
					var tick = 0;
					if("<?php echo $episode['show'] ?>" == show_name){
						<?php $show_name = $episode['show'] . " S" . $episode['season'] . "E" . $episode['episode']; ?>
						tick += 1;
						setTimeout(
							function(){
								if (!inJSON("<?php echo $show_name ?>", "name", json)){
									console.log("Not in the calendar! - <?php echo $show_name ?>");
									addToCalendar("<?php echo $show_name ?>", "<?php echo $show_date ?>", 0);
								} else 	{
									console.log("Episode is already in calendar - <?php echo $show_name ?>");
									if (tick != 0){
										tick -= 1;
									}
								}
							}, delay * tick); 
						}
					<?php
					}
				}?>
			}
						
			function eventCheck(show_name, json){
				console.log("Show: " + show_name + "\n");
				console.log(json);
				writeToCalendar(show_name, json);
			}

			function getEventList(show_name){
				console.log("Gathering Event list.");
				var event = {
					'calendarId': 'primary',
					'q': show_name
				}
			
				var event_list = '{ "events" : [';
				var request = gapi.client.calendar.events.list(event);
				var resp = request.execute(function(resp) {
					console.log(resp);
					for (var i = 0; i < resp.items.length; i++){
						console.log(resp.items[i].summary);
						event_list += '{"name": "' + resp.items[i].summary + '", "id": "' + resp.items[i].id + '"}';
					//	console.log(event_list);
						if (i != resp.items.length - 1){
							event_list += ",";
						}
				
						if (false){
							var event = {
								'calendarId': 'primary',
								'eventId': resp.items[i].id
							}
							
							var request = gapi.client.calendar.events.delete(event);
							request.execute(function(resp) {
								console.log("	- Episode Deleted.");
							});
						}
					}
					event_list += ']}';
					
				//	console.log(event_list);
					eventCheck(show_name, JSON.parse(event_list));
				});
				
			}
			
			function addToCalendar(name, date, attempt){
				console.log("Inserting: " + name + " on " + date);
				var event = {
					'calendarId': 'primary',
					'summary': name,
					'start': {
						'date': date
					},
					'end': {
						'date': date
					}
				}
				var request = gapi.client.calendar.events.insert(event);
				request.execute(function(resp) {
					console.log(resp);
					if (resp.code == 503){
						console.log("Error 503: Time Out. Trying again...");
						if(attempt >= 5){
							appendText("Error saving '" + name + "'. Try again later.", "li");
						} else {
							addToCalendar(name, date, attempt+1);
						}
					} else {
						appendText(name + " @ " + date + "\n", "li");
					}
				});
			}		
			 
			function appendText(message, element_type) {
				var output = document.getElementById('output');
				var node = document.createElement(element_type);
				var textnode = document.createTextNode(message);
				node.appendChild(textnode);
				output.appendChild(node);
			}
	</script>
		<script src="https://apis.google.com/js/client.js?onload=checkAuth">
		</script>
	</head>
	<body>	
	<?php
		require_once('Tracker/View/navbar.php');
	?>
		<div class="show_container" style="width:400px;padding:5px 10px;text-align:center">
		<div class="google-calendar-button" id="authorize-div">
			<span>Authorize access to calendar</span>
			<!--Button for the user to click to initiate auth sequence -->
			<button id="authorize-button" onclick="handleAuthClick(event)">
			Authorize
			</button>
		</div>
		<h3>Adding Shows to Calendar...</h3>
		<p id="output"></p>
	</div>
	</body>
</html>
