import urllib as url
import os
import socket
import tv_crawler as tv

socket.setdefaulttimeout(3)
image_folder = "images/"
directories = {"film/", "tv/"}

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
		print "Saving: %s" % file_name
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
		print "    - File Saved: %s" % file_name
	except:
		print "Timed out..."
	
def download_all_images(series_list, type):
	for show in series_list:
		url = show["image"]
		file_type = url.split(".")[-1]
		if url != "" and url != None and url != " ":
			file_name = show['location'] + "." + file_type
			print (file_name)
			download_file(url, type + "/" + file_name)

if __name__ == "__main__":
	print "Downloading Images..."
	setup()
	series_list = tv.get_show_list(True)
	download_all_images(series_list, "tv")

