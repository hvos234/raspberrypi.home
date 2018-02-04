#!/bin/bash
# open /dev/ttyXXX by redirecting /dev/ttyXXX to file description 3
exec 3<> /dev/ttyUSB0
# wait for Arduino's initialization
sleep 1

# communicate with Arduino
echo "$1" >&3

#cmd="echo -ne"
#cmd+=" $1\r"
#cmd+=" > /dev/ttyUSB0"
#eval $cmd

while read -r -t 4 line <&3; do
    # $line is the line read, do something with it
    echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        echo $line;
        # close /dev/ttyXXX
        exec 3>&-
        exit 0
    fi
done

# close /dev/ttyXXX
exec 3>&-
exit 1