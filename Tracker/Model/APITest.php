<?php
require_once('/var/www/html/Tracker/Model/TV.php');
require_once('/var/www/html/Tracker/Model/Essentials.php');
require_once('/var/www/html/Tracker/Model/Film.php');

class APITest extends PHPUnit_Framework_TestCase{

    public $essen;
    public $db; 

    function setUp() {
		$this->essen = new Essentials('testdatabase.db');
        $this->db = new SQLite3('testdatabase.db');
	}

	function tearDown(){
		unset($this->essen);
	}

    function testGet(){
        $this->setUp();
		$expected = "{\"status\":\"okay\",\"id\":1,\"name\":\"A\",\"date\":\"01\/01\/2015\",\"runtime\":\"120mins\",\"rating\":\"6.5\",\"starring\":\"Actor A,Actress A\",\"director\":\"Director A\",\"genre\":\"comedy\",\"synopsis\":\"Storyline A\",\"image\":\"imageA.png\",\"age\":\"PG\"}";
		$this->expectOutputString($expected);
		$this->essen->get($this->db,"films",1,1);
		$this->tearDown();
    }

    function testGet2(){
        $this->setUp();
        $expected = "No Film with that ID";
        $this->expectOutputString($expected);
        $this->essen->get($this->db,"films",5,1);
        $this->tearDown();
    }

    function testGet3(){
        $this->setUp();
		$expected = "{\"name\":\"ShowA\",\"id\":\"1\",\"rating\":\"8\",\"genre\":\"Crime\",\"image\":\"imageA.png\",\"show\":[{\"status\":\"okay\",\"tile\":\"Pilot\",\"season\":\"1\",\"episode\":\"1\",\"date\":\"01\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"A second coming\",\"season\":\"1\",\"episode\":\"2\",\"date\":\"15\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"Pilot\",\"season\":\"1\",\"episode\":\"1\",\"date\":\"01\/01\/1987\"},{\"status\":\"okay\",\"tile\":\"A second coming\",\"season\":\"1\",\"episode\":\"2\",\"date\":\"15\/01\/1987\"}], \"date\":\"01/01/1987\"}";
		$this->expectOutputString($expected);
		$this->essen->get($this->db,"tv_shows",1,1);
		$this->tearDown();
    }

    function testGetList(){
        $this->setUp();
        $expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"C\",\"date\":\"01\/02\/2015\",\"image\":\"imageC.png\",\"rating\":\"4\",\"id\":3},{\"status\":\"okay\",\"name\":\"B\",\"date\":\"02\/01\/2015\",\"image\":\"imageB.png\",\"rating\":\"10\",\"id\":2},{\"status\":\"okay\",\"name\":\"A\",\"date\":\"01\/01\/2015\",\"image\":\"imageA.png\",\"rating\":\"6.5\",\"id\":1}]}";
		$this->expectOutputString($expected);
		$this->essen->getList($this->db,"films",'1','0',"DESC");	
		$this->tearDown();
    }

    function testGetList1(){
        $this->setUp();
        $expected = "You're Page Value is too high or too low!!";
		$this->expectOutputString($expected);
		$this->essen->getList($this->db,"films",'1','1',"DESC");	
		$this->tearDown();
    }

    function testGetList2(){
		$this->setUp();
		$expected = "{\"tv_shows\":[{\"status\":\"okay\",\"name\":\"ShowA\",\"image\":\"imageA.png\",\"genre\":\"Crime\",\"rating\":\"8\",\"id\":1},{\"status\":\"okay\",\"name\":\"ShowB\",\"image\":\"imageB.png\",\"genre\":\"Crime\",\"rating\":\"10\",\"id\":2}]}";

		$this->expectOutputString($expected);
		$this->essen->getList($this->db,"tv_shows",'1','0',"ASC");	
		$this->tearDown();
	}

    function testSearch(){
        $this->setUp();
        $expected = "{\"films\":[{\"status\":\"okay\",\"name\":\"C\",\"rating\":\"4\",\"id\":3,\"image\":\"imageC.png\"}]}";
		$this->expectOutputString($expected);
		$this->essen->search($this->db,"films","C");	
		$this->tearDown();
    }

    function testSearch2(){
        $this->setUp();
        $expected = "{\"tv_shows\":[]}";
        $this->expectOutputString($expected);
        $this->essen->search($this->db,"tv_shows","FAIL");
        $this->tearDown();
    }

}
