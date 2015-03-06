from util import util
import unittest

# Unit Testing for the Film Crawler.
class TestUtil(unittest.TestCase):

    def test_to_clean_string(self):
        '''String cleaning tests'''
        self.assertEqual("Test String", util.clean_text("        Test String           "))
        self.assertEqual("Test Bracket", util.clean_text("        Test Bracket[1]    "))
        self.assertEqual("Test Line Break", util.clean_text("  \nTest Line Break[1]    "))

    def test_to_clean_int(self):
        '''String should be cleaned and converted to an integer'''
        self.assertEqual(3, util.clean_int("      3        "))
        self.assertEqual(70, util.clean_int("    70 min    "))
        self.assertEqual(130, util.clean_int("130 minutes  "))

    def test_to_remove_accents(self):
        '''Attempt to remove accents from unicode string'''

if __name__ == "__main__":
    unittest.main()