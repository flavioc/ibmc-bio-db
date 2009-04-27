#!/usr/bin/python

import MySQLdb
import sys
from connection import *
from utils import *

db = create_conn()

def has_type(name):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy_name_type WHERE name = %s" % (sql_to_string(name))
  c.execute(sql)
  row = c.fetchone()
  return row is not None

def add_type(name):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy_name_type(name) VALUES(%s)" \
    % (sql_to_string(name))
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  return id

def check_type(name):
  if has_type(name):
    print("Taxonomy type %s already inserted" % name)
  else:
    add_type(name)
    print("Taxonomy type %s inserted" % name)

def check_types(ls):
  for name in ls:
    check_type(name)

check_types(["synonym", "in-part", "blast name", "genbank common name",
             "equivalent name", "includes", "authority",
             "misspelling", "common name", "misnomer",
             "genbank synonym", "unpublished name",
             "anamorph", "genbank anamorph",
             "teleomorph", "acronym",
             "genbank acronym"])
db.close()
