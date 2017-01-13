#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, argparse

#FR:2;TO:1;TS:0;AC:1;MSG:t:36.00,h:24.00
# add arguments for the command line partameters
parser = argparse.ArgumentParser()
#parser.add_argument('-ts', '--task', help="give the task id", dest='ts', default='123')
parser.add_argument('-fr', '--from', help="give the device id from witch it will send", dest='fr', default='1')
parser.add_argument('-to', '--to', help="give the device id witch it will send to", dest='to', default='2')
parser.add_argument('-ac', '--action', help="give the action id", dest='ac', default='3')
parser.add_argument('-msg', '--message', help="give data to send with it", dest='msg', default='None')

args = parser.parse_args()

#ts = args.ts
fr = args.fr
to = args.to
ac = args.ac
msg = args.msg

# cache variables
#ts = sys.argv[1]
#fr = sys.argv[2]
#to = sys.argv[3]
#ac = sys.argv[4]
#msg = sys.argv[5]

# logging
from home_transmitter_logging import logger
logger.info("Home Transmitter Starting !")

# imports
import time
from home_daemon import home_daemon
from home_serial import home_serial
from home_mysql import home_mysql

# daemon
_home_daemon = home_daemon()
_home_daemon.stop()

# serial
_home_serial = home_serial()
_home_serial.connect()

# mysql
_home_mysql = home_mysql()
_home_mysql.connect()

# run
logger.info("Home Daemon Receiver Running !")

#_home_serial.write("FR:" + fr + ";TO:" + to + ";TS:" + ts + ";AC:" + ac + ";MSG:" + msg)
_home_serial.write("fr:" + fr + ";to:" + to + ";ac:" + ac + ";msg:" + msg)

while True:
    string = _home_serial.read()
    # split string (FR:2;TO:1;TS:0;AC:3;MSG:t:29.00,h:34.00)
    array = string.split(';')
    # if it is the return message
    # master id is 1 and if the task is the same
    if array[3] == "1" and array[5] == ts:
        print string
        _home_daemon.start()
        sys.exit(0)
    else:
        # insert everything into the MySQL database
        query = "INSERT INTO task(from_device_id, to_device_id, action_id, data, created_at) VALUES('%s', '%s', '%s', '%s', '%s')" % (array[1], array[3], array[7], array[9], time.strftime("%Y-%m-%d %H:%M:%S"))
        _home_mysql.query(query)
