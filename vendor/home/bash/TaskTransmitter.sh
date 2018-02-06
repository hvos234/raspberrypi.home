#!/bin/bash
$(../../arduino-serial/arduino-serial -b 9600 -p /dev/ttyUSB0 -s "^fr:1;to:4;ac:3$")


#while true; do
while line=$(../../arduino-serial/arduino-serial -b 9600 /dev/ttyUSB0 -r); do
    # $line is the line read, do something with it
    echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        echo $line;
        $(../../arduino-serial/arduino-serial -b 9600 /dev/ttyUSB0 -p)
        exit 0
    fi
done
$(../../arduino-serial/arduino-serial -b 9600 /dev/ttyUSB0 -p)
exit 1