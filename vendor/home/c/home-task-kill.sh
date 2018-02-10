#!/bin/sh
# write a script that kills the user and then give normal_user the right to run that script
# see https://stackoverflow.com/questions/18359433/how-to-allow-a-normal-user-to-kill-a-certain-root-application-in-visudo-with-no 4
/usr/bin/pkill -TERM "home-task"