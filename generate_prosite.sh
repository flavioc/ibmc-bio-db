#!/bin/sh

wget --timeout=15 -c ftp://ftp.expasy.org/databases/prosite/prosite.dat
if [ ! $? -eq 0 ]; then
  echo "Failed to retrieve the PROSITE database (server down?): please run generate_prosite.sh later!"
  exit 1
fi
wget --timeout=15 -c ftp://ftp.expasy.org/databases/prosite/prosite.doc
if [ ! $? -eq 0 ]; then
  echo "Failed to retrieve the PROSITE database (server down?): please run generate_prosite.sh later!"
  exit 1
fi

prosextract -prositedir . || exit 1

rm -f prosite.dat
rm -f prosite.doc
