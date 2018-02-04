#!/bin/bash
stty -F /dev/ttyUSB0 9600 raw -echo
echo -ne "^fr:1;to:4;ac:3$\r" > /dev/ttyUSB0

while read -r line < /dev/ttyUSB0; do
    # $line is the line read, do something with it
    #echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        echo $line;
    fi
done