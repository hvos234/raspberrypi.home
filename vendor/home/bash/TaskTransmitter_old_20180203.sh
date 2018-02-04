#!/bin/bash
#stty -F /dev/ttyUSB0 9600 raw -echo time 5

# https://playground.arduino.cc/Interfacing/LinuxTTY
stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts time 5
#stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts 

echo -ne "$1" > /dev/ttyUSB0

#cmd="echo -ne"
#cmd+=" $1\r"
#cmd+=" > /dev/ttyUSB0"
#eval $cmd

while read -r -t 4 line < /dev/ttyUSB0; do
    # $line is the line read, do something with it
    echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        echo $line;
        exit 0
    fi
done