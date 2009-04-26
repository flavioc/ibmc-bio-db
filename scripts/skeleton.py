#!/usr/bin/python

import MySQLdb
import sys
from connection import *

db = create_conn()

db.close()
