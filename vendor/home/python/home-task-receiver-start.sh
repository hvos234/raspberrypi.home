#!/bin/sh
if [ "$#" -eq 0 ] ; 
then
    echo -e "No path"
    exit 1
fi

p=$1
f="$p/vendor/home/python/home_task_receiver.py -p '$p'"

#echo $f

/usr/bin/python $f > /dev/null 2> /dev/null &

#echo $(pgrep -f "$f")

if pgrep -f "$f" > /dev/null
then
    exit 0 # if exists
else
    exit 1
fi