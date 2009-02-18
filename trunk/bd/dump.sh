#!/bin/sh

USER=$1
PASSWORD=$2

mysqldump FDB -u $USER --password=$PASSWORD --add-drop-table --no-data > scheme.sql
