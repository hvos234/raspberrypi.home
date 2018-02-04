#!/bin/bash
lang="nl"
string=$@

#empty the original file
echo "" > "tts.mp3"

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
    wget -q -U Mozilla -O "tts_tmp.mp3" "https://translate.google.com/translate_tts?tl=${lang}&q=$tmp&ie=UTF-8&total=1&idx=0&client=tw-ob"
    cat "tts_tmp.mp3" >> "tts.mp3"
done
#this will get the last remnants
wget -q -U Mozilla -O "tts_tmp.mp3" "https://translate.google.com/translate_tts?tl=${lang}&q=$string&ie=UTF-8&total=1&idx=0&client=tw-ob"
cat "tts_tmp.mp3" >> "tts.mp3"

omxplayer "tts.mp3" - 1>>/dev/shm/voice.log 2>>/dev/shm/voice.log