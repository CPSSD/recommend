import film_crawler
import database_connector
import unittest

# Unit Testing for the Film Crawler.
class TestCrawler(unittest.TestCase):

    def test_to_clean_string(self):
        '''String cleaning tests'''
        self.assertEqual("Test String", film_crawler.clean_text("        Test String           "))
        self.assertEqual("Test Bracket", film_crawler.clean_text("        Test Bracket[1]    "))
        self.assertEqual("Test Line Break", film_crawler.clean_text("  \nTest Line Break[1]    "))

    def test_to_clean_int(self):
        '''String should be cleaned and converted to an integer'''
        self.assertEqual(3, film_crawler.clean_int("      3        "))
        self.assertEqual(70, film_crawler.clean_int("    70 min    "))
        self.assertEqual(130, film_crawler.clean_int("130 minutes  "))

    def test_wikipedia_crawl(self):
        '''Should return the correct link for the movies wikipedia page'''
        film_list = film_crawler.crawl_wikipedia(2015, 2015)
        for film in film_list:
            self.assertTrue(film.startswith("/wiki/"))

    def test_imdb_scrape(self):
        '''Each section of IMDB data should be correct'''
        data = film_crawler.scrape_imdb("http://www.imdb.com/title/tt0926084/");
        self.assertEqual(data['synopsis'], film_crawler.clean_text(data['synopsis']))
        self.assertEqual(data['rating'], float(data['rating']))
        self.assertEqual(data['runtime'], film_crawler.clean_int(data['runtime']))
        self.assertEqual(data['age_rating'], film_crawler.clean_text(data['age_rating']))

    def test_wikipedia_scrape(self):
        '''Should return the correct data from the wikipedia page'''
        data = film_crawler.scrape_wikipedia("http://en.wikipedia.org/wiki/Divergent_(film)");
        self.assertEqual(data['name'], film_crawler.clean_text(data['name']))
        self.assertEqual(data['director'], film_crawler.clean_text(data['director']))
        self.assertEqual(data['runtime'], film_crawler.clean_int(data['runtime']))
        self.assertEqual(data['starring'], film_crawler.clean_text(data['starring']))
     #  self.assertTrue(data['image'].startswith("http://en.wikipedia.com/"))
        self.assertTrue(data['wiki_url'].startswith("http://en.wikipedia.org/wiki/"))

    def test_database_save(self):
        '''Should save the data to the database without error'''
        test_data = film_crawler.scrape_wikipedia("http://en.wikipedia.org/wiki/Harry_Potter_and_the_Half-Blood_Prince_(film)")
        self.assertTrue(database_connector.open_database_connection())
        self.assertTrue(film_crawler.save_to_database(test_data))
        self.assertTrue(database_connector.close_database_connection())

    def test_to_remove_accents(self):
        '''Attempt to remove accents from unicode string'''

if __name__ == "__main__":
    unittest.main()