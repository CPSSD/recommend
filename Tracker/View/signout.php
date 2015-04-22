<html> 
  <link rel="stylesheet" type="text/css" href="css/signUp.css" />
  <link rel="stylesheet" type="text/css" href="css/material.css" />
    <head> 
      <title>Login</title> 
    </head> 
	<body id="body-color"> 
	
      <div class="signUp">
           <h1>Sign-Out</h1><br>
          <hr/>
          <?php		
			include_once('google_login.php');			
			echo "<div class='g-signin2' style='visibility:hidden;position:absolute;'></div>";
			echo "<div class='sign_out'>";			
            echo "<p style='margin-left:15px'>Do you want to logout?</p>";
			echo "<a href='logout.php' onclick='signOut()'><p class='show_info double_info button' style='margin:7.5px auto'>Yes</p></a>";
			echo "<a href='/'><p class='show_info double_info button' style='margin:0 auto'>No</p></a>";
			echo "</div>";
          ?>
    </div>
	</body>
</html>
