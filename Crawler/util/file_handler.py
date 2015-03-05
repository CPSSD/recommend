import util

file = None

def open_file(file_name, type):
    print("Opening %s" % file_name)
    global file
    file = open(file_name, type)
    
def close_file():
    print("Closing file")
    global file
    file.close()
    
def output(output):
    file.write("%s\n" % output)

def read_config():
    print("Reading File...")
    global file
    data = {}
    for line in file:
        if not startswith("#"):
            line = line.split("=")
            data[util.clean_text(line[0])] = util.clean_text(line[1])
    return data
    
def get_config_data(file_name):
    open_file(file_name, 'r')
    data = read_config()
    close_file()
    return data