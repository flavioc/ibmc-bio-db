#!/bin/sh

FILE=$1

if [ -z "$FILE" ]; then
  echo "usage: <labels xml file>"
  exit 1
fi

php wrapper.php command_line import_labels "$FILE"
