#!/bin/sh

DATABASE=FDB
USER=fdb_app
PASSWORD=$1

mysqldump $DATABASE -R -u $USER --password=$PASSWORD --add-drop-table --no-data -y | sed -e 's/`root`/`fdb_app`/g' -e 's/AUTO_INCREMENT=[0-9]* //g' | sed -e 's/\/\*\![0-9]* DEFINER=\`fdb_app\`\@\`localhost\`\*\/ //g' -e '/\/\*\![0-9]* DEFINER=\`fdb_app\`\@\`localhost\` SQL SECURITY DEFINER \*\//d' -e 's/FDB\.//g' > scheme.sql
