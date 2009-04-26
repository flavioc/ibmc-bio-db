def sql_to_bool(val):
  if val is True:
    return "TRUE"
  else:
    return "FALSE"

def sql_to_string(val):
  if val == "":
    return "NULL"
  else:
    return "\"%s\"" % val

def sql_to_symbol(val):
  return "'%s'" % val
