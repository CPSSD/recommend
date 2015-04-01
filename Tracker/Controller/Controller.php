<?php session_start();

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/Model/Film.php');  
require_once('Tracker/Model/TV.php');
require_once('Tracker/config.php');

//Class to control what call the server makes to the database


class Controller{
	public $film;
	public $tv_show;

	public function __construct(){
		$this->film = new Film('database.db');
		$this->tv_show = new TV('database.db');
	}
	
	public function invoke(){
		if($_GET["type"] == "films"){
			if(isset($_GET["page"]) && isset($_GET["organise"]) && intval($_GET["page"]) >= 0){
				$this->film->getFilmList($_GET["organise"],$_GET["page"]); // gets a data of list of 24 films
			}else if (isset($_GET["id"])){
				$this->film->getFilm($_GET["id"]); // gets all data from 1 film
			}else if(isset($_GET['searchFilm'])){
				$this->film->searchFilm($_GET['searchFilm']); // returns up to 10 search results on a film search
			}else if(isset($_GET['page'])){						         
				$this->film->filmLikes($_GET['page']); // returns list of films for user to like upon logging in
			}else if (isset($_GET['filmLikesToRecommend'])){                        
					$this->film->filmLikesToRecommend($_GET['filmLikesToRecommend']);// gets all the films a logged in user has liked
			}else if(isset($_GET['filmRecommendations'])){      
					$this->film->filmRecommendations($_GET['filmRecommendations']); // returns data on recommended films
			}
		}else if($_GET["type"] == "tv_shows"){
			if(isset($_GET["page"]) && isset($_GET["organise"]) && intval($_GET["page"]) >= 0){
				$this->tv_show->getShowList($_GET["organise"],$_GET["page"]); // gets a data of list of 24 tv shows
			}else if(isset($_GET['id']) && isset($_GET["season"])){
				$this->tv_show->getShow($_GET['id'],$_GET["season"]); // gets all data from 1 show
			}else if(isset($_GET['searchShow'])){
				$this->tv_show->searchShow($_GET['searchShow']); // returns up to 10 search results on a show
			}else if(isset($_GET['page'])){
				$this->tv_show->showLikes($_GET['page']); // returns list of films for user to like upon logging in
			}
		} else if ($_GET["type"] == "calendar"){
			if(isset($_GET["date"]) && isset($_GET["media"])){
				$db = new SQLite3('database.db');
				$type = $_GET["media"];
				if (isset($_GET['range'])){
					$range = $_GET['range'];
				} else {
					$range = "month";
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
			$url = "{$GLOBALS['ip']}Tracker/View/getFilmList.php?type=films&organise=0&page=0";
			header( "Location: $url" );
		}
	}
}

?>
