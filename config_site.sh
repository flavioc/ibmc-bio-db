#!/bin/sh

SITE=$1

sed -e "s@SITE_BASE@$SITE@" config/general.in > application/config/config.php
