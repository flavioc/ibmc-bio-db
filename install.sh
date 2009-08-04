#!/bin/sh

echo "***************************"
echo "Before proceeding please create a mysql database"
echo "***************************"

echo "What's the site base URL? (something like http://evolution.ibmc.up.pt/~flavio/bio)"
echo -n "> "
read SITE_URL

if [ -z "$SITE_URL" ]; then
	echo "Invalid URL."
	exit 1
fi

echo "What's the site location in apache's document root? Example: /home/flavio/public_html/bio"
echo "In the example, please note that this will only create a symlink bio in public_html."
echo -n "> "
read SITE_DIR

if [ -z "$SITE_DIR" ]; then
	echo "Invalid site location."
	exit 1
fi

echo "What's the database name?"
echo -n "> "
read DATABASE

if [ -z "$DATABASE" ]; then
	echo "Invalid database."
	exit 1
fi

echo "What's the database username?"
echo -n "> "
read USER

if [ -z "$USER" ]; then
	echo "Invalid username."
	exit 1
fi

echo "What's the database password? (password is not echoed)"
echo -n "> "
read -s PASSWORD

if [ -z "$PASSWORD" ]; then
	echo "Invalid password."
	exit 1
fi

echo "The application installs the 'admin' user. Please specify the admin password to login. (not echoed)"
echo -n "> "
read -s ADMIN_PASSWORD

echo

rm -f scripts/*.pyc
sh config_site.sh "$SITE_URL" || exit 1
sh config_fs.sh "$SITE_DIR" || exit 1
sh config_db.sh "$DATABASE" "$USER" "$PASSWORD" || exit 1

echo
echo
echo "Creating database tables..."
(cd db && sh import.sh "$DATABASE" "$USER" "$PASSWORD") || exit 1

echo
echo
echo "Inserting default database data..."
(cd scripts && sh run_all.sh) || exit 1

mysql -u $USER --password=$PASSWORD $DATABASE<<EOFMYSQL
UPDATE user SET password = "$ADMIN_PASSWORD"
WHERE name = 'admin'
EOFMYSQL
if [ $? -ne 0 ]; then
  echo "Error changing admin password."
  exit 1
fi

echo
echo
echo "**********************"
echo "Everything's done!"
echo "To uninstall just do rm -f $SITE_DIR (it will only remove a symlink)"
echo "Point your browser to $SITE_URL"

