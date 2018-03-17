#!/bin/bash
threshold_max=150 # bash only handles integers so it is 0.15 x 1000 = 150
threshold_min=-700 # the minimale threshold is for short noices

magic_word="\"hallo\"" # the variable stt has also extra qoutes \"

# argument
if [ $# -ne 1 ]; then
    echo $0: usage: Voice.sh path
    exit 1
fi

path="$1"

function voice {
    path="$1"

    stt=""
    cmd="sox -v 3.5 -t alsa default ${path}/vendor/home/bash/stt.flac channels 1 rate 16000"

    # with trim, it is the duration of the recording
    if [ "$2" == "-trim" ] ; then
        cmd+=" trim $3"
    fi

    # record only when there is sound, and cuts out the silence and no gaps and no long silences
    cmd+=" silence 1 0.1 5% 1 1.0 5%"
    #cmd+=" silence 1 0 1% 1 1.0 5%"
    # https://aws.amazon.com/blogs/machine-learning/build-a-voice-kit-with-amazon-lex-and-a-raspberry-pi/
    # silence 1 0 1% 4 0.3t 2% 
    
    eval $cmd

    # get the statistics of the recording
    ##_stat=$(sox -t .flac $path/vendor/home/bash/stt.flac -n stat 2>&1)
    cmd="sox -t .flac ${path}/vendor/home/bash/stt.flac -n stat 2>&1"
    _stat=$(eval $cmd)
    
    # get the max amp en min amp from the stat
    max_amp=$(echo "$_stat" | awk '/^Maximum\ amplitude/ {print int($3 * 1000)}')
    min_amp=$(echo "$_stat" | awk '/^Minimum\ amplitude/ {print int($3 * 1000)}')

    # check if max_amp is more or equal to the threshold
    if [ "$max_amp" -ge "$threshold_max" ] && [ "$min_amp" -ge "$threshold_min" ]; then
        cmd="${path}/vendor/home/bash/stt.sh \"${path}\""     
        stt=$(eval $cmd)
        return
    fi
}



#tts=$(/usr/bin/php /var/www/html/home/yii voice "woonkamer")
#echo "wat"
#echo "$tts"

while true; do
    voice "$path"
    
    if [ "$stt" = "$magic_word" ]; then
        echo "Hello !"

        cmd="/usr/bin/php ${path}/yii notice \"Hello !\""
        $(eval $cmd)
        
        omxplayer "${path}/vendor/home/bash/hello.mp3"

        # wait for command
        voice "${path}" "-trim" "0 10"
        
        if [ ! -z "$stt" ]; then
            echo "You said: ${stt}"
            
            # the bash script stt.sh returns a string with double quotes in them,
            # with ${stt//\"} it removes the double qoutes,
            # see: https://stackoverflow.com/questions/9733338/shell-script-remove-first-and-last-quote-from-a-variable, 21
            # we only want to remove the double qoutes if we add "Je zei"
            cmd="/usr/bin/php ${path}/yii notice \"Je zei: ${stt//\"}\""
            $(eval $cmd)
            
            # we only want to remove the double qoutes if we add "Je zei"
            cmd="$path/vendor/home/bash/tts.sh ${path} \"Je zei: ${stt//\"}\""
            $(eval $cmd)
            
            cmd="/usr/bin/php ${path}/yii voice ${stt}"
            tts=$(eval $cmd)
            
            echo "Tell: ${tts}"
            
            # opposite of stt is tts that has no double quotes when it returns
            cmd="/usr/bin/php ${path}/yii notice \"${tts}\""
            $(eval $cmd)
            
            cmd="${path}/vendor/home/bash/tts.sh ${path} \"${tts}\""
            $(eval $cmd)
            
        else
            echo "Nothing !"
            cmd="/usr/bin/php ${path}/yii notice \"Nothing !\""
            $(eval $cmd)
            
            omxplayer "${path}/vendor/home/bash/nothing.mp3"
        fi
    else
        echo "What ? ${stt}"

        cmd="/usr/bin/php ${path}/yii notice \"What ? ${stt//\"}\""
        $(eval $cmd)
    fi
done