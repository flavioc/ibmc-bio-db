#!/bin/sh

SITE=$1

if [ -z "$SITE" ]; then
	echo "usage: <full site base url>"
	exit 1
fi

sed -e "s@SITE_BASE@$SITE@" config/general.in > application/config/config.php

exit 0
