<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<meta name="google-signin-client_id" content="933303928886-3n6mtpsic6fbdvjet9hod9cpb1sbmin1.apps.googleusercontent.com">
<script>
	function onSignIn(googleuser) {
		var profile = googleuser.getBasicProfile();
		console.log('ID: ' + profile.getId());
		console.log('Name: ' + profile.getName());
		console.log('Image: ' + profile.getImageUrl());
		console.log('Email: ' + profile.getEmail());
		$.post("google_login_post.php", 
			{ 	
				id: profile.getId(), 
				name: profile.getName(), 
				image: profile.getImageUrl(), 
				email: profile.getEmail() 
			},
			function(data){
				alert(data);
				$("#div").append(data);
			});
		console.log("Posted Details to index.php");
	}
</script>
