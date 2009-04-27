#!/bin/sh

USER=fdb_app
PASSWORD=$1

mysqldump FDB -R -u $USER --password=$PASSWORD --add-drop-table --no-data | sed -e 's/`root`/`fdb_app`/g' -e 's/AUTO_INCREMENT=[0-9]*\b//g' > scheme.sql
#mysqldump FDB -R -u $USER --password=$PASSWORD --add-drop-table --no-data | sed -e 's/`root`/`fdb_app`/g' -e 's/ AUTO_INCREMENT=[0-9]\+//g' > scheme.sql
