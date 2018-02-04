#!/usr/bin/env python
# -*- coding: utf-8 -*-

# logging
from home_daemon_logging import logger
logger.info("Home Daemon Receiver Starting !")

# imports
import sys, signal, time
from home_serial import home_serial
from home_mysql import home_mysql

# declare variables
ser = None

string = ""
array = ""
query = ""

# serial
_home_serial = home_serial()
_home_serial.connect()

# mysql
_home_mysql = home_mysql()
_home_mysql.connect()

# cleanup
def cleanup():
    global _home_mysql
    del _home_mysql
    global _home_serial
    del _home_serial
    sys.exit(0)

# SIGTERM handler
def signal_term_handler(signal, frame):
    logger.info("Home Daemon Receiver got SIGTERM !")
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGTERM, signal_term_handler)

# SIGINT handler
def signal_int_handler(signal, frame):
    logger.info("Home Daemon Receiver got SIGINT !")
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_int_handler)

# run
logger.info("Home Daemon Receiver Running !")

while True:
    string = _home_serial.read()
    # split string (FR:2:TO:1:TS:0:AC:3:MSG:29.00;34.00)
    array = string.split(':')
    # insert everything into the MySQL database
    query = "INSERT INTO task(from_device_id, to_device_id, action_id, data, created_at) VALUES('%s', '%s', '%s', '%s', '%s')" % (array[1], array[3], array[7], array[9], time.strftime("%Y-%m-%d %H:%M:%S"))
    _home_mysql.query(query)
