#!/bin/bash

rm -f scripts/connection.py
rm -f scripts/*.pyc
rm -f application/config/database.php
rm -rf uploads/*
rm -rf system/cache/*
find . -name '.svn' | xargs rm -rf
