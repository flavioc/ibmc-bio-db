#!/bin/sh

DATABASE=$1
USER=$2
PASSWORD=$3

mysql -u $USER --password=$PASSWORD $DATABASE < scheme.sql
