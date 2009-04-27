#!/bin/sh

USER=$1
PASSWORD=$2

mysqldump FDB -R -u $USER --password=$PASSWORD --add-drop-table --no-data | sed 's/`root`/`fdb_app`/g' | sed 's/ AUTO_INCREMENT=[0-9]\+//' > scheme.sql
