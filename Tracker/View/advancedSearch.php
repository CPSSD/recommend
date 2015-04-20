<?php session_start();?>
<html>
<link rel="stylesheet" type="text/css" href="css/material.css" />
    <title>Advanced Search</title>
    <body>
        <?php include_once("navbar.php");?>
        <div class='show_container' style='height:500px;'>
            <h2>Search Films</h2>
                <form action='processSearch.php?' method='POST'>
                    <input type ='hidden' name='type' value='films'>
                    <input type='text' name='params[]' placeholder='Enter Director:'><br>
                    <input type='text' name='params[]' placeholder='Enter Actor/Actress:'><br>
                    <input type='text' name='params[]' placeholder='Enter Genre:'><br>
                    <input type ='number' size="200" placeholder='Rating'  name='rating' min='0' max='10'><br>
                    <input type ='submit' value='submit'>
                </form>
            <h2>Search Shows</h2>
                <form action='processSearch.php' method='POST'>
                    <input type='hidden' name='type' value='tv_shows'>
                    <input type='text' name='params[]' placeholder='Enter Genre'>
                    <p>Min Rating of <input type ='number' name='rating' min='0' max='10'></p>
                    <input type ='submit' value='submit'>
                </form>
        </div>
    </body>
</html>

