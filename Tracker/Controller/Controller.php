<?php

set_include_path("{$_SERVER['DOCUMENT_ROOT']}");
require_once('Tracker/Model/Film.php');  
require_once('Tracker/Model/TV.php');
require_once('Tracker/config.php');

//Class to control what call the server server makes to the database


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
				$this->film->getFilmList($_GET["organise"],$_GET["page"]);
			}else if (isset($_GET["id"])){
				$this->film->getFilm($_GET["id"]);
			}else if(isset($_GET['searchFilm'])){
				$this->film->searchFilm($_GET['searchFilm']);
			}else if(isset($_GET['page'])){
				$this->film->filmLikes($_GET['page']);
			}
		}else if($_GET["type"] == "tv_shows"){
			if(isset($_GET["page"]) && isset($_GET["organise"]) && intval($_GET["page"]) >= 0){
				$this->tv_show->getShowList($_GET["organise"],$_GET["page"]);
			}else if(isset($_GET['id']) && isset($_GET["season"])){
				$this->tv_show->getShow($_GET['id'],$_GET["season"]);
			}else if(isset($_GET['searchShow'])){
				$this->tv_show->searchShow($_GET['searchShow']);
			}else if(isset($_GET['page'])){
				$this->tv_show->showLikes($_GET['page']);
			}
		}else{
			$url = "{$GLOBALS['ip']}Tracker/View/getFilmList.php?type=films&organise=0&page=0";
			header( "Location: $url" );
		}
	}
}

?>
