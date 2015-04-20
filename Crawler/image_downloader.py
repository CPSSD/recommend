import urllib as url
import os
import socket
from util import util
import tv_crawler as tv

socket.setdefaulttimeout(3)
image_folder = "images/"
directories = {"film/", "tv/"}
db = tv.database.Database()

def setup():
    try:
        os.mkdir(image_folder)
    except OSError:
        print("* Image directory already exists.")
    
    for media in directories:
        try:
            os.mkdir(image_folder + media)
        except:
            print ("* %s directory already exists." % media)

def download_file(url_link, file_name):
    try:
        util.debug_print("Saving: %s" % file_name)
        connection = url.urlopen(url_link)
        file_name = "%s%s" % (image_folder, file_name)
        file = open(file_name, 'wb')
        chunk = 2048
        tick = 0
        while True:
            buffer = connection.read(chunk)
            if not buffer:
                break
            file.write(buffer)
            tick +=1
        file.close()
        print("    - File Saved: %s" % file_name)
    except:
        print("    - Timed out ...")
        return False
    return True
    
def download_all_images(media_list, type, database):
    setup()
    for media in media_list:
        url = media["image"]
        file_type = url.split(".")[-1]
        if url != "" and url != None and url != " ":
            file_name = media['location'] + "." + file_type
            if (download_file(url, type + "/" + file_name)):
                update_table(media['location'], "images/%s/%s" % (type, file_name), database)

def update_table(id, file_name, database):
	print("Updating Table")
	database.update_in_database(file_name, "image_location", "tv_shows", id, "location") 
	print("Done")