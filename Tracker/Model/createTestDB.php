<?php

$db = new SQLite3('testdatabase.db');

// Films table set up

$db->exec('CREATE TABLE IF NOT EXISTS films(id INTEGER PRIMARY KEY, name TEXT,
								    date TEXT, 
								    runtime TEXT,
								    rating TEXT,
								    starring TEXT,
								    director TEXT,
								    genre TEXT,
								    synopsis TEXT,
								    image TEXT,
								    age TEXT)');

$sql = "INSERT INTO films(name,date,runtime,rating,starring,director,genre,synopsis,image,age) VALUES('A','01/01/2015','120mins','6.5','Actor A,Actress A','Director A','comedy','Storyline A','imageA.png','PG')";
$result = $db->query($sql);

$sql = "INSERT INTO films(name,date,runtime,rating,starring,director,genre,synopsis,image,age) VALUES('B','02/01/2015','130mins','10','Actor B','Director B','War','Storyline B','imageB.png','18')";
$result = $db->query($sql);

$sql = "INSERT INTO films(name,date,runtime,rating,starring,director,genre,synopsis,image,age) VALUES('C','01/02/2015','90mins','4','Actor C,Actor D,Actress C','Director C','Drama+Comedy','Storyline C','imageC.png','PG')";
$result = $db->query($sql);

// tv_shows table set up

$db->exec('CREATE TABLE IF NOT EXISTS tv_shows(id INTEGER PRIMARY KEY, name TEXT, image TEXT, location TEXT, rating TEXT)');

$sql = "INSERT INTO tv_shows(name,image,location,rating) VALUES('ShowA','imageA.png','_a','8')";
$result = $db->query($sql);

$sql = "INSERT INTO tv_shows(name,image,location,rating) VALUES('ShowB','imageB.png','_b','10')";
$result = $db->query($sql);

// individual shows film set up
$db->exec("CREATE TABLE IF NOT EXISTS _a(id INTEGER PRIMARY KEY, season TEXT, episode TEXT, title TEXT, date TEXT)");

$sql = "INSERT INTO _a(season,episode,title,date) VALUES('1','1','Pilot','01/01/1987')";
$result = $db->query($sql);

$sql = "INSERT INTO _a(season,episode,title,date) VALUES('1','2','A second coming','15/01/1987')";
$result = $db->query($sql);

$sql = "INSERT INTO _a(season,episode,title,date) VALUES('2','1','Season 2','15/01/1987')";
$result = $db->query($sql);

$db->exec("CREATE TABLE IF NOT EXISTS _b(id INTEGER PRIMARY KEY, season TEXT, episode TEXT, title TEXT, date TEXT)");
$sql = "INSERT INTO _b(season,episode,title,date) VALUES('1','1','First','01/01/2000')";
$result = $db->query($sql);
?>
