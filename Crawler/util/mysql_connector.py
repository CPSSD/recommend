import MySQLdb as mdb
import sys
import file_handler as file

connection = None
table_name = None
table_vartype = ""
config = None
tick = 0

# Loads the config file. Needed for the username and password.

def open_database_connection(create_database, table_schema, database, table, vartype):
    if create_database:
        global connection
        global table_name
        global table_vartype
        table_vartype = vartype.split(", ")
        table_name = table

        connection = mdb.connect(config['db_ip'], config['db_username'], config['db_password'], database)
        # Creates a new temporary table to work in.
        cur = connection.cursor()
        cur.execute("DROP TABLE IF EXISTS temp_%s;" % table_name)
        connection.commit()
        print("CREATE TABLE temp_%s(%s)" % (table_name, table_schema))
        cur.execute("CREATE TABLE temp_%s(%s)" % (table_name, table_schema))
        cur.execute("CREATE TABLE IF NOT EXISTS %s(%s)" % (table_name, table_schema))
        cur.close()
        connection.commit()
    else:
        connection = mdb.connect(config['db_ip'], config['db_username'], config['db_password'], database)
        cur = connection.cursor()
        cur.execute("SELECT %s FROM %s" % (table_schema, database))
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

def write_to_database(data, database_layout):
    # Splits the database_layout up to get the names of each variable.
    database_layout = "id, %s" % database_layout
    data['id'] = tick
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
    print(data)
    print(database_layout)
    tick += 1;
    
    # Opens connection to the database. (host, username, password, database)
    global connection

    if connection is None:
        print("Connection to database has not been opened. Exiting.")
        return False

    # Allows execution of commands.
    cur = connection.cursor()
    # Inserts all data into the database.
    cur.execute("INSERT INTO temp_%s(%s) VALUES(%s)" % (table_name, database_layout, data_values))
    cur.close()
    connection.commit()
    return True

def create_template_table(show_list, schema):
    global connection
    connection = mdb.connect(config['db_ip'], config['db_username'], config['db_password'], database)
    cur = connection.cursor()
    for show in show_list:
        print show['location']
        cur.execute("CREATE TABLE IF NOT EXISTS %s(%s)" % (show['location'], schema))
        connection.commit()
    cur.close()
    connection.commit()
    connection.close()

# Commits changes and closes connection.
def close_database_connection():
    global connection
    global table_name
    cur = connection.cursor()
    cur.execute("DROP TABLE IF EXISTS backup_%s" % table_name)
    connection.commit()
    cur.execute("RENAME TABLE %s TO backup_%s;" % (table_name, table_name))
    connection.commit()
    cur.execute("RENAME TABLE temp_%s TO %s;" % (table_name, table_name))
    connection.commit()
    cur.close()
    connection.close()
    return True