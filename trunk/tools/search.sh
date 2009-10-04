#!/bin/sh

TERM=$1
TRANSFORM=$2

if [ -z "$TERM" ]; then
  echo "usage: <search term>"
  exit 1
fi

php wrapper.php command_line search "$TERM" "$TRANSFORM"
