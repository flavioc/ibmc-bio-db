#!/bin/sh

FILEDNA=$1
FILEPROTEIN=$2

if [ -z "$FILEPROTEIN" ]; then
  echo "usage: <dna fasta or xml file> <protein fasta or xml file>"
  exit 1
fi

php wrapper.php command_line import_and_link "$FILEDNA" "$FILEPROTEIN"
