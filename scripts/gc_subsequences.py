#!/usr/bin/python

import MySQLdb
import sys
from connection import *
from utils import *

db = create_conn()

def drop_sequence(id):
  c = db.cursor()
  sql = "DELETE FROM sequence WHERE id = " + str(id)
  c.execute(sql)
  db.commit()

c = db.cursor()
sql = "SELECT seq_id \
    FROM label_sequence_info \
    WHERE name = 'lifetime' AND \
          date_data IS NOT NULL AND \
          NOW() > date_data"
c.execute(sql)
rows = c.fetchall()

for row in rows:
  seq_id = row[0]
  print "Deleting sequence", seq_id
  drop_sequence(seq_id)

db.close()
