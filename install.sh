#!/bin/bash

FILE=install.config
source $FILE || exit 1

function mysql_return_val()
{
  mysql -s -u $USER --password="$PASSWORD" -e "$*" &> /dev/null
}

function mysql_return_val_db()
{
  mysql -s -u $USER --password="$PASSWORD" $DATABASE -e "$*" &> /dev/null
}

function mysql_output()
{
  str="$1"
  mysql -u $USER --password="$PASSWORD" -e "$str"
}

function can_login_db()
{
  mysql_return_val
}

function database_exists()
{
  cmd="SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$DATABASE'"
  mysql_output "$cmd" | grep SCHEMA >/dev/null
}

function database_create()
{
  cmd="CREATE DATABASE $DATABASE"
  mysql_return_val "$cmd"
}

exec_exists()
{
  which $1 >/dev/null
}

install_emboss ()
{
  prog="$1"
  exec_exists "$prog"
  if [ $? -eq 0 ]; then
    echo -n "Installing EMBOSS's $prog label... "
    run_python_script "label_$prog.py"
    echo "ok."
    return 0
  fi
  echo "Could not find EMBOSS's $prog program."
}

function install_emboss_labels()
{
  install_emboss antigenic
  install_emboss chips
  install_emboss epestfind
  install_emboss iep
  install_emboss patmatmotifs
  install_emboss pscan
  install_emboss sigcleave
}

function install_emboss_data()
{
  echo
  echo "If asked, please provide your root password to install the EMBOSS's PROSITE database."
  sudo bash generate_prosite.sh
  echo
  echo
  echo "If asked, please provide your root password to install the EMBOSS's PRINT database."
  sudo bash generate_prints.sh
  echo
  echo
}

function install_ncbi()
{
  cd scripts && bash ./ncbi.sh $PASSWORD
  if [ $? -ne 0 ]; then
    echo "Run manually: cd scripts && ./ncbi.sh DATABASE_PASSWORD"
  fi
  echo
  echo
}

function run_python_script()
{
  script="$1"
  cd scripts
  python "$script" &> /dev/null
  if [ $? -ne 0 ]; then
    echo "Failed to run python script $script!"
    exit 1
  fi
  cd ..
  return 0
}

function get_crontab()
{
  crontab -l 2>&1
}

add_to_crontab()
{
  NEWTAB="0 0 * * * /usr/bin/python $PWD/scripts/gc_subsequences.py"
  CRONTAB=`get_crontab`
  if [ $? -ne 0 ]; then
    # empty crontab
    echo "$NEWTAB" | crontab
    echo "done [init crontab]."
    return 0
  fi

  echo $CRONTAB | grep "$PWD/scripts/gc_subsequences.py" &>/dev/null
  if [ $? -eq 0 ]; then
    echo "already installed."
    return 0
  fi
  if [ -z "$CRONTAB" ]; then
    echo "$NEWTAB" | crontab
  else
    printf "$CRONTAB\n$NEWTAB\n" | crontab
  fi
  echo "done."
}

if [ -z "$SITE_URL" ]; then
  echo "No SITE_URL was defined in $FILE"
  exit 1
fi

if [ -z "$SITE_DIR" ]; then
  echo "No SITE_DIR was defined in $FILE"
  exit 1
fi

if [ -z "$DATABASE" ]; then
  echo "No DATABASE was defined in $FILE"
  exit 1
fi

if [ -z "$USER" ]; then
  echo "No USER was defined in $FILE"
  exit 1
fi

echo "What's the MySQL database password? (password is not echoed)"
echo -n "> "
read -rs PASSWORD
echo

can_login_db
if [ ! $? -eq 0 ]; then
  echo
  echo "Cannot login into database with the user $USER and the provided password"
  exit 1
fi

echo -n "Inspecting if database $DATABASE exists... "
database_exists
if [ ! $? -eq 0 ]; then
  echo "does not exist."
  echo -n "Creating database $DATABASE... "
  database_create
  if [ ! $? -eq 0 ]; then
    echo "failure to create database $DATABASE with user $USER!"
    echo "Please hand-create database $DATABASE using an user with enough privileges"
    exit 1
  fi
  echo "created!"
else
  echo "ok."
fi

echo
echo "The application installs the 'admin' user. Please specify the admin password to login. (not echoed)"
echo "The password must have at least 6 characters"
echo -n "> "
read -rs ADMIN_PASSWORD
echo

len_admin=${#ADMIN_PASSWORD}
if [ "$len_admin" -lt 6 ]; then
  echo "Admin password musth ave at least 6 characters, please re-run script"
  exit 1
fi

echo

rm -f scripts/*.pyc

sh config_site.sh "$SITE_URL" || exit 1
sh config_fs.sh "$SITE_DIR" || exit 1
sh config_db.sh "$DATABASE" "$USER" "$PASSWORD" || exit 1

echo -n "Creating database tables... "
(cd db && sh import.sh "$DATABASE" "$USER" "$PASSWORD")
if [ $? -ne 0 ]; then
  echo "failed."
  echo "Failed to create the database tables!"
  exit 1
fi
echo "done."

echo -n "Installing admin user... "
run_python_script users.py
echo " ok."

echo -n "Setting admin password... "
mysql_return_val_db "UPDATE user SET PASSWORD = '$ADMIN_PASSWORD' WHERE name = 'admin'"
if [ $? -ne 0 ]; then
  echo "Error changing admin password."
  exit 1
fi
echo " ok"

echo
echo -n "Inserting default database data... "
echo -n "labels "
run_python_script labels.py
echo -n "ranks "
run_python_script ranks.py
echo -n "types "
run_python_script types.py
echo -n "letters "
run_python_script label_letters.py
echo -n "others "
run_python_script label_others.py
echo

if [ ! -z "$CRON_JOB" ]; then
  echo -n "Installing cron job... "
  add_to_crontab
fi

if [ ! -z "$EMBOSS" ]; then
  install_emboss_labels
  install_emboss_data
fi

if [ ! -z "$NCBI" ]; then
  install_ncbi
fi

cat <<EOF

****************************
PHP + MYSQL TIPS
****************************

In some installations, PHP only allows uploads up to 2MB, if you want
to upload bigger sequence files please change upload_max_filesize and
post_max_size in php.ini and reset the HTTP server.
Note that post_max_size should be greater than upload_max_filesize.
In the [PHP] section of /etc/php.ini you can use for example:
post_max_size = 32MB
upload_max_size = 16MB

For MySQL we recommend activating the query cacher, by setting in
/etc/my.cnf the following variables in the section [mysqld]:
query_cache_type=1
query_cache_size=67108864 # 64MB

**********************
Everything's done!
To uninstall just do rm -f $SITE_DIR (it will only remove a symlink)
Point your browser to $SITE_URL
EOF
