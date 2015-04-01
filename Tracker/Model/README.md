Model:

Unit Testing is done using PHPunit. This can be gotten and make excecutable by issueing commands;

➜ wget https://phar.phpunit.de/phpunit.phar

➜ chmod +x phpunit.phar

Run createTestDB.php to create the database for testing the Server.

Paths had to be hardcoded in test files as $_SERVER['DOCUMENT_ROOT']} wouldn't work. 


Film: phpunit FilmTest.php runs the film tests

TV: phpunit TVTest.php runs the TV tests


