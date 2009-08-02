#!/bin/sh

USER=fdb_app
PASSWORD=$1

mysqldump FDB -R -u $USER --password=$PASSWORD --add-drop-table --no-data | sed -e 's/`root`/`fdb_app`/g' -e 's/AUTO_INCREMENT=[0-9]* //g' | sed -e 's/\/\*\![0-9]* DEFINER=\`fdb_app\`\@\`localhost\`\*\/ //g' -e '/\/\*\![0-9]* DEFINER=\`fdb_app\`\@\`localhost\` SQL SECURITY DEFINER \*\//d' > scheme.sql
