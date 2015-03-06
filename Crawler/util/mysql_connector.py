import MySQLdb as mdb
import sys

connection = None
tick = 0

def open_database_connection(table_schema):
    global connection
    connection = mdb.connect('localhost', 'test', 'test123', 'movies')
    # Creates a new temporary table to work in.
    cur = connection.cursor()
    cur.execute("DROP TABLE IF EXISTS temp_films;")
    connection.commit()
    cur.execute("DROP TABLE IF EXISTS temp_films")
    cur.execute("CREATE TABLE temp_films(%s)" % table_schema)
    cur.execute("CREATE TABLE IF NOT EXISTS films(%s)" % table_schema)
    cur.close()
    connection.commit()
    return True

def write_to_database(data, database_layout):
    # Splits the database_layout up to get the names of each variable.
    names = database_layout.split(", ")
    data_values = ""

    # Gets the name of each variable in the dictionary from the layout and concatenates the values together.
    for data_name in names:
        data_type = "\"%s\""
        if str(data[data_name]).isdigit() and data_name != "age":
            data_type = "\"%d\""

        if data_values == "":
            data_values += data_type % data[data_name]
        else:
            data_values += ", " + data_type % data[data_name]
    
    global tick;
    print("%d\t| Writing movie to database => '%s'" % (tick, data['name']))
    tick += 1;
    
    # Opens connection to the database. (host, username, password, database)
    global connection

    if connection is None:
        print("Connection to database has not been opened. Exiting.")
        return False

    # Allows execution of commands.
    cur = connection.cursor()
    # Inserts all data into the database.
    cur.execute("INSERT INTO temp_films(" + database_layout + ") VALUES(" + data_values + ")")
    cur.close()
    connection.commit()
    return True

# Commits changes and closes connection.
def close_database_connection():
    global connection
    cur = connection.cursor()
    cur.execute("DROP TABLE IF EXISTS backup_films; \
                RENAME TABLE films TO backup_films; \
                RENAME TABLE temp_films TO films;")
    cur.close()
    connection.commit()
    connection.close()
    return True