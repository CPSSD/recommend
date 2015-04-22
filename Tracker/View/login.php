<html> 
  <link rel="stylesheet" type="text/css" href="css/signUp.css" />
    <head> 
      <title>Login</title> 
    </head> 
	<body id="body-color"> 

      <div class="signUp">
            <h1>Sign-Up</h1><br>
          <form method="POST" action="../Model/Login/login.php">
            <input type="text" name="username" placeholder="Userame">
            <input type="password" name="password" placeholder="Password">
            <input type="submit" name="submit" class="login login-submit" value="Log in">
          </form>
          <div class="login-help">
            <a href="signUp.html">SignUp</a>
          </div>
          <hr/>
          <?php
            include_once('google_login.php');
            echo "<p style='text-align:center'>Sign in with Google.</p>"; 
            echo "<div class='g-signin2' style='margin-left:75px;' data-onsuccess='onSignIn'</div>";
          ?>
    </div>
	</body>
</html>
