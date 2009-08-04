#!/bin/sh

FILE=$1

if [ -z "$FILE" ]; then
  echo "usage: <ranks xml file>"
  exit 1
fi

php wrapper.php command_line import_tree "$FILE"
