#!/bin/sh

SITE=$1

if [ -z "$SITE" ]; then
	echo "usage: <full site base url>"
	exit 1
fi

get_cookie_path()
{
  echo $* | gawk -F'(http://[^/]+|?)' '$0=$2'
}

cookie_path=$(get_cookie_path $SITE)
sed -e "s@SITE_BASE@$SITE@" config/general.in > application/config/config.php
echo "var cookie_path = '$cookie_path';" > application/scripts/cookies.js

exit 0
