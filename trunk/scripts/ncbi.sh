#!/bin/sh

LOCK=ncbi.lock

function cleanup ()
{
  rm -f $LOCK
  exit 1
}

if [ -f $LOCK ]; then
  echo "Locked!"
  exit 1
fi

touch $LOCK
rm -rf taxdump || cleanup
rm -f taxdump.tar.gz || cleanup
mkdir -p taxdump || cleanup
wget -c ftp://ftp.ncbi.nih.gov/pub/taxonomy/taxdump.tar.gz || cleanup
tar zxvf taxdump.tar.gz -C taxdump || cleanup
rm -f taxdump.tar.gz || cleanup
python ncbi.py || cleanup
rm -rf taxdump || cleanup

rm -f $LOCK
exit 0
