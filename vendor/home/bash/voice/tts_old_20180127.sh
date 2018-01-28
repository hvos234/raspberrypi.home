#!/bin/bash

#for the Raspberry Pi, we need to insert some sort of FILLER here since it cuts off the first bit of audio

string=$@
lang="nl"
if [ "$1" == "-l" ] ; then
    lang="$2"
    string=`echo "$string" | sed -r 's/^.{6}//'`
fi

#empty the original file
echo "" > "/dev/shm/speak.mp3"

len=${#string}
while [ $len -ge 100 ] ;
do
    #lets split this up so that its a maximum of 99 characters
    tmp=${string:0:100}
    string=${string:100}
    
    #now we need to make sure there aren't split words, let's find the last space and the string after it
    lastspace=${tmp##* }
    tmplen=${#lastspace}

    #here we are shortening the tmp string
    tmplen=`expr 100 - $tmplen` 
    tmp=${tmp:0:tmplen}
    
    #now we concatenate and the string is reconstructed
    string="$lastspace$string"
    len=${#string}
    
    #get the first 100 characters
    wget -q -U Mozilla -O "/dev/shm/tmp.mp3" "https://translate.google.com/translate_tts?tl=${lang}&q=$tmp&ie=UTF-8&total=1&idx=0&client=tw-ob"
    cat "/dev/shm/tmp.mp3" >> "/dev/shm/speak.mp3"
done
#this will get the last remnants
wget -q -U Mozilla -O "/dev/shm/tmp.mp3" "https://translate.google.com/translate_tts?tl=${lang}&q=$string&ie=UTF-8&total=1&idx=0&client=tw-ob"
cat "/dev/shm/tmp.mp3" >> "/dev/shm/speak.mp3"
#now we finally say the whole thing
#vlc --audio --no-random --no-loop --no-repeat --play-and-stop --play-and-exit --no-interact "/dev/shm/speak.mp3" > /dev/shm/voice.log 2>&1
#mplayer -ao pulse -srate 44100 /dev/shm/speak.mp3 > /dev/null 2>&1
omxplayer "/dev/shm/speak.mp3" - 1>>/dev/shm/voice.log 2>>/dev/shm/voice.log