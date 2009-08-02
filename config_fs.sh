#!/bin/sh

LOCATION=$1
NAME=www

if [ -z "$LOCATION" ]; then
	echo "usage: <apache subdir>"
	exit 1
fi

CACHE_DIR=$PWD/system/cache
UPLOAD_DIR=$PWD/uploads

rm -rf $LOCATION

ln -sf $PWD/www $LOCATION || exit 1
ln -sf $PWD/system/application/images www/ || exit 1
ln -sf $PWD/system/application/scripts www/ || exit 1
ln -sf $PWD/system/application/styles www/ || exit 1
rm -rf $CACHE_DIR || exit 1
rm -rf $UPLOAD_DIR || exit 1
mkdir -p $CACHE_DIR || exit 1
mkdir -p $UPLOAD_DIR || exit 1
chmod -R ugo+w $CACHE_DIR || exit 1
chmod -R ugo+w $UPLOAD_DIR || exit 1

exit 0
