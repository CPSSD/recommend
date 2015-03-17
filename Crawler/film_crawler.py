#!/usr/bin/env python
#-*- coding: latin-1 -*-

import requests
from bs4 import BeautifulSoup
from util import file_handler as file
from util import util

database_type = "sqlite"
database_layout = "name, director, date, runtime, rating, starring, synopsis, image, age, genre"
table_schema = "id INTEGER PRIMARY KEY, name VARCHAR(255), date VARCHAR(255), runtime VARCHAR(255), rating VARCHAR(255), starring VARCHAR(255), director VARCHAR(255), synopsis TEXT, image VARCHAR(255), age VARCHAR(255), genre VARCHAR(255)"
table_vartype = "%s, %s, %s, %s, %s, %s, %s, %s, %s, %s"

# Loads in all config settings.
config = file.get_config_data("crawler.config")

# Chooses which database to use.
# Defaults to sqlite.
from util import sqlite_connector as db
if config['database_type'] == 'mysql':
    from util import mysql_connector as db
db.config = config

# Crawls through a 'YEAR_in_film' wikipedia page.
# Returns a list of movies from the year.
def crawl_wikipedia(year_start, year_end):

    film_list = []
    for year in range(year_start, year_end+1):
        year = str(year)
        print("Grabbing links from %s" % year)
        url = "http://en.wikipedia.org/wiki/%s_in_film" % year
        util.debug_print("Crawling through " + url)
        html = requests.get(url).text
        bs4 = BeautifulSoup(html)
        
        sections = {'_in_films', '_Wide-release_films', '_in_movies', '_Wide-release_movies', '_films'};
        film_tables = None
        for section in sections:
            util.debug_print(year + section)
            try:
                film_tables = bs4.find('span', {'id': year + section}).parent.find_next_siblings('table', {'class': 'wikitable'})
            except:
                None	
        if film_tables is None:
            print("Error getting correct section in " + year)
            exit(0)
        for table in film_tables:
            for film in table:
                film_name = film.find('i')
                if(film_name != -1 and film_name != None and film_name is not None):
                    try:
                        film_url = film_name.find('a')['href']
                        film_list.insert(0, film_url)
                        util.debug_print(film_url)
                    except:
                        None
        util.debug_print(year)
        util.debug_print(film_list)
    return film_list

# Scrapes the data from a given Wikipedia URL.
# Returns a dictionary of data.
def scrape_wikipedia(url):
    util.debug_print("Grabbing data from '%s'." % url)
    html = requests.get(url).text
    b = BeautifulSoup(html)

    # Initialising temporary variables.
    details = None
    release_date = None
    runtime = None
    stars = None
    director = None
    image = None

    infobox = b.find('table', {'class': 'infobox vevent'})

    # Grabs Title of the Movie from top of the page.
    name = infobox.find('th', {'class': 'summary'}).text
    name = util.clean_text(name)
    util.debug_print("1. Title: " + name)

    # Grabs Title of the Movie from top of the page.
    try:
        image = "http:" + infobox.find('img')['src']
    except:
        image = None

    # Cycles through each section in sidebar.
    for section in infobox.find_all('th'):
        query = ""

        if len(list(section.children)) > 1:
            section = section.div

        try:
            query = section.string
        except:
            None

        # Grabs Director of the Movie from sidebar.
        if query == "Directed by":
            director = section.parent.td.text
            director = util.clean_text(director)
            util.debug_print("2. Director: " + director)

        # Grabs top 3 Actors from sidebar.
        if query == "Starring":
            star_list = section.parent.td.find_all('li')
            # If there are no list elements, take links instead.
            if len(star_list) <= 0:
                star_list = section.parent.td.find_all('a')
            star_cap = 3
            star_tick = 0
            stars = ""
            for star in star_list:
                # If actor name + who they played shows up, only take actor name.
                if len(list(star.children)) > 1:
                    star = star.a
                if star != None:
                    stars += star.string + ", "
                    star_tick += 1
                    if star_tick >= star_cap:
                        util.debug_print("3. Starring: " + stars)
                        break
            stars = util.clean_text(stars)

        # Grabs release date from sidebar.
        if query == "Release dates":
            try:
                release_date = section.parent.parent.find('span', {'style': 'display:none'}).span.string
                util.debug_print("4. Release Date: "+release_date)
            except:
                None

        # Grabs runtime from sidebar.
        if query == "Running time":
            runtime = section.parent.parent.td.contents[0].split(" ")[0]
            util.debug_print("5. Runtime: "+runtime+" minutes")

    # Grabs IMDB link.
    imdb_url = None
    try:
        external_links = b.find('span', {'id': 'External_links'}).parent
        imdb_link = external_links.find_next_sibling('ul').find('a', {'href': '/wiki/Internet_Movie_Database'}).previous_sibling.previous_sibling
        imdb_url = imdb_link['href']
        util.debug_print("6. IMDB: " + imdb_url)
    except:
        util.debug_print("6. No IMDB Link")

    data = []
    if imdb_url != None or imdb_url == "":
        data = scrape_imdb(imdb_url)

    if data == None or len(data) <= 0:
        data = {'synopsis': "---", 'rating': "0", 'runtime': "0", 'age_rating': "---"}
        util.debug_print("No IMDB Data")

    if runtime == None:
        runtime = data['runtime']

    dictionary = {'name': str(util.remove_accents(name)),
                'director': str(util.remove_accents(director)),
                'date': str(release_date), # Get it changed to release_date
                'runtime': util.clean_int(runtime),
                'starring': (util.remove_accents(stars)),
                'image': str(image),
                'synopsis': (data['synopsis']),
                'rating': float(data['rating']),
                'age': data['age_rating'],
                'image': str(image),
                'wiki_url': url,
                'imdb_url': str(imdb_url),
                'genre': data['genre']}
    return dictionary

