import subprocess
import sys
import time
import datetime
from util import file_handler as file

config = None
film_offset = 7 + 1
tv_offset = 2 + 1


def run():
	global next_time_film
	global start_time_tv
	global start_time_film
	global next_time_tv
	global current_time
	while(running):
		current_time = time.strftime("%Y-%m-%d")
		print current_time
		print "Film:", start_time_film, "=>", next_time_film
		print "Tv:", start_time_tv, "=>", next_time_tv
		
		if(time.strptime(current_time, "%Y-%m-%d") >= time.strptime(next_time_film, "%Y-%m-%d")):
			print("---------- Starting Film Crawler ----------");
			subprocess.call([sys.executable, 'film_crawler.py'])
			start_time_film = current_time
			next_time_film = get_date_offset(start_time_film, film_offset)
			
			update_config_file()
			
			print("----------------------------------\nFinished Film Crawler")
		
		if(time.strptime(current_time, "%Y-%m-%d") >= time.strptime(next_time_tv, "%Y-%m-%d")):
			print("---------- Starting TV Crawler ----------");
			subprocess.call([sys.executable, 'tv_crawler.py'])
			start_time_tv = current_time
			next_time_tv = get_date_offset(start_time_tv, tv_offset)	
			
			update_config_file()
			
			print("----------------------------------\nFinished TV Crawler")
			
		time.sleep(30)
	
def get_update_file():
	global config
	file.open_file('times.config', 'r')
	config = file.read_config()
	file.close_file()

def update_config_file():
	file.open_file('times.config', 'w')
	file.output("film = %s" % start_time_film)
	file.output("tv = %s" % start_time_tv)
	file.close_file()

def get_date_offset(date1, offset):
	date1 = date1.split("-")
	for i in range(0, 3):
		date1[i] = int(date1[i])
	date2 = date1;
	daysInMonth = get_days_in_month(date1[1])
	# If total date + offset exceeds total days in month.
	if((date2[2]+offset-1) > daysInMonth):
		date2[1] = (date2[1]+1);
		date2[2] = ((date2[2]+offset-1) - daysInMonth);
	else:
		date2[2] = (date1[2] + offset - 1);
	

	return "%s-%s-%s" % (date2[0], date2[1], date2[2])

def get_days_in_month(month):
	if(month == 12):
		month = 0
	end_of_month = datetime.date(2000, int(month)+1, 1) - datetime.timedelta(days=1)
	return int(end_of_month.strftime("%d"))
	
if __name__ == "__main__":
	print("Staring this shit..")
	global next_time_film
	global start_time_tv
	global start_time_film
	global next_time_tv
	global current_time
	global config
	get_update_file()
	
	start_time_film = config['film']
	next_time_film = get_date_offset(start_time_film, film_offset)

	start_time_tv = config['tv']
	next_time_tv = get_date_offset(start_time_tv, tv_offset)

	current_time = time.strftime("%Y-%m-%d")
	
	running = True
	run()
