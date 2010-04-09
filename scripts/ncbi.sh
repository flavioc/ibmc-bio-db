#!/bin/bash

if [ -z "$1" ]; then
	echo "Database password must be given."
	exit 1
fi

function cleanup_timeout()
{
  echo "Cannot install NCBI database (server down?): please run ncbi.sh later!"
  exit 1
}

function cleanup ()
{
  echo "Failed to install the NCBI database!"
  exit 1
}

cat <<EOF
Now installing the NCBI taxonomy database.
***************************************************************
**  This will take some time (~700000 taxonomies to import)  **
***************************************************************
EOF

rm -rf taxdump || cleanup
rm -f taxdump.tar.gz || cleanup
mkdir -p taxdump || cleanup
wget --timeout=15 -c ftp://ftp.ncbi.nih.gov/pub/taxonomy/taxdump.tar.gz || cleanup_timeout
tar zxvf taxdump.tar.gz -C taxdump || cleanup
rm -f taxdump.tar.gz || cleanup
python ncbi.py $1 || cleanup
rm -rf taxdump || cleanup

exit 0
