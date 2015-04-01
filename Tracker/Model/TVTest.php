<?php
require_once('/var/www/html/Tracker/Model/TV.php');
require_once('/var/www/html/Tracker/Model/Essentials.php');
require_once('/var/www/html/Tracker/Model/Film.php');

class TVTest extends PHPUnit_Framework_TestCase{
	
	public $tv_show;
    public $db;

	function setUp() {
		$this->tv_show = new TV('testdatabase.db');
        $this->db = new SQLite3('testdatabase.db');
	}

	function tearDown(){
		unset($this->tv_show);
	}

	function testsearchShowExists(){
		$this->setUp();
		$expected = "{\"name\":\"ShowA\",\"id\":\"1\",\"rating\":\"8\",\"genre\":\"Crime\",\"image\":\"imageA.png\",\"show\":[{\"status\":\"okay\",\"tile\":\"Pilot\",\"season\":\"1\",\"episode\":\"1\",\"date\":\"01\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"A second coming\",\"season\":\"1\",\"episode\":\"2\",\"date\":\"15\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"Pilot\",\"season\":\"1\",\"episode\":\"1\",\"date\":\"01\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"A second coming\",\"season\":\"1\",\"episode\":\"2\",\"date\":\"15\/01\/1987\"}], \"date\":\"01/01/1987\"}";
		$this->expectOutputString($expected);
		$this->tv_show->getShow($this->db,1,1);
		$this->tearDown();
	}
	
	function testShowList(){
		$this->setUp();
		$db = new SQLite3('testdatabase.db');
		$expected = "{\"tv_shows\":[{\"status\":\"okay\",\"name\":\"ShowA\",\"image\":\"imageA.png\",\"genre\":\"Crime\",\"rating\":\"8\",\"id\":1},{\"status\":\"okay\",\"name\":\"ShowB\",\"image\":\"imageB.png\",\"genre\":\"Crime\",\"rating\":\"10\",\"id\":2}]}";

		$this->expectOutputString($expected);
		$this->tv_show->getShowList($this->db,"tv_shows",'1','0',"ASC");	
		$this->tearDown();
	}

	function testShowSearch(){
		$this->setUp();
		$db = new SQLite3('testdatabase.db');
		$expected = "{\"tv_shows\":[]}";
		$this->expectOutputString($expected);
		$this->tv_show->searchShows($this->db,"tv_shows","C");
		$this->tearDown();
	}
}

?>
