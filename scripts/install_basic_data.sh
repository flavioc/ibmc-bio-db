#!/bin/sh

python users.py $*
python labels.py $*
python ranks.py $*
python types.py $*
python label_letters.py $*
