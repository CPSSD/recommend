<?php
set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
include_once('Tracker/Model/Film.php');
include_once('Tracker/Model/Essentials.php');

class FilmTest extends PHPUnit_Framework_TestCase{
	
	public $film;

	function setUp() {
		$this->film = new Film('testdatabase.db');
	}

	function tearDown(){
		unset($this->film);
	}

	function testgetFilm(){
		$this->setUp();
		$db = new SQLite3('testdatabase.db');
		$expected = "{\"status\":\"okay\",\"id\":1,\"name\":\"A\",\"date\":\"01\/01\/2015\",\"runtime\":\"120mins\",\"rating\":\"6.5\",\"starring\":\"Actor A,Actress A\",\"director\":\"Director A\",\"genre\":\"comedy\",\"synopsis\":\"Storyline A\",\"image\":\"imageA.png\",\"age\":\"PG\"}";
		$this->expectOutputString($expected);
		$this->film->getFilm($db,1);
		$this->tearDown();
	}
	
	function testFilmList(){
		$this->setUp();
		$db = new SQLite3('testdatabase.db');
		$expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"A\",\"date\":\"01\/01\/2015\",\"image\":\"imageA.png\",\"rating\":\"6.5\",\"id\":1},{\"status\":\"okay\",\"name\":\"C\",\"date\":\"01\/02\/2015\",\"image\":\"imageC.png\",\"rating\":\"4\",\"id\":3},{\"status\":\"okay\",\"name\":\"B\",\"date\":\"02\/01\/2015\",\"image\":\"imageB.png\",\"rating\":\"10\",\"id\":2}]}";

		$this->expectOutputString($expected);
		$this->film->getFilmList($db,'2','0');	
		$this->tearDown();
	}

	function testFilmSearch(){
		$this->setUp();
		$db = new SQLite3('testdatabase.db');
		$expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"A\",\"rating\":\"6.5\",\"id\":1,\"image\":\"imageA.png\"}]}";
		$this->expectOutputString($expected);
		$this->film->searchFilm($db,"A");
		$this->tearDown();
	}
}

?>
