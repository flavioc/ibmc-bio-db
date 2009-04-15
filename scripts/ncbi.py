#!/usr/bin/python

import MySQLdb
from connection import *

just_add = True
db = create_conn()

######## NAME TYPES

types = {}

def get_type_id(type_name):
  try:
    id = types[type_name]
    return id
  except KeyError:
    return False

def type_db_id(type_name):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy_name_type WHERE name = \"" + type_name + "\""
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return False
  return row[0]

def add_type(type_name):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy_name_type (name) VALUES(\"" + type_name + "\")"
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  add_type_local(type_name, id)
  return id

def add_type_local(type_name, id):
  types[type_name] = id
  return id

def ensure_type(type_name):
  id = get_type_id(type_name)
  if id is False:
    id = type_db_id(type_name)
    if id is False:
      return add_type(type_name)
    return add_type_local(type_name, id)
  else:
    return id

#### RANKS #########

ranks = {}

def get_rank_id(rank_name):
  try:
    id = ranks[rank_name]
    return id
  except KeyError:
    return False

def rank_db_id(rank_name):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy_rank WHERE name = \"" + rank_name + "\""
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return False
  return row[0]

def add_rank(rank_name):
  cursor = db.cursor()
  sql = "INSERT INTO `taxonomy_rank` (name) VALUES(\"" + rank_name + "\")"
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  add_rank_local(rank_name, id)
  return id

def add_rank_local(rank_name, id):
  ranks[rank_name] = id
  return id

def ensure_rank(rank_name):
  id = get_rank_id(rank_name)
  if id is False:
    id = rank_db_id(rank_name)
    if id is False:
      return add_rank(rank_name)
    return add_rank_local(rank_name, id)
  else:
    return id

######## TREES

def get_tree_id(name):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy_tree WHERE name = \"" + name + "\""
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return False
  return row[0]

def add_tree(name):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy_tree (name) VALUES(\"" + name + "\")"
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  return id

tree_name = "NCBI"
tree_id = get_tree_id(tree_name)
if tree_id is False:
  tree_id = add_tree(tree_name)

##### FILES

class TaxFile:
  def __init__(self, filename):
    self.fp = open(filename, 'r')
    self.current = None

  def peek(self):
    if self.current is None:
      self.__get_next()
    return self.current

  def advance(self):
    self.current = None

  def __get_next(self):
    line = self.fp.readline()
    if not line:
      return None
    splitted = line.split("|")
    ret = []
    for part in splitted:
      ret.append(part.strip())
    self.current = ret

  def get_next(self):
    if self.current is None:
      self.__get_next()
    ret = self.current
    self.current = None
    return ret

class TaxNames:
  def __init__(self, filename):
    self.taxfile = TaxFile(filename)

  def __is_id(self, vec, id):
    id_str = vec[0]
    id_int = int(id_str)
    return id_int == id

  def fetch_id(self, id):
    fetched = []
    while True:
      peeked = self.taxfile.peek()
      if peeked is None:
        return None
      if self.__is_id(peeked, id):
        break
      self.taxfile.advance()
    while True:
      peeked = self.taxfile.peek()
      if peeked is None:
        return fetched
      if not self.__is_id(peeked, id):
        return fetched
      fetched.append(peeked)
      self.taxfile.advance()
    return fetched

def is_scientific_name(el):
  type = el[3]
  return type == 'scientific name'

def get_scientific_name(vec):
  for el in vec:
    if is_scientific_name(el):
      return el
  return None

def get_other_names(vec):
  ret = []
  for el in vec:
    if not is_scientific_name(el):
      ret.append(el)
  return ret

### Taxonomies

def drop_tax():
  c = db.cursor()
  sql = "DELETE FROM taxonomy"
  c.execute(sql)
  db.commit()

def get_tax(id):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy WHERE import_id = " + id
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return False
  return int(row[0])

def get_import_tax(import_id):
  c = db.cursor()
  sql = "SELECT id FROM taxonomy WHERE import_id = " + str(import_id)
  c.execute(sql)
  row = c.fetchone()
  if row is None:
    return None
  return int(row[0])

def create_tax(id, parent, rank, name):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy (name, rank_id, tree_id, import_id, import_parent_id) VALUES(\"" + MySQLdb.escape_string(name) + "\", " + str(rank) + ", " + str(tree_id) + ", " + id + ", " + parent + ")"
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  return id

def update_tax(id, parent, rank, name):
  cursor = db.cursor()
  sql = "UPDATE taxonomy SET import_parent_id = " + parent + ", rank_id = " + str(rank) + ", tree_id = " + str(tree_id) + ", name = \"" + name + "\" WHERE import_id = " + id
  cursor.execute(sql)
  db.commit()
  return id

### TAXONOMY NAMES

def drop_all_tax_names():
  c = db.cursor()
  sql = "DELETE FROM taxonomy_name"
  c.execute(sql)
  db.commit()

def get_tax_names(id):
  c = db.cursor()
  sql = "SELECT id, name, type_id FROM taxonomy_name WHERE tax_id = " + str(id)
  c.execute(sql)
  rows = c.fetchall()
  return rows

def add_tax_name(id, name, type_id):
  cursor = db.cursor()
  sql = "INSERT INTO taxonomy_name(name, tax_id, type_id) VALUES(\"" + MySQLdb.escape_string(name) + "\", " + str(id) + ", " + str(type_id) + ")"
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  return id

tax_dir = "../taxdump/"

def has_missing_name(current_names, name):
  name_cmp = name[1]
  for cur_name in current_names:
    cur_name_str = cur_name[1]
    if cur_name_str == name_cmp:
      return True
  return False

def get_missing_names(other_names, current_names):
  ret = []
  for name in other_names:
    if not has_missing_name(current_names, name):
      ret.append(name)
  return ret

def sync_names(id, other_names, current_names):
  if just_add:
    missing = other_names
  else:
    missing = get_missing_names(other_names, current_names)
  for name in missing:
    name_str = name[1]
    type_str = name[3]
    type_id = ensure_type(type_str)
    add_tax_name(id, name_str, type_id)

def sync_db():
  nodes = TaxFile(tax_dir + "nodes.dmp")
  names = TaxNames(tax_dir + "names.dmp")
  total = 0
  while nodes.peek():
    total = total + 1
    node = nodes.get_next()
    import_id = node[0]
    parent_id = node[1]
    rank = ensure_rank(node[2])
    tax_names = names.fetch_id(int(import_id))
    name_vec = get_scientific_name(tax_names)
    name = name_vec[1]
    if just_add:
      tax = create_tax(import_id, parent_id, rank, name)
    else:
      tax = get_tax(import_id)
      if not tax:
        tax = create_tax(import_id, parent_id, rank, name)
      #else:
      #  update_tax(import_id, parent_id, rank, name)
    other_names = get_other_names(tax_names)
    current_names = get_tax_names(tax)
    sync_names(tax, other_names, current_names)
    print import_id

if just_add:
  drop_all_tax_names()
  drop_tax()

sync_db()

db.close()

