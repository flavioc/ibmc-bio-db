#!/bin/sh

USERNAME=fdb_app
PASSWORD=$1
DATABASE=FDB
HOSTNAME=localhost

mysql -h $HOSTNAME -u $USERNAME -D $DATABASE --password=$PASSWORD -e "INSERT INTO user(name, complete_name, password, email, user_type, birthday) VALUES('flavio', 'Flavio Cruz', 'ibmc123', 'flaviocruz@gmail.com', 'admin', '11-12-1986')"
mysql -h $HOSTNAME -u $USERNAME -D $DATABASE --password=$PASSWORD -e "INSERT INTO user(name, complete_name, password, email, user_type) VALUES('nf', 'Nuno fonseca', 'ibmc123', 'nf@gmail.com', 'admin')"
mysql -h $HOSTNAME -u $USERNAME -D $DATABASE --password=$PASSWORD -e "INSERT INTO user(name, complete_name, password, email, user_type) VALUES('jbvieira', 'Jorge B. Vieira', 'ibmc123', 'jbvieira@ibmc.up.pt', 'admin')"

