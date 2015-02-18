<?php

class DatabaseConnection {
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbName;

    function __construct() {
        $this->dbHost = "localhost";
        $this->dbUser = "root";
        $this->dbPass = "";
        $this->dbName = "movies";

        $connection = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass)
            or die("Could not connect to the database:<br />" . mysql_error());
        mysql_select_db($this->dbName) 
            or die("Database error:<br />" . mysql_error());
    }
}

?>
