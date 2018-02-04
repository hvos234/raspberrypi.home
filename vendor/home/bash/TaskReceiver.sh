#!/bin/bash

#Set permisions
#sudo chmod o+rwx /dev/ttyUSB0

#stty -F /dev/ttyUSB0 9600 raw -echo time 5

# https://playground.arduino.cc/Interfacing/LinuxTTY
##stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts time 5

# -t 4 is exit with status 1 if nothing read in 4 seconds
while read -r line < /dev/ttyUSB0; do
    # $line is the line read, do something with it
    echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        cmd="/usr/bin/php /var/www/html/home/yii taskreceiver"
        cmd+=" '$line'"
        eval $cmd
    fi
done