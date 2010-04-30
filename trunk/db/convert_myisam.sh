#!/bin/bash

source install.config || exit 1

echo "What's the MySQL database password? (password is not echoed)"
echo -n "> "
read -rs PASSWORD
echo

mysql -u $USER --password="$PASSWORD" $DATABASE < convert_myisam.sql
