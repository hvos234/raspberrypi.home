#!/bin/bash
stty -F /dev/ttyUSB0 9600 raw -echo

while read -r line < /dev/ttyUSB0; do
    # $line is the line read, do something with it
    #echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        cmd="/usr/bin/php /var/www/html/home/yii receiver"
        cmd+=" $line"
        eval $cmd
    fi
done