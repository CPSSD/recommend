from util import sqlite_connector as db
from util import util
import tv_crawler
import unittest

# Unit Testing for the Film Crawler.
class TestCrawler(unittest.TestCase):
    print("Test")

    def test_wikipedia_scrape(self):
        ''' Scraped data should be cleaned '''
        data = tv_crawler.scrape_wikipedia("/wiki/List_of_Arrow_episodes")
        self.assertEqual(data[0]['name'], "Arrow")
        data.remove(data[0])
        for episode in data:
            self.assertEqual(episode['title'], util.clean_text(episode['title']))

if __name__ == "__main__":
    unittest.main()