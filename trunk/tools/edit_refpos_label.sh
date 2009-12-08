#!/bin/sh

START=$1
LENGTH=$2

php wrapper.php command_line add_refpos_label $START $LENGTH
