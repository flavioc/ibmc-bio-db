#!/bin/sh

FILE=prints39_0.dat
wget -c ftp://bioinf.man.ac.uk/pub/prints/$FILE.gz || exit 1
gzip -d $FILE.gz || exit 1
printsextract $FILE
rm -f $FILE || exit 1
