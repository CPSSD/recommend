#!/usr/bin/env python
#-*- coding: latin-1 -*-

import requests
from bs4 import BeautifulSoup
import database_connector
import unicodedata

database_layout = "name, director, date, runtime, rating, starring, synopsis, image, age"
debug = False

# Debug printing. Only outputs when debug mode is on.
def debug_print(output):
    global debug
    if debug:
        print(output)

# Cleans and returns the given string.
def clean_text(input):
    if input is not None:
        input = input.replace("\n", " ")  # Removes all new lines.
        input = input.replace("\"", "'")
        input = input.lstrip()
        input = input.rstrip()
        input = input.split("[")[0]
        input = input.split("See full summary")[0]
        input = remove_accents(input)
        return input  # lstrip removes whitespace at beginning.
    debug_print("Exiting due to input error in clean_text()")
    exit()
    return ""

# Cleans and returns the given int.
def clean_int(input):
    try:
        if str(input).isdigit():
            return int(input)
    except:
        None

    if input != None:
        input = input.lstrip()
        input = input.rstrip()

        input2 = remove_accents(input)
        input2 = input.replace(u'xa0', " ")
        input2 = input.split(" ")[0]
        if str(input2).isdigit():
            return int(input2)
        try:
            input2 = input.split(u'xa0')[0]
            if str(input2).isdigit():
                return int(input2)
        except:
            None
    return int(input)

# Recives a Unicode string as input and removes the accents.
def remove_accents(input):
    input = unicode(input)
    decoded_string = unicodedata.normalize('NFKD', input).encode('ASCII', 'ignore')
    return decoded_string

# Crawls through a 'YEAR_in_film' wikipedia page.
# Returns a list of movies from the year.
def crawl_wikipedia(year_start, year_end):

    film_list = []
    for year in range(year_start, year_end+1):
        year = str(year)
        url = "http://en.wikipedia.org/wiki/%s_in_film" % year
        debug_print("Crawling through " + url)
        html = requests.get(url).text
        bs4 = BeautifulSoup(html)

        film_tables = bs4.find('span', {'id': year + '_films'}).parent.find_next_siblings('table', {'class': 'wikitable'})
        for table in film_tables:
            for film in table:
                film_name = film.find('i')
                if(film_name != -1 and film_name != None and film_name is not None):
                    try:
                        film_url = film_name.find('a')['href']
                        film_list.insert(0, film_url)
                        debug_print(film_url)
                    except:
                        None
        debug_print(year)
        debug_print(film_list)
    return film_list

# Scrapes the data from a given Wikipedia URL.
# Returns a dictionary of data.
def scrape_wikipedia(url):
    debug_print("Grabbing data from '%s'." % url)
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
    name = clean_text(name)
    debug_print("1. Title: " + name)

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
            director = clean_text(director)
            debug_print("2. Director: " + director)

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
                        debug_print("3. Starring: " + stars)
                        break
            stars = clean_text(stars)

        # Grabs release date from sidebar.
        if query == "Release dates":
            try:
                release_date = section.parent.parent.find('span', {'style': 'display:none'}).span.string
                debug_print("4. Release Date: "+release_date)
            except:
                None

        # Grabs runtime from sidebar.
        if query == "Running time":
            runtime = section.parent.parent.td.contents[0].split(" ")[0]
            debug_print("5. Runtime: "+runtime+" minutes")

    # Grabs IMDB link.
    imdb_url = None
    try:
        external_links = b.find('span', {'id': 'External_links'}).parent
        imdb_link = external_links.find_next_sibling('ul').find('a', {'href': '/wiki/Internet_Movie_Database'}).previous_sibling.previous_sibling
        imdb_url = imdb_link['href']
        debug_print("6. IMDB: " + imdb_url)
    except:
        debug_print("6. No IMDB Link")

    data = []
    if imdb_url != None or imdb_url == "":
        data = scrape_imdb(imdb_url)

    if data == None or len(data) <= 0:
        data = {'synopsis': "---", 'rating': "0", 'runtime': "0", 'age_rating': "---"}
        debug_print("No IMDB Data")

    if runtime == None:
        runtime = data['runtime']

    dictionary = {'name': str(remove_accents(name)),
                'director': str(remove_accents(director)),
                'date': str(release_date), # Get it changed to release_date
                'runtime': clean_int(runtime),
                'starring': (remove_accents(stars)),
                'image': str(image),
                'synopsis': (data['synopsis']),
                'rating': float(data['rating']),
                'age': data['age_rating'],
                'image': str(image),
                'wiki_url': url,
                'imdb_url': str(imdb_url)}
    return dictionary

# Scrapes the data from a given IMDB URL.
# Returns a dictionary of data.
def scrape_imdb(url):
    debug_print("\tGrabbing data from IMDB")
    html = requests.get(url).text
    bs4 = BeautifulSoup(html)

    description = bs4.find('p', {'itemprop': 'description'})
    if description != None:
        description = clean_text(description.text)
        debug_print("\tA. Description: " + description)
    else:
        description = "---"

    rating = bs4.find('span', {'itemprop': 'ratingValue'})
    if rating != None:
        rating = rating.string
        debug_print("\tB. Rating: " + rating)
    else:
        rating = "0"

    runtime = bs4.find('time', {'itemprop': 'duration'})
    if runtime != None:
        runtime = clean_text(runtime.string)
        debug_print("\tC. Runtime: " + runtime)
    else:
        runtime = "0"

    age = bs4.find('span', {'itemprop': 'contentRating'})
    if age != None:
        age = age['content']
        debug_print("\tD. ESRB Rating: " + age)
    else:
        age = "---"

    return {'synopsis': clean_text(description),
            'rating': float(rating),
            'runtime': clean_int(runtime),
            'age_rating': clean_text(age) }

def save_to_database(data):
    global database_layout
    database_connector.write_to_database(data, database_layout)
    return True # To verify that there were no errors.

if __name__ == "__main__":

    print("Starting Film Crawler...")
    film_list = crawl_wikipedia(2014, 2014)
    print(len(film_list))
    database_connector.open_database_connection()
    for film in film_list:
        try:
            save_to_database(scrape_wikipedia("http://en.wikipedia.org" + film))
        except:
            print("Error with %s" % film)
    database_connector.close_database_connection()
    print("Exiting Film Crawler...")