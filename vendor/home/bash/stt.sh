#!/bin/bash
lang="nl"
key="AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw"

path="$1"

# send flac file to google and he return the text
wget -q --post-file "${path}/vendor/home/bash/stt.flac" --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$lang&key=$key" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'