#!/bin/sh

URL=$1
PARAM=$2

php wrapper.php command_line add_url_label "$URL" "$PARAM"
