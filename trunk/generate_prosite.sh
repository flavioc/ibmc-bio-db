#!/bin/sh

wget -c ftp://ftp.expasy.org/databases/prosite/prosite.dat || exit 1
wget -c ftp://ftp.expasy.org/databases/prosite/prosite.doc || exit 1

prosextract -prositedir . || exit 1

rm -f prosite.dat
rm -f prosite.doc
