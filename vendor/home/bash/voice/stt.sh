#!/bin/bash
lang="nl"
key="AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw"

# send flac file to google and he return the text
wget -q --post-file stt.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$lang&key=$key" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'