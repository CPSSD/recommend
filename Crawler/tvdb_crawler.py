#!/usr/bin/env python
#-*- coding: latin-1 -*-

import requests
import urllib
import xml.etree.ElementTree as xml
from util import util
from util import sqlite_connector as database
from util import file_handler as file
import tv_crawler as tv

# Loads the config file.
config = file.get_config_data("crawler.config")

api_key = config['api_key']
account_key = config['account_key']
main_url = "http://thetvdb.com"
language = "en"
db = database.Database()

def get_root(url):
    util.debug_print(url)
    string = requests.get(url).text
    string = string.replace("\n", " ")
    string = string.replace("\r", "")
    string = util.remove_accents(string)
    return xml.fromstring(string)

def get_mirror():
    root = get_root("%s/api/%s/mirrors.xml" % (main_url, api_key))
    # Get a mirror that supports all functionality (typemask = 7)
    for mirror in root:
        if int(get_attrib(mirror, 'typemask', True)) == 7:
            return get_attrib(mirror, 'mirrorpath', True)
            
def get_time():
    root = get_root("%s/api/Updates.php?type=none" % main_url)
    return int(get_attrib(root, 'Time', True))

def get_attrib(data, attrib, get_text):
    grabbed_data = data.find(attrib)
    if grabbed_data == None:
        return False
    if get_text:
        return grabbed_data.text
    return grabbed_data
    return False
    
def get_series(series_name, imdb_link):
    root = get_root("%s/api/GetSeries.php?seriesname=\"%s\"" % (main_url, series_name))
    data = {}
    attrib_list = ('banner', 'seriesid', 'IMDB_ID', 'Overview', 'Network', 'FirstAired')
    name_list = ('image', 'id', 'imdb', 'synopsis', 'network', 'start_date')
    for series in root.findall('Series'):
        data['name'] = get_attrib(series, 'SeriesName', True)
        grabbed_link = "http://www.imdb.com/title/%s/" % get_attrib(series, 'IMDB_ID', True)
        if imdb_link == "" or imdb_link == grabbed_link:
            for attrib, name in zip(attrib_list, name_list):
                data[name] = get_attrib(series, attrib, True)
            return data
    
def get_series_data(series_id):
    root = get_root("%s/api/%s/series/%s/all/en.xml" % (main_url, api_key, series_id))
    data = []
    data.append(get_series_info(root))
    data.append(get_series_episodes(root))
    return data
    
def get_series_info(root):
    root = root.find('Series')
    data = {}
    attrib_list = ('id', 'Actors', 'IMDB_ID', 'Genre', 'Network', 'Overview', 'Rating', 'SeriesName', 'poster')
    name_list = ('id', 'stars', 'imdb_id', 'genre', 'network', 'synopsis', 'rating', 'title', 'image')
    for attrib, name in zip(attrib_list, name_list):
        try:    
            if name == "image":
                print("Updating Image.")
                data[name] = "%s/banners/%s" % (main_url, get_attrib(root, attrib, True))
            else:
                data[name] = get_attrib(root, attrib, True)
        except:
            pass
    return data
    
def get_series_episodes(root):
    episode_list_data = []    
    for episode in root.findall('Episode'):
        data = {}
        data['season'] = get_attrib(episode, 'Combined_season', True)
        if int(data['season']) > 0:
            data['episode'] = get_attrib(episode, 'EpisodeNumber', True)
            data['title'] = get_attrib(episode, 'EpisodeName', True)
            data['date'] = get_attrib(episode, 'FirstAired', True)
            data['rating'] = get_attrib(episode, 'Rating', True)
            data['synopsis'] = get_attrib(episode, 'Overview', True)
            episode_list_data.append(data)
    return episode_list_data
        
def update_episode_data(database_name, data):
    episode_db = database.Database()
    episode_db.open_database_connection(True, tv.episode_list_schema, config['database_file_name'], util.create_table_name(data[0]['title']), tv.episode_list_vartype)
    for episode in data[1]:
        try:
            episode['season'] = int(episode['season'])
            episode['episode'] = int(episode['episode'])
            episode_db.write_to_database(episode, tv.episode_list_layout)
        except:
            print("Error with episode in \"%s\"" % data[0]['title'])
    episode_db.close_database_connection()        

def compare_data(data_old, data_new):
    data_types = {"name", "image", "imdb_url"};
    data_new["imdb_url"] = "http://www.imdb.com/title/%s/" % data_new["imdb_id"]
    data_new["name"] = data_new["title"]
    for data in data_types:
        if(data_old[data] == "" or data_old[data] == 0.0 or data_old[data] == None):
            data_old[data] = data_new[data]
        # Images from here are generally better. So always swap.
        if ((data == "image" and data_new["image"] != "http://thetvdb.com/banners/None")):
            data_old[data] = data_new[data]
    print(data_old['image'])
    return data_old
    
def clean_data(data):
    data['rating'] = float(data['rating'])
    return data

mirror_link = main_url # get_mirror()
util.debug_print("Mirror Link: " + mirror_link)

time = 1426866230 # get_time()
util.debug_print("Time: %d" % time)
    
def update_show_data(show_limit):
    series_list = tv.get_show_list(True)
    tick = 0
    file.open_file("tvdb_problems.txt", "w")
    show_limit = config['episode_limit']
    db.open_database_connection(True, tv.tv_show_schema, config['database_file_name'], "tv_shows", tv.tv_show_vartype)
    for saved_data in series_list:
        if tick > config['episode_offset']:   
    #    if show_limit == -1 or tick < show_limit:
            util.debug_print(saved_data)
            series = saved_data['name']
            try:
                series_info = get_series(series, saved_data['imdb_url'])
                if series_info != None:
                    series_data = get_series_data(series_info['id'])
                    update_episode_data(saved_data['location'], series_data);
                    new_data = clean_data(compare_data(saved_data, series_data[0]))
                    db.write_to_database(new_data, tv.tv_show_layout)
                    print(("%d: Success with" % tick, series))
                else:
                    print(("%d: Error with" % tick, series))
                    file.output("%d: Error: -> %s" % (tick, series))
            except Exception as e:
                print "%d: Exception with" % tick, series
                file.output("%d: Exception: -> %s" % (tick, series))
        tick += 1
        util.debug_print("***************************")
    db.close_database_connection()   
    file.close_file()
    print("* Finished TVDB Crawl")

if __name__ == "__main__":
    update_show_data(-1)