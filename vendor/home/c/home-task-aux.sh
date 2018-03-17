#!/bin/sh
output=$(ps aux | pgrep -x -f "$1")
echo $output
if [ "${#output}" -gt 0 ] ;
then 
    echo "$1 is running !"
    exit 0 # process is running
else 
    echo "$1 is not running !"
    exit 1 # process is not running
fi