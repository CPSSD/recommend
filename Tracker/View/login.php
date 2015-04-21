<html> 
  <link rel="stylesheet" type="text/css" href="css/signUp.css" />
    <head> 
      <title>Login</title> 
    </head> 
	<body id="body-color"> 
		<div class="Sign-Up"> 
			<fieldset style="width:30%"><legend>Log In</legend> 
				<table border="0"> <tr> 
					<form method="POST" action="../Model/Login/login.php"> 							
						<td>Username</td><td> <input type="text" name="username"></td> </tr> 
						<tr> <td>Password</td><td> <input type="password" name="password"></td> </tr>
						<tr> <td><input id="button" type="submit" name="submit" value="login"></td> </tr> 
					</form> 						
				</table>
				<hr />
				<?php
					include_once('google_login.php');
					echo "<p style='text-align:center'>Sign in with Google.</p>";
					echo "<div class='g-signin2' style='margin-left:60' data-onsuccess='onSignIn'></div>";
				?>				
			</fieldset> 
		</div>
		<br />
		<br />
	</body>
</html>
