#!/bin/sh

LOCATION=$1
NAME=www

function has_permission()
{
  touch $LOCATION 2> /dev/null
}

if [ -z "$LOCATION" ]; then
	echo "usage: <apache subdir>"
	exit 1
fi

CACHE_DIR=$PWD/system/cache
UPLOAD_DIR=$PWD/uploads

echo "Linking application into apache directory $LOCATION (from $PWD/$NAME)"
has_permission
if [ ! $? -eq 0 ]; then
  echo "Not enough permissions on SITE_DIR=$LOCATION"
  exit 1
fi

rm -rf $LOCATION

mkdir -p $(dirname $LOCATION) || exit 1
ln -sf $PWD/www $LOCATION || exit 1
ln -sf $PWD/system/application/images $NAME/ || exit 1
ln -sf $PWD/system/application/scripts $NAME/ || exit 1
ln -sf $PWD/system/application/styles $NAME/ || exit 1
ln -sf $PWD/system/application/manual $NAME/ || exit 1
rm -rf $CACHE_DIR || exit 1
rm -rf $UPLOAD_DIR || exit 1
mkdir -p $CACHE_DIR || exit 1
mkdir -p $UPLOAD_DIR || exit 1
chmod -R ugo+w $CACHE_DIR || exit 1
chmod -R ugo+w $UPLOAD_DIR || exit 1

exit 0
