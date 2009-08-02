#!/bin/sh

DATABASE=$1
PASSWORD=$2

mysql -u fdb_app --password=$PASSWORD $DATABASE < scheme.sql
