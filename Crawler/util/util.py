import unicodedata

debug = False

# Debug printing. Only outputs when debug mode is on.
def debug_print(output):
    global debug
    if debug:
        print(output)

# Cleans and returns the given string.
def clean_text(input):
    if input is not None:
        input = input.replace("\n", " ")  # Removes all new lines.
        input = input.replace("\"", "'")
        input = input.lstrip()
        input = input.rstrip()
        input = input.split("[")[0]
        input = input.split("See full summary")[0]
        input = remove_accents(input)
        return input  # lstrip removes whitespace at beginning.
    debug_print("Exiting due to input error in clean_text()")
    exit()
    return ""

# Cleans and returns the given int.
def clean_int(input):
    try:
        if str(input).isdigit():
            return int(input)
    except:
        None

    if input != None:
        input = input.lstrip()
        input = input.rstrip()

        input2 = remove_accents(input)
        input2 = input.replace(u'xa0', " ")
        input2 = input.split(" ")[0]
        if str(input2).isdigit():
            return int(input2)
        try:
            input2 = input.split(u'xa0')[0]
            if str(input2).isdigit():
                return int(input2)
        except:
            None
    return int(input)

# Recives a Unicode string as input and removes the accents.
def remove_accents(input):
    input = unicode(input)
    decoded_string = unicodedata.normalize('NFKD', input).encode('ASCII', 'ignore')
    return decoded_string

# Compares the 2 dates.
# Returns true if date_1 is bigger than or equal to date_2.
# Returns false if date_2 is bigger.
def compare_dates(date_1, date_2):
    if date_1 == "-   NA   -" and date_2 != "-   NA   -":
        return True
    elif date_1 != "-   NA   -" and date_2 == "-   NA   -":
        return False
    elif date_1 == "-   NA   -" and date_2 == "-   NA   -":
        return True

    date_1 = date_1.split("-")
    date_2 = date_2.split("-")

    # 2015 5 3 | 2015 6 2
    if date_1[0] > date_2[0]:
        return True
    elif date_1[0] == date_2[0]:

        if date_1[1] > date_2[1]:
            return True
        elif date_1[1] == date_2[1]:

            if date_1[2] >= date_2[2]:
                return True
    return False
