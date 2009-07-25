#!/usr/bin/python

import MySQLdb
import sys
from connection import *
from utils import *

db = create_conn()
ranks = {}

def has_rank(name):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy_rank WHERE name = %s" % (sql_to_string(name))
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return None
  return row[0]

def add_rank(name):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy_rank(name, is_default) VALUES(%s, TRUE)" \
    % (sql_to_string(name))
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  print("Rank %s added to database." % name)
  return id

def ensure_rank(name):
  try:
    id = ranks[name]
  except KeyError:
    id = has_rank(name)
    if id is None:
      id = add_rank(name)
      ranks[name] = id
  return id

def set_parent(id, parent_id):
  cursor = db.cursor()
  sql = "UPDATE taxonomy_rank SET parent_id = %d WHERE id = %d" \
    % (parent_id, id)
  cursor.execute(sql)
  db.commit()

def process_rank(name, parent_name):
  name_id = ensure_rank(name)
  parent_id = ensure_rank(parent_name)
  set_parent(name_id, parent_id)
  print(parent_name + " set as parent of " + name)

def process_ranks(hash):
  for name, parent in hash.items():
    process_rank(name, parent)

process_ranks({"class" : "phylum",
               "family" : "order",
               "forma" : "subvarietas",
               "genus" : "family",
               "infraclass" : "subclass",
               "infraorder" : "suborder",
               "kingdom" : "superkingdom",
               "no rank" : "no rank",
               "order" : "class",
               "parvorder" : "order",
               "phylum" : "kingdom",
               "species" : "genus",
               "species group" : "no rank",
               "species subgroup" : "species group",
               "subclass" : "class",
               "subfamily" : "family",
               "subforma" : "forma",
               "subgenus" : "genus",
               "subkingdom" : "kingdom",
               "suborder" : "order",
               "subphylum" : "phylum",
               "subspecies" : "species",
               "subtribe" : "tribe",
               "subvarietas" : "varietas",
               "superclass" : "subphylum",
               "superfamily" : "order",
               "superkingdom" : "no rank",
               "superorder" : "class",
               "superphylum" : "class",
               "supertribe" : "subfamily",
               "tribe" : "supertribe",
               "varietas" : "subspecies"})

db.close()
