<?php

require_once('/var/www/html/Tracker/Model/Film.php');
require_once('/var/www/html/Tracker/Model/Essentials.php');
require_once('/var/www/html/Tracker/Model/TV.php');

class FilmTest extends PHPUnit_Framework_TestCase{
	
	public $film;
    public $db;

	function setUp() {
		$this->film = new Film('testdatabase.db');
        $this->db = new SQLite3('testdatabase.db');
	}

	function tearDown(){
		unset($this->film);
	}

	function testgetFilm(){
		$this->setUp();
		$expected = "{\"status\":\"okay\",\"id\":1,\"name\":\"A\",\"date\":\"01\/01\/2015\",\"runtime\":\"120mins\",\"rating\":\"6.5\",\"starring\":\"Actor A,Actress A\",\"director\":\"Director A\",\"genre\":\"comedy\",\"synopsis\":\"Storyline A\",\"image\":\"imageA.png\",\"age\":\"PG\"}";
		$this->expectOutputString($expected);
		$this->film->getFilm($this->db,"films",1);
		$this->tearDown();
	}
	
	function testFilmList(){
		$this->setUp();
		$expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"A\",\"date\":\"01\/01\/2015\",\"rating\":\"6.5\",\"image\":\"imageA.png\",\"id\":1},{\"status\":\"okay\",\"name\":\"C\",\"date\":\"01\/02\/2015\",\"rating\":\"4\",\"image\":\"imageC.png\",\"id\":3},{\"status\":\"okay\",\"name\":\"B\",\"date\":\"02\/01\/2015\",\"rating\":\"10\",\"image\":\"imageB.png\",\"id\":2}]}";
		$this->expectOutputString($expected);
		$this->film->getFilmList($this->db,"films",'2','0',"ASC");	
		$this->tearDown();
	}

	function testFilmSearch(){
		$this->setUp();
		$expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"A\",\"rating\":\"6.5\",\"id\":1,\"image\":\"imageA.png\"}]}";
		$this->expectOutputString($expected);
		$this->film->searchFilms($this->db,"films","A");
		$this->tearDown();
	}
}

?>
