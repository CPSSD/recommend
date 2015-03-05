#!/usr/bin/env python
#-*- coding: latin-1 -*-

import requests
from bs4 import BeautifulSoup
from util import file_handler as file
from util import util

database_type = "sqlite"
tv_show_layout = "name, image, location, rating, wiki_url, imdb_url, episode_url"
tv_show_schema = "id INTEGER PRIMARY KEY, name VARCHAR, image VARCHAR, location VARCHAR, rating VARCHAR, wiki_url VARCHAR, imdb_url VARCHAR, episode_url VARCHAR"
tv_show_vartype = "%s, %s, %s, %s, %s, %s, %s"
episode_list_layout = "season, episode, title, date"
episode_list_schema = "id INTEGER PRIMARY KEY, season INTEGER, episode INTEGER, title VARCHAR, date VARCHAR"
episode_list_vartype = "%d, %d, %s, %s"

# Loads the config file.
config = file.get_config_data("crawler.config")

# Chooses which database to use.
# Defaults to sqlite.
from util import sqlite_connector as db
if config['database_type'].lower() == 'mysql':
   from util import mysql_connector as db

def scrape_imdb(url):
    data = {}
    data['image'] = ""
    data['rating'] = "0.0"
    if url == "":
        return data
    html = requests.get(url).text
    bs4 = BeautifulSoup(html)

    # Grabs the image from the left hand side.
    image = bs4.find('td', {'id': 'img_primary'})
    image_url = ""
    if image is not None:
        image = image.find('img')
        if image is not None:
            image_url = image['src']
    data['image'] = image_url

    # Grabs the rating.
    rating = bs4.find('span', {'itemprop': 'ratingValue'})
    rating_value = "0.0"
    if rating is not None:
        rating_value = rating.text
    data['rating'] = rating_value

    # Grabs the Synopsis.
    # TO BE COMPLETED
    return data

def scrape_wikipedia(url):
    print("Grabbing Data from '%s'" % url)
    #url = '/wiki/List_of_Banshee_episodes'
    html = requests.get("http://en.wikipedia.org%s" % url).text
    bs4 = BeautifulSoup(html)

    name = bs4.find('h1', {'class': 'firstHeading'})
    if name.i is None:
        name = name.text.split("List of")[1].split("episodes")[0]
        name = util.clean_text(name)
    else:
        name = util.clean_text(name.i.text)
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
                banned_dates = {"Unaired"}
                release_date = util.clean_text(release_date.text)
                for date in banned_dates:
                    if release_date == date:
                        release_date = "-   NA   -"
            else:
                release_date = "-   NA   -"

            if len(episode_list_data) > 2:
                print episode_list_data
                print release_date
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
    global tv_show_layout
    db.write_to_database(data, layout)
    return True # To verify that there were no errors.

def crawl_wikipedia(base_url, url, link_list):
    skip_first = True
    if url == "":
        skip_first = False
        url = base_url
    html = requests.get(url).text
    bs4 = BeautifulSoup(html)

    section = bs4.find('div', {'id': 'mw-pages'})
    if section is None:
        return link_list
    links = section.find_all('li')
    if links is None:
        return link_list
    if skip_first:
        links.remove(links[0])
    if len(links) == 0:
        print("All Links Saved...")
        return link_list
    for link in links:
        show_name = util.clean_text(link.a.text)
        link_list[show_name] = (link.a['href'])
        print link.a['href']

    last_show_name = links[len(list(links))-1].text
    last_show_name = last_show_name.replace(" ", "+")
    next_url = ("%s&pagefrom=%s" % (base_url, last_show_name))
    if next_url == url:
        return link_list
    print next_url
    link_list = crawl_wikipedia(base_url, next_url, link_list)

    return link_list

