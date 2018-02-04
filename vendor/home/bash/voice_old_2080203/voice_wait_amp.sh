#!/bin/bash

# google speech api
speech_api_lang="nl"
speech_api_key="AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw"

# threshold
max_amp_threshold=150 # bash only handles integers so it is 0.15 x 1000 = 150
min_amp_threshold=-700 # the minimale threshold is for short noices

#
stt=""
magic_word="\"home\"" # the variable stt has also extra qoutes \"

function record() {
    # record only when there is sound, and cuts out the silence and no gaps and no long silences 
    sox -t alsa default stt.flac channels 1 rate 16000 silence 1 0.1 5% 1 1.0 5%
    return
}

function record_threshold() {
    while true; do
        record

        # check max amp
        max_amp=$(sox -t .flac stt.flac -n stat 2>&1 | awk '/^Maximum\ amplitude/ {print int($3 * 1000)}')
        min_amp=$(sox -t .flac stt.flac -n stat 2>&1 | awk '/^Minimum\ amplitude/ {print int($3 * 1000)}')
        echo "$max_amp"
        echo "$min_amp" 

        # check if max_amp is more or equal to the threshold
        if [ $max_amp -ge $max_amp_threshold ] && [ $min_amp -ge $min_amp_threshold ]; then
            # send flac file to google and he return the text
            stt=$(wget -q --post-file stt.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$speech_api_lang&key=$speech_api_key" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n')
            echo "$stt"
            return
        fi
    done
}

while true; do
    record_threshold
    echo "$stt"
done