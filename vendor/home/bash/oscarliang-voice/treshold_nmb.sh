#!/bin/bash

TALKING_PERIOD=16
UP_SOUND_PERC=65
DOWN_SOUND_PERC=45
counter=0
while true; do

    echo "counter: " $counter

    if [ "$counter" -eq 0 ]; then
        nmb=$(arecord -d 1 tmp_rec.wav ; sox -t .wav tmp_rec.wav -n stat 2>&1 | grep "Maximum amplitude" | cut -d ':' -f 2)

        echo "nmb: " $nmb
    fi

    sleep 1
done