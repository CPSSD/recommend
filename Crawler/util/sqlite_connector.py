import sqlite3 as lite
import sys

connection = None
table_name = None
tick = 0

def open_database_connection(table_schema, table):
    global connection
    global table_name
    table_name = table
    connection = lite.connect('database/%s.db' % table_name)

    cur = connection.cursor()
    cur.execute("DROP TABLE IF EXISTS temp_%s;" % table_name)
    connection.commit()
    cur.execute("DROP TABLE IF EXISTS temp_%s" % table_name)
    cur.execute("CREATE TABLE temp_%s(%s)" % (table_name, table_schema))
    cur.execute("CREATE TABLE IF NOT EXISTS %s(%s)" % (table_name, table_schema))
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
    global table_name
    cur.execute("INSERT INTO temp_%s(%s) VALUES(%s)" % (table_name, database_layout, data_values))
    cur.close()
    connection.commit()
    return True

# Commits changes and closes connection.
def close_database_connection():
    global connection
    global table_name
    cur = connection.cursor()
    cur.execute("DROP TABLE IF EXISTS backup_%s;" % table_name)
    connection.commit()
    cur.execute("ALTER TABLE %s RENAME TO backup_%s;" % (table_name, table_name))
    connection.commit()
    cur.execute("ALTER TABLE temp_%s RENAME TO %s;" % (table_name, table_name))
    connection.commit()
    cur.close()
    connection.commit()
    connection.close()
    return True
