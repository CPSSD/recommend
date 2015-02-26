# Crawler #
===============

### Launching Crawlers:

* Run `film_crawler.py` to start the film crawler.<br />
    _- Change the `start_year` and `end_year` variables in the `Config/Crawler.config` file to specify the range._
    
* Run `tv_crawler.py` to start the tv crawler.

### Database Changing:

* Set the `database_type` in the `Config/Crawler.config` file to either `mysql` or `sqlite` depending on which you would prefer to use.

### Testing:

* Test Film Crawler with: `test_film_crawler.py` or `test_film_crawler.py -v` for more descriptive tests.

* Test Util with: `test_util.py` or `test_util.py -v` for more descriptive tests.

* Test TV Crawler with: `test_tv_crawler.py` or `test_tv_crawler.py -v` for more descriptive tests.

