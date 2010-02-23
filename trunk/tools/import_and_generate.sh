#!/bin/sh
# 
#

FILE=$1
KEEP_STRUCTURE=$2

if test -z "$FILE" || test -z "$KEEP_STRUCTURE"; then
  echo "usage: <dna fasta or xml file> <keep structure: yes or no>"
  exit 1
fi

php wrapper.php command_line import_sequence_file_and_generate "$FILE" "$KEEP_STRUCTURE"
