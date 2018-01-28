#!/bin/bash
threshold_max=150 # bash only handles integers so it is 0.15 x 1000 = 150
threshold_min=-700 # the minimale threshold is for short noices

magic_word="\"hallo\"" # the variable stt has also extra qoutes \"

function voice {
    stt=""
    cmd="sox -t alsa default stt.flac channels 1 rate 16000"

    # with trim, it is the duration of the recording
    if [ "$1" == "-trim" ] ; then
        cmd+=" trim $2"
    fi

    # record only when there is sound, and cuts out the silence and no gaps and no long silences
    cmd+=" silence 1 0.1 5% 1 1.0 5%"

    eval $cmd

    # get the statistics of the recording
    _stat=$(sox -t .flac stt.flac -n stat 2>&1)
    # get the max amp en min amp from the stat
    max_amp=$(echo "$_stat" | awk '/^Maximum\ amplitude/ {print int($3 * 1000)}')
    min_amp=$(echo "$_stat" | awk '/^Minimum\ amplitude/ {print int($3 * 1000)}')

    # check if max_amp is more or equal to the threshold
    if [ "$max_amp" -ge "$threshold_max" ] && [ "$min_amp" -ge "$threshold_min" ]; then
        stt=$(./stt.sh)
        return
    fi
}

#tts=$(/usr/bin/php /var/www/html/home/yii voice "woonkamer")
#echo "wat"
#echo "$tts"

while true; do
    voice
    echo "What ?"
    echo "$stt"
    
    if [ "$stt" = "$magic_word" ]; then
        echo "Hello !"
        omxplayer hello.mp3

        # wait for command
        voice "-trim" "0 10"
        
        if [ ! -z "$stt" ]; then
            echo "You said:"
            echo "$stt"
            ./tts.sh "Je zei: " + "$stt"
            
            cmd="/usr/bin/php /var/www/html/home/yii voice"
            cmd+=" $stt"
            tts=$(eval $cmd)
            
            echo "Tell: "
            echo "$tts"
            ./tts.sh "$tts"
            
        else
            echo "Nothing !"
            omxplayer nothing.mp3
        fi
    fi
done