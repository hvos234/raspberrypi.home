#!/bin/bash
#busybox stty -F /dev/ttyUSB0 9600 raw -echo time 5
##stty -F /dev/ttyUSB0 9600 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts -clocal -hupcl time 5
##stty -F /dev/ttyUSB0 9600 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts -clocal -hupcl
#stty -F /dev/ttyUSB0 9600 raw -echo time 5
#stty -F /dev/ttyUSB0 -hupcl
##cat < /dev/ttyUSB0 > /dev/null &

# stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts 
# sudo stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts
# sudo stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts -hupcl
# stty -F /dev/ttyUSB0 cs8 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts -hupcl
# stty -F /dev/ttyUSB0 ispeed 9600 ospeed 9600 -ignpar cs8 -cstopb -echo
# stty -F /dev/ttyUSB0 cs8 9600 ispeed 9600 ospeed 9600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts -hupcl -ignpar -cstopb
#echo -en "^fr:1;to:4;ac:3$" > /dev/ttyUSB0
echo "$1"
#trans=$1
#echo -en "$trans" > /dev/ttyUSB0

#cmd="echo -en '$1' > /dev/ttyUSB0"
#eval $cmd

#cmd="echo -en $1 > /dev/ttyUSB0"
#echo $cmd

#transmit=$1
#echo -en "$1" > /dev/ttyUSB0

#cmd="/bin/echo -en"
#cmd+=" \"$1\""
#cmd+="  > /dev/ttyUSB0"
#echo $cmd
#eval $cmd
timeout=$((SECONDS+3)) # 4 seconds, $SECONDS variable, which has a count of the time that the script (or shell) has been running for

transmit=$1
transmit="$1"
transmit=${1}
#echo -en "$1" > /dev/ttyUSB0
echo -en "^fr:1;to:4;ac:3$" > /dev/ttyUSB0

#while read -r -t 10 line < /dev/ttyUSB0; do
#while cat -t 10 /dev/ttyUSB0; do
#echo $(/usr/bin/tail -f /dev/ttyUSB0)
#read reply < /dev/ttyUSB0
#echo "$reply"

#while [ $SECONDS -lt $timeout ]; do
while true; do
#while read -r line < /dev/ttyUSB0; do
    #line=tail -f /dev/ttyUSB0
    #line=cat </dev/ttyUSB0
    #line=$(cat </dev/ttyUSB0)
    #line=$(/usr/bin/tail -f /dev/ttyUSB0)
    #cmd="/usr/bin/tail -f /dev/ttyUSB0"
    #line=$(eval $cmd)
    read line < /dev/ttyUSB0
    
    # $line is the line read, do something with it
    echo $line
    if [[ ${line:0:1} == "^" ]] ; then 
        echo $line;
        exit 0
    fi
done
exit 1