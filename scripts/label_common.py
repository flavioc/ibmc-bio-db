#!/usr/bin/python

import MySQLdb
import sys
from connection import *
from utils import *

db = create_conn()

def has_label(name):
  c = db.cursor()
  sql = "SELECT id FROM label WHERE name = \"%s\"" % (name)
  c.execute(sql)
  row = c.fetchone()
  return row is not None

def update_label(name, type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification):
  cursor = db.cursor()
  sql = "UPDATE label SET type = %s, `default` = %s, must_exist = %s, auto_on_creation = %s, auto_on_modification = %s, code = %s, valid_code = %s, deletable = %s, editable = %s, multiple = %s, action_modification = %s WHERE name = %s" \
    % (type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification, sql_to_string(name))
  cursor.execute(sql)
  db.commit()
  print("Label %s has been updated." % name)

def add_new_label(name, type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification):
  cursor = db.cursor()
  sql = "INSERT INTO label(name, type, `default`, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification) VALUES(\"%s\", %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)" \
    % (name, type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification)
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  print("Label %s added to database." % name)
  return id

def add_label(name, type, default = True, must_exist = True, auto_on_creation = True, auto_on_modification = True, code = '', valid_code = '', deletable = False, editable = False, multiple = False, action_modification = ''):
  type = sql_to_symbol(type)
  default = sql_to_bool(default)
  must_exist = sql_to_bool(must_exist)
  auto_on_creation = sql_to_bool(auto_on_creation)
  auto_on_modification = sql_to_bool(auto_on_modification)
  code = sql_to_string(code)
  valid_code = sql_to_string(valid_code)
  deletable = sql_to_bool(deletable)
  editable = sql_to_bool(editable)
  multiple = sql_to_bool(multiple)
  action_modification = sql_to_string(action_modification)
  if has_label(name):
    update_label(name, type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification)
  else:
    add_new_label(name, type, default, must_exist, auto_on_creation, auto_on_modification, code, valid_code, deletable, editable, multiple, action_modification)

