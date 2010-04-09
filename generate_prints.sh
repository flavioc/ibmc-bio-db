#!/bin/sh

URL=ftp://bioinf.man.ac.uk/pub/prints
VERSIONFILE=newpr.lis.gz

function discover_version ()
{
  rm -f $VERSIONFILE
  wget --timeout=15 -q $URL/$VERSIONFILE
  if [ ! $? -eq 0 ]; then
    echo "Failed to retrieve PRINTS database (server down?): run generate_prints.sh later!"
    exit 1
  fi
  VERSION=`gunzip -c $VERSIONFILE | grep VERSION | awk -F' ' {'print $3'} | sed -e 's/\./_/'`
  rm -f $VERSIONFILE
}

echo "Discovering PRINTS database latest version..."
discover_version
FILE=prints$VERSION.dat
echo "Last version is $VERSION..."
wget -c $URL/$FILE.gz || exit 1
gzip -fd $FILE.gz || exit 1

printsextract $FILE || exit 1

rm -f $FILE

exit 0
