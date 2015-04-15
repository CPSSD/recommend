<html>
  <head>
	<link rel="stylesheet" type="text/css" href="css/style.css" />

    <script onload="" type="text/javascript">
      var CLIENT_ID = '933303928886-3n6mtpsic6fbdvjet9hod9cpb1sbmin1.apps.googleusercontent.com';

      var SCOPES = ['https://www.googleapis.com/auth/calendar'];

      function checkAuth() {
        gapi.auth.authorize(
          {
            'client_id': CLIENT_ID,
            'scope': SCOPES,
            'immediate': true
          }, handleAuthResult);
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
          {client_id: CLIENT_ID, scope: SCOPES, immediate: false},
          handleAuthResult);
        return false;
      }

      function loadCalendarApi() {
        gapi.client.load('calendar', 'v3', processEpisodes);
      }
	  
	  function processEpisodes(){
		var tick = 0;
		var delay = 300;
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

			foreach ($data as $section){
				$show_date = $section['date'];
				foreach ($section['episodes'] as $episode) {
					$show_name = $episode['show'] . " S" . $episode['season'] . "E" . $episode['episode'];
					?> 	
					tick += 1;
					setTimeout(
						function(){
							addToCalendar("<?php echo $show_name ?>", "<?php echo $show_date ?>");
						}, delay * tick); 
					<?php
				}
			}?>
	  }

	  function addToCalendar(name, date){
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
				console.log("Error 503: Time Out.");
			}
			appendText(name + " @ " + date + "\n", "li");
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
    <div class="google-calendar-content">
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
