
import MySQLdb

def create_conn():
  return MySQLdb.connect(host = "localhost", user = "fdb_app", passwd = "fdb_app%#_", db="FDB", use_unicode = True, charset = 'utf8')
