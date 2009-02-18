#!/bin/sh

PASSWORD=$1

mysql -u fdb_app --password=$PASSWORD FDB < scheme.sql