def grab_show_data(url):
    print "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\turl: \t\t\t %s" % url
    html = requests.get("http://en.wikipedia.org%s" % url).text
    bs4 = BeautifulSoup(html)

    show_data = {}
    show_data['wiki_url'] = url
    show_data['keep'] = False
    name = bs4.find('h1', {'class': 'firstHeading'}).i
    if name is None:
        return show_data
    show_data['name'] = util.clean_text(name.text)

    infobox = bs4.find('table', {'class': 'infobox vevent'})
    if infobox is None:
        return show_data
    links = infobox.find_all('a')
    episode_link = None
    for link in links:
        if link.text == "List of episodes":
            episode_link = link
            break
    if episode_link is None:
        show_data['episode_url'] = url
    else:
        show_data['episode_url'] = episode_link['href']
        if episode_link['href'].startswith('#'):
            show_data['episode_url'] = url

    ext_links = bs4.find('span', {'id': 'External_links'})
    show_image = ""
    imdb_data = None
    imdb_link = ""
    if ext_links is not None:
        ext_links = ext_links.parent.find_next_sibling('ul')
        if ext_links is not None:
            imdb_link = ext_links.find('a', {'href': '/wiki/Internet_Movie_Database'})
            imdb = ""
            if imdb_link is not None:
                imdb_link = imdb_link.parent.find('a', {'rel': 'nofollow'})
                imdb = imdb_link['href']
            show_data['imdb_url'] = imdb
            imdb_data = scrape_imdb(imdb)

    if imdb_link == "":
        show_data['keep'] = False
        return show_data
    show_data['image'] = ""
    show_data['rating'] = "0.0"
    if imdb_data is not None:
        show_data['image'] = imdb_data['image']
        show_data['rating'] = imdb_data['rating']
    show_data['keep'] = True
    return show_data

def get_show_list(from_database):
    if from_database:
        print("Retrieving Show list from database.");
        show_list = db.open_database_connection(False, "name, episode_url, wiki_url, imdb_url", "tv_shows", "tv_shows", None)
        db.connection.close()
        return show_list
    else:
        show_list = {}
        for year in range(config['start_year'], config['end_year']):
            show_list = (crawl_wikipedia("http://en.wikipedia.org/w/index.php?title=Category:%s_American_television_series_debuts" % year, "", show_list))
        tick = 1
        return show_list

def update_show_data():
    link_list = get_show_list(False)

    db.open_database_connection(True, tv_show_schema, "tv_shows", "tv_shows", tv_show_vartype)
    show_link_data = {}
    file.open_file('failures.txt', 'w')
    print len(list(link_list))
    tick = 0
    show_tick = 0
    show_limit = 9999999
    for link in link_list:
        show_data = grab_show_data(link_list[link])
        if show_data['keep'] is True:
            show_link_data[show_data['name']] = show_data['episode_url']
            print "%d: \t %s \t\t %s" % (tick, show_data['name'], link_list[link])
            show_data['location'] = util.create_table_name(show_data['name'])
            db.write_to_database(show_data, tv_show_layout)
            tick += 1
            show_tick += 1
            if show_tick > show_limit:
                break
        else:
            file.output("%s\t| %s" % (link_list[link], link))
            print("Invalid Show -> %s" % link)
    db.close_database_connection()
    file.close_file()

def update_show_episodes(index):
    show_link_data = get_show_list(True)
    print ("Getting Episode Data for all shows..")

    tick = 1
    print len(list(show_link_data))
    for show in show_link_data:
        if tick >= index:
            episode_list = scrape_wikipedia(show['episode_url'])
            db.open_database_connection(True, episode_list_schema, "tv_shows", util.create_table_name(episode_list[0]['name']), episode_list_vartype)
            episode_list.remove(episode_list[0])
            #print episode_list
            for episode in episode_list:
                print("%d \t | Episode: | %d \t| %d \t| %s \t | %s" % (tick, episode['season'], episode['episode'], episode['date'], episode['title']))
                db.write_to_database(episode, episode_list_layout)
            db.close_database_connection()
            tick += 1
        else:
            tick += 1
            print "Skipping Episode: %d" % tick

if __name__ == "__main__":
    print("Starting TV Crawler...")

    if (config['update_show_indexes']):
        update_show_data()
    if (config['update_episode_lists']):
        update_show_episodes(0)

    print("Finished...")
    print("Exiting TV Crawler...")