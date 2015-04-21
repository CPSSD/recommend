from util import util
from util import sqlite_connector as db

old_db = db.Database()
new_db = db.Database()

new_layout = "id, name, image, location, rating, wiki_url, imdb_url, episode_url, genre, image_location"
new_schema = "id INTEGER PRIMARY KEY, name VARCHAR(255), image VARCHAR(255), location VARCHAR(255), rating VARCHAR(255), wiki_url VARCHAR(255), imdb_url VARCHAR(255), episode_url VARCHAR(255), genre VARCHAR(255), image_location VARCHAR(255)"
new_vartype = "%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s"

old_layout = "id, name, image, location, rating, wiki_url, imdb_url, episode_url"
old_schema = "id INTEGER PRIMARY KEY, name VARCHAR(255), image VARCHAR(255), location VARCHAR(255), rating VARCHAR(255), wiki_url VARCHAR(255), imdb_url VARCHAR(255), episode_url VARCHAR(255)"
old_vartype = "%s, %s, %s, %s, %s, %s, %s"

show_list = old_db.open_database_connection(False, old_layout, "database", "tv_shows", None)
try:
	old_db.close_database_connection()
except:
	pass

new_db.open_database_connection(True, new_layout, "database", "tv_shows", new_vartype)

tick = 0;
for show in show_list: 
	print tick, " out of ", len(show_list)
	tick += 1;
	show['genre'] = "Unknown"
	show['image_location'] = show['image']	   
	new_db.write_to_database(show, new_layout) 

new_db.close_database_connection()
		   