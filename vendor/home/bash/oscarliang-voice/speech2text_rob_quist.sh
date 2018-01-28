#!/bin/bash
speech_api_lang="nl"
speech_api_key="AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw"

echo "Recording… Press Ctrl+C to Stop."
#arecord -D "plughw:1,0" -q -f cd -t wav | ffmpeg -loglevel panic -y -i – -ar 16000 -acodec flac file.flac > /dev/null 2>&1
#arecord -D "plughw:0,0" -q -f cd -t wav | ffmpeg -loglevel panic -y -i – -ar 16000 -acodec flac file.flac > /dev/null 2>&1
##arecord -D "plughw:1,0" -q -f S16_LE -r 16000 cd -t wav | ffmpeg -loglevel panic -y -i – -ar 16000 -acodec flac file.flac > /dev/null 2>&1
sox -t alsa default file.flac channels 1 rate 16000 silence 1 0.1 5% 1 1.0 5%

echo "Processing…"
#wget -q -U "Mozilla/5.0" –post-file file.flac –header "Content-Type: audio/x-flac; rate=16000" -O – "https://www.google.com/speech-api/v2/recognize?output=json&lang=$lang&key=AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw&client=Mozilla/5.0" | cut -d\" -f12 >stt.txt
#wget -O - -o /dev/null --post-file file.flac --header="Content-Type: audio/x-flac; rate=16000" "http://www.google.com/speech-api/v2/recognize?lang=en-us&key=AIzaSyCBWqW9cHNXR_01o44gC8RVAe13kkKgW9g&output=json"  | sed -e 's/[{}]/''/g'
#wget -q --post-file file.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=en_US&key=AIzaSyCBWqW9cHNXR_01o44gC8RVAe13kkKgW9g" | sed -e 's/[{}]/''/g'

#wget -q --post-file file.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$speech_api_lang&key=$speech_api_key" | sed -e 's/[{}]/''/g'
wget -q --post-file file.flac --header="Content-Type: audio/x-flac; rate=16000" -O - "https://www.google.com/speech-api/v2/recognize?client=chromium&lang=$speech_api_lang&key=$speech_api_key" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'
#curl -X POST --data-binary @file.flac --user-agent 'Mozilla/5.0' --header 'Content-Type: audio/x-flac; rate=16000;' "https://www.google.com/speech-api/v2/recognize?output=json&lang=$speech_api_lang&key=$speech_api_key&client=Mozilla/5.0" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'

#echo -n "You Said: "
#cat stt.txt
#rm file.flac > /dev/null 2>&1


#arecord -D $hardware -f S16_LE -d $duration -r 16000 | 
#flac - -f --best --sample-rate 16000 -o /dev/shm/out.flac 1>/dev/shm/voice.log 2>/dev/shm/voice.log; 
#curl -X POST --data-binary @file.flac --user-agent 'Mozilla/5.0' --header 'Content-Type: audio/x-flac; rate=16000;' "https://www.google.com/speech-api/v2/recognize?output=json&lang=$lang&key=AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw&client=Mozilla/5.0" | sed -e 's/[{}]/''/g' | awk -F":" '{print $4}' | awk -F"," '{print $1}' | tr -d '\n'

#arecord -D "plughw:0,0" -f S16_LE -d "3" -r 16000 | flac - -f --best --sample-rate 16000 -o /dev/shm/out.flac 1>/dev/shm/voice.log 2>/dev/shm/voice.log; 
#curl -X POST --data-binary @/dev/shm/out.flac --user-agent 'Mozilla/5.0' --header 'Content-Type: audio/x-flac; rate=16000;' "https://www.google.com/speech-api/v2/recognize?output=json&lang=$lang&key=AIzaSyBOti4mM-6x9WDnZIjIeyEU21OpBXqWBgw&client=Mozilla/5.0" | 
#sed -e 's/[{}]/''/g' | 
#awk -F":" '{print $4}' | 
#awk -F"," '{print $1}' | 
#tr -d '\n'