import util

file = None

def open_file(file_name, type):
    #print("* Opening %s" % file_name)
    global file
    file = open(file_name, type)
    
def close_file():
    global file
    file.close()
    
def output(output):
    file.write("%s\n" % output)

def read_config():
    global file
    data = {}
    for line in file:
        if (not line.startswith("#")) and (not line.startswith("\n")):
            line = line.split("=")
            section = util.clean_text(line[0])
            value = util.clean_text(line[1])
            data[section] = value
            if value.isdigit():
                data[section] = util.clean_int(value)
    return data
    
def get_config_data(file_name):
    open_file(file_name, 'r')
    data = read_config()
    close_file()
    return data