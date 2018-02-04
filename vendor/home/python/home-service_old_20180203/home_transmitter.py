#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, signal, argparse, time

#FR:2:TO:1:TS:0:AC:1:MSG:36.00;24.00
# add arguments for the command line partameters
parser = argparse.ArgumentParser()
parser.add_argument('-fr', '--from', help="give the device id from witch it will send", dest='fr', default='0')
parser.add_argument('-to', '--to', help="give the device id witch it will send to", dest='to', default='0')
parser.add_argument('-ac', '--action', help="give the action id", dest='ac', default='0')
parser.add_argument('-msg', '--message', help="give data to send with it", dest='msg', default='None')

args = parser.parse_args()

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
#from home_transmitter_logging import logger
#logger.info("Home Transmitter Starting !")
print "Home Transmitter Starting !"

# imports
import time
from home_service import home_service
from home_serial import home_serial

# daemon
print "Home Transmitter Stops Home Service !"
_home_service = home_service()
_home_service.stop()

# serial
print "Home Transmitter Serial Connect !"
_home_serial = home_serial()
_home_serial.connect(3)
time.sleep(1) # wait to device is started up

timeout = 10 # seconds
timeout_start = time.time()

# cleanup
def cleanup():
    print "Home Transmitter Cleanup !"
    global _home_serial
    del _home_serial
    sys.exit(0)

# SIGTERM handler
def signal_term_handler(signal, frame):
    #logger.info("Home Transmitter got SIGTERM !")
    print "Home Transmitter got SIGTERM !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGTERM, signal_term_handler)

# SIGINT handler
def signal_int_handler(signal, frame):
    #logger.info("Home Transmitter got SIGINT !")
    print "Home Transmitter got SIGINT !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_int_handler)

# run
#logger.info("Home Transmitter Running !")
print "Home Transmitter Running !"

print "Home Transmitter Serial Write !"
print "fr:" + fr + ";to:" + to + ";ac:" + ac
_home_serial.write("fr:" + fr + ";to:" + to + ";ac:" + ac)

while True:
    print "Home Transmitter Serial Read !"
    string = _home_serial.read()
    print string
    if "" != string:
        print "Home Transmitter Start Home Service !"
        _home_service.start()
        sys.exit(0)
    
    if time.time() > timeout_start + timeout:
        print "Home Transmitter Timeout !"
        print "^fr:" + to + ";to:" + fr + ";ac:" + ac + ";msg:err:timeout$"
        print "Home Transmitter Start Home Service !"
        _home_service.start()
        sys.exit(1)
