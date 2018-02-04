#!/bin/bash
speech_api_lang="nl"
speech_api_key="AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw"

# record only when there is sound, and cuts out the silence and no gaps and no long silences 
sox -t alsa default stt.flac channels 1 rate 16000 silence 1 0.1 5% 1 1.0 5%

# send flac file to google and he return the text
wget -q --post-file stt.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$speech_api_lang&key=$speech_api_key" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'