#!/bin/sh

DATABASE=$1
USER=$2
PASSWORD=$3

if [ -z "$3" ]; then
	echo "usage: <database> <user> <password>"
	exit 1
fi

sed -e "s/USER_DB/$USER/" -e "s/USER_PWD/$PASSWORD/" -e "s/DATABASE/$DATABASE/" config/database.in > application/config/database.php
sed -e "s/USER_DB/$USER/" -e "s/USER_PWD/$PASSWORD/" -e "s/DATABASE/$DATABASE/" config/connection.py.in > scripts/connection.py
