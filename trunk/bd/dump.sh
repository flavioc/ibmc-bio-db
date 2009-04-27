#!/bin/sh

USER=$1
PASSWORD=$2

mysqldump FDB -R -u $USER --password=$PASSWORD --add-drop-table --no-data > scheme.sql
