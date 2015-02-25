#!/usr/bin/env python
#-*- coding: latin-1 -*-

import requests
from bs4 import BeautifulSoup
from util import util

database_type = "sqlite"
database_layout = "name, location"
table_schema = "id INTEGER PRIMARY KEY, name VARCHAR, image VARCHAR, location VARCHAR"
episode_list_layout = "season, episode, title, date"
episode_list_schema = "id INTEGER PRIMARY KEY, season INTEGER, episode INTEGER, title VARCHAR, date VARCHAR"

# Chooses which database to use.
# Defaults to sqlite.
from util import sqlite_connector as db
if database_type.lower() == 'mysql':
    from util import mysql_connector as db

def scrape_wikipedia(url):
    print("Grabbing Data from '%s'" % url)
    #url = '/wiki/List_of_Banshee_episodes'
    html = requests.get("http://en.wikipedia.org%s" % url).text
    bs4 = BeautifulSoup(html)

    name = bs4.find('h1', {'class': 'firstHeading'}).i.text
    name = util.clean_text(name)
    print(name)

    episode_tables = bs4.find_all('table', {'class': 'wikitable plainrowheaders'})
    season_count = 0
    data = None
    episode_list_data = [{'name': name}]
    for episode_table in episode_tables:
        season_count += 1
        episode_count = 0
        episode_list = episode_table.find_all('tr', {'class': 'vevent'})
        for episode in episode_list:
            if season_count is 2 and data is None:
                season_count = 1;
            episode_count += 1
            title = episode.find('td', {'class', 'summary'}).text
            title = title.replace("\"", "")
            title = util.clean_text(title)

            release_date = episode.find('span', {'class': 'bday dtstart published updated'})
            if release_date is not None:
                release_date = util.clean_text(release_date.text)
            else:
                release_date = "-   NA   -"

            if len(episode_list_data) > 2:
                if season_count >= 5 and name == "Breaking Bad":
                    print(episode_list_data[len(episode_list_data)-1])
                if not util.compare_dates(release_date, episode_list_data[len(episode_list_data)-1]['date']):
                    print("NOT AN EPISODE | %d \t| %d \t| %s \t | %s" % (season_count, episode_count, release_date, title))
                    break # Prevents all of the next episodes from being added to database as they are web-series/mini-series and not the actual show.
                else:
                    util.debug_print("| %d \t| %d \t| %s \t | %s" % (season_count, episode_count, release_date, title))

            data = {
                'season': season_count,
                'episode': episode_count,
                'title': title,
                'date': release_date
            }
            episode_list_data.append(data)

    return episode_list_data

def save_to_database(data, layout):
    global database_layout
    db.write_to_database(data, layout)
    return True # To verify that there were no errors.

if __name__ == "__main__":
    print("Starting Film Crawler...")
    show_list = ['/wiki/List_of_Arrow_episodes', '/wiki/List_of_Banshee_episodes', '/wiki/List_of_The_Walking_Dead_episodes', '/wiki/List_of_Breaking_Bad_episodes', '/wiki/The_Flash_(2014_TV_series)']
    db.open_database_connection(table_schema, "tv_shows", "tv_shows")
    db.write_to_database({"name": "Arrow",              "location": "Arrow"             }, database_layout)
    db.write_to_database({"name": "Banshee",            "location": "Banshee"           }, database_layout)
    db.write_to_database({"name": "The Walking Dead",   "location": "The_Walking_Dead"  }, database_layout)
    db.write_to_database({"name": "Breaking Bad",       "location": "Breaking_Bad"      }, database_layout)
    db.write_to_database({"name": "The Flash",          "location": "The_Flash"         }, database_layout)
    db.close_database_connection()
    print("Written to tv database")

    for show in show_list:
        episode_list = scrape_wikipedia(show)
        db.open_database_connection(episode_list_schema, "tv_shows", episode_list[0]['name'].replace(' ', '_'))
        episode_list.remove(episode_list[0])
        #print episode_list
        for episode in episode_list:
            print("Starting episode: | %d \t| %d \t| %s \t | %s" % (episode['season'], episode['episode'], episode['date'], episode['title']))
            db.write_to_database(episode, episode_list_layout)
        db.close_database_connection()
    print("Written to episode databases");

    print("Exiting Film Crawler...")