#!/usr/bin/python

import MySQLdb
import sys
from connection import *
from utils import *

db = create_conn()

db.close()