# Scrapes the data from a given IMDB URL.
# Returns a dictionary of data.
def scrape_imdb(url):
    util.debug_print("\tGrabbing data from IMDB")
    html = requests.get(url).text
    bs4 = BeautifulSoup(html)

    description = bs4.find('p', {'itemprop': 'description'})
    if description != None:
        description = util.clean_text(description.text)
        util.debug_print("\tA. Description: " + description)
    else:
        description = "---"

    rating = bs4.find('span', {'itemprop': 'ratingValue'})
    if rating != None:
        rating = rating.string
        util.debug_print("\tB. Rating: " + rating)
    else:
        rating = "0"

    runtime = bs4.find('time', {'itemprop': 'duration'})
    if runtime != None:
        runtime = util.clean_text(runtime.string)
        util.debug_print("\tC. Runtime: " + runtime)
    else:
        runtime = "0"

    genre_list = bs4.findAll('span', {'itemprop': 'genre'})
    genre_text = "Unknown"
    if genre_list != None:
        genre_text = ""
        for genre in genre_list:
            if genre_text is not "Unknown":
                genre_text += "+"
            genre_text += (util.clean_text(genre.text))

    age = bs4.find('span', {'itemprop': 'contentRating'})
    if age != None:
        age = age['content']
        util.debug_print("\tD. ESRB Rating: " + age)
    else:
        age = "---"

    return {'synopsis': util.clean_text(description),
            'rating': float(rating),
            'runtime': util.clean_int(runtime),
            'age_rating': util.clean_text(age),
            'genre': util.clean_text(genre_text)}

def save_to_database(data):
    global database_layout
    db.write_to_database(data, database_layout)
    return True # To verify that there were no errors.

if __name__ == "__main__":
    print("Starting Film Crawler...")

    film_list = crawl_wikipedia(config['start_year'], config['end_year'])
    print(len(film_list))

    db.open_database_connection(True, table_schema, config['database_file_name'], "films", table_vartype)
    tick = 0
    for film in film_list:
        if tick < config['film_limit']:
            try:
                 print "%d: \t Saving %s to database." % (tick, film)
                 save_to_database(scrape_wikipedia("http://en.wikipedia.org" + film))
                 tick += 1
            except:
                print("Error with %s" % film)
        else:
            print("Got enough films.")
            break

    db.close_database_connection()
    print("Exiting Film Crawler...")