#!/usr/bin/python

import MySQLdb
import sys
from connection import *

db = create_conn()

def has_user(name):
  c = db.cursor()
  sql = "SELECT id FROM user WHERE name = \"%s\"" % (name)
  c.execute(sql)
  row = c.fetchone()
  return row is not None

def add_user(name, complete_name = None, email = 'noemail@email.com', user_type = 'admin', password = 'ibmc123'):
  if has_user(name):
    print("User %s already in the database." % name)
    return
  if complete_name is None:
    complete_name = name
  cursor = db.cursor()
  sql = "INSERT INTO user(name, complete_name, password, email, user_type) VALUES(\"%s\", \"%s\", \"%s\", \"%s\", '%s')" \
    % (name, complete_name, password, email, user_type)
  cursor.execute(sql)
  id = int(db.insert_id())
  db.commit()
  print("User %s added to database." % name)
  return id

add_user(name = 'flavio',
  complete_name = 'Flavio Cruz',
  email = 'flaviocruz@gmail.com')
add_user(name = 'nf',
    complete_name = 'Nuno Fonseca',
    email = 'nf@gmail.com')
add_user(name = 'jbvieira',
    complete_name = 'Jorge B. Vieira',
    email = 'jbvieira@ibmc.up.pt')
add_user(name = 'user1', user_type = 'user')
add_user(name = 'admin')

db.close()
