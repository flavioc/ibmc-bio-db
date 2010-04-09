#!/bin/bash

rm -f scripts/connection.py
rm -f scripts/*.pyc
rm -f application/config/database.php
find . -name '.svn' | xargs rm -rf
