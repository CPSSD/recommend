import sqlite3 as lite
import sys

class Database:

    connection = None
    table_name = None
    table_vartype = ""
    config = None
    
    def open_database_connection(self, create_database, table_schema, database, table, vartype):
        if create_database:
            self.table_vartype = vartype.split(", ")
            self.table_name = table
            self.connection = lite.connect('database/%s.db' % database)

            cur = self.connection.cursor()
            cur.execute("DROP TABLE IF EXISTS temp_%s;" % self.table_name)
            self.connection.commit()
            cur.execute("DROP TABLE IF EXISTS temp_%s" % self.table_name)
            cur.execute("CREATE TABLE temp_%s(%s)" % (self.table_name, table_schema))
            cur.execute("CREATE TABLE IF NOT EXISTS %s(%s)" % (self.table_name, table_schema))
            cur.close()
            self.connection.commit()
        else:
            self.connection = lite.connect('database/%s.db' % database)
            cur = self.connection.cursor()
            cur.execute("SELECT %s FROM %s;" % (table_schema, table))
            data = cur.fetchall()
            variable_names = table_schema.split(", ")
            data_list = []
            for row in data:
                tick = 0
                section = {}
                for var in variable_names:
                    section[var] = row[tick]
                    tick += 1
                data_list.append(section)
            return data_list
        return True

    def write_to_database(self, data, database_layout):
        # Splits the database_layout up to get the names of each variable.
        names = database_layout.split(", ")
        data_values = ""

        # Gets the name of each variable in the dictionary from the layout and concatenates the values together.
        tick = 0
        for data_name in names:
            data_type = "%s" % self.table_vartype[tick]

            if data_values == "":
                data_values +=  ("\"" + data_type + "\"") % data[data_name]
            else:
                data_values += (", \"" + data_type + "\"") % data[data_name]
            tick += 1
            
        # Opens connection to the database. (host, username, password, database)
        if self.connection is None:
            print("Connection to database has not been opened. Exiting.")
            return False

        # Allows execution of commands.
        cur = self.connection.cursor()
        # Inserts all data into the database.
        cur.execute("INSERT INTO temp_%s(%s) VALUES(%s)" % (self.table_name, database_layout, data_values))
        cur.close()
        self.connection.commit()
        return True

    def create_template_tables(self, show_list, schema):
        self.connection = lite.connect('database/%s.db' % Database.config['database_file_name'])
        cur = self.connection.cursor()
        for show in show_list:
            print show['location']
            cur.execute("CREATE TABLE IF NOT EXISTS %s(%s)" % (show['location'], schema))
            self.connection.commit()
        cur.close()
        self.connection.commit()
        self.connection.close()

    def update_in_database(self, data, data_type, table, id, id_type):
        self.connection = lite.connect('database/database.db')
        cur = self.connection.cursor()
        cur.execute("UPDATE %s SET %s=\"%s\" WHERE %s=\"%s\"" % (table, data_type, data, id_type, id))
        self.connection.commit()
        cur.close()
        self.connection.close()
        
    # Commits changes and closes connection.
    def close_database_connection(self):
        cur = self.connection.cursor()
        cur.execute("DROP TABLE IF EXISTS backup_%s;" % self.table_name)
        self.connection.commit()
        cur.execute("ALTER TABLE %s RENAME TO backup_%s;" % (self.table_name, self.table_name))
        self.connection.commit()
        cur.execute("ALTER TABLE temp_%s RENAME TO %s;" % (self.table_name, self.table_name))
        self.connection.commit()
        cur.close()
        self.connection.close()
        return True
