<?php session_start();

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/config.php');
require_once('Tracker/Model/Essentials.php');
require_once('Tracker/Model/Film.php');
require_once('Tracker/Model/TV.php');

class Controller{

	public $db;
	public $essen;
    public $film;
    public $tv_show;

	public function __construct(){
		$this->essen = new Essentials('database.db');
		$this->db = new SQLite3('database.db');
        $this->film = new Film('database.db');
        $this->tv_show = new TV('database.db');        
	}

	public function invoke(){
		if($_GET["type"] == "films"){
			if(isset($_GET["page"]) && isset($_GET["organise"]) && intval($_GET["page"]) >= 0){
				$this->essen->getList($this->db,$_GET["type"],$_GET["organise"],$_GET["page"],$_GET["order"]); // gets a data of list of 24 films
			}else if (isset($_GET["id"])){
				$this->essen->get($this->db,$_GET['type'],$_GET["id"],""); // gets all data from 1 film
			}else if(isset($_GET['search'])){
				$this->essen->search($this->db,$_GET['type'],$_GET['search']); // returns up to 10 search results on a film search
			}else if (isset($_GET['userLikes'])){                        
					$this->essen->userLikes($this->db,$_GET['type'],$_GET['userLikes']);// gets all the films a logged in user has liked
			}else if(isset($_GET['recommendations'])){      
					$this->essen->recommendations($this->db,$_GET['type'],$_GET['recommendations']); // returns data on recommended films
			}
		}else if($_GET["type"] == "tv_shows"){
			if(isset($_GET["page"]) && isset($_GET["organise"]) && intval($_GET["page"]) >= 0){
				$this->essen->getList($this->db,$_GET['type'],$_GET["organise"],$_GET["page"],$_GET['order']); // gets a data of list of 24 tv shows
			}else if(isset($_GET['id']) && isset($_GET["season"])){
				$this->essen->get($this->db,$_GET['type'],$_GET['id'],$_GET["season"]); // gets all data from 1 show
			}else if(isset($_GET['search'])){
				$this->essen->search($this->db,$_GET['type'],$_GET['search']); // returns up to 10 search results on a show
			}else if (isset($_GET['userLikes'])){                        
					$this->essen->userLikes($this->db,$_GET['type'],$_GET['userLikes']);// gets all the films a logged in user has liked
			}else if(isset($_GET['recommendations'])){      
					$this->essen->recommendations($this->db,$_GET['type'],$_GET['recommendations']); // returns data on recommended films
			}
		} else if ($_GET["type"] == "calendar"){
			if(isset($_GET["date"]) && isset($_GET["media"])){
				$db = new SQLite3('database.db');
				$type = $_GET["media"];
				$range = "month";
				if(isset($_GET["range"])){
					$range = $_GET["range"];
				}
				if (isset($_GET['uid'])){
					$userID = $_GET['uid'];
					if($type == "film"){
						$retval = $db->query("SELECT * FROM 'track' WHERE userID = {$userID} AND mediaTable = \"films\"");
					} else {
						$retval = $db->query("SELECT * FROM 'track' WHERE userID = {$userID} AND mediaTable = \"tv_shows\"");
					}
					$tick = 0;
					while($row = $retval->fetchArray()){
						$tracking[$tick] = $row["mediaID"];
						$tick++;
					}
					if(!isset($tracking)){
						$tracking = [];
					}$media_type = $_GET["media"];
					if ($media_type == "tv"){
						$this->tv_show->getEpisodes($tracking, $_GET["date"], $range);
					} else {
						$this->film->getEpisodes($tracking, $_GET["date"], $range);
					}
				} else {
					$this->tv_show->getEpisodes(array(), $_GET["date"], $range);
				}
			}
		} else {
			$url = "{$GLOBALS['ip']}Tracker/View/getFilmList.php?type=films&organise=1&page=0";
			header( "Location: $url" );
		}
	}
}
