#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, signal, argparse, time, atexit

#FR:2:TO:1:TS:0:AC:1:MSG:36.00;24.00
# add arguments for the command line partameters
parser = argparse.ArgumentParser()
parser.add_argument('-fr', '--from', help="give the device id from witch it will send", dest='fr', default='0')
parser.add_argument('-to', '--to', help="give the device id witch it will send to", dest='to', default='0')
parser.add_argument('-ac', '--action', help="give the action id", dest='ac', default='0')
parser.add_argument('-msg', '--message', help="give data to send with it", dest='msg', default='None')
parser.add_argument('-p', '--path', help="give the full path to root", dest='p', default='0')
parser.add_argument('-t', '--timeout', help="give the number of seconds, for script timeout", dest='t', default=4)
args = parser.parse_args()

fr = args.fr
to = args.to
ac = args.ac
msg = args.msg
p = args.p
t = args.t

# logging
#from home_transmitter_logging import logger
#logger.info("Home Transmitter Starting !")
print "Home Transmitter Starting !"

# imports
from home_serial import home_serial
from home_os import home_os

# declare variables
string = ""

# os, serail
print "Home Transmitter OS !"
_home_os = home_os()

# serial
print "Home Transmitter Serial Connect !"
_home_serial = home_serial()

# receiver
def receiver_start():    
    print "Home Transmitter Receiver Start!"
    if False == _home_os.start(["/usr/bin/python", p + "/vendor/home/python/home_task_receiver.py", "-p", p], True):
        print "^fr:" + to + ";to:" + fr + ";ac:" + ac + ";msg:err:reciever start$"
        sys.exit(1)

def receiver_stop():
    print "Home Transmitter Receiver Stop!"
    #if False == _home_os.stop(["/usr/bin/python", p + "/vendor/home/python/home_task_receiver.py", "-p", p]):
    #    print "^fr:" + to + ";to:" + fr + ";ac:" + ac + ";msg:err:reciever stop$"
    #    sys.exit(1)
    
# cleanup
def cleanup():
    print "Home Transmitter Cleanup !"
    global _home_serial
    del _home_serial
    receiver_start()
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

# on exit cleanup
atexit.register(cleanup)

# pid
#print _home_os.pgrep(["/usr/bin/python", "/var/www/html/home/vendor/home/python/home_task_transmitter.py", "-p", p, "-fr", fr, "-to", to, "-ac", ac])

# receiver stop
#receiver_stop()
#sys.exit(0)

# serial connect
_home_serial.connect(3)
time.sleep(1) # wait to device is started up
# wait to device is started up
timeout = int(t) # seconds
timeout_start = time.time()

# run
#logger.info("Home Transmitter Running !")
print "Home Transmitter Running !"

print "Home Transmitter Serial Write !"
print "fr:" + fr + ";to:" + to + ";ac:" + ac
_home_serial.write("fr:" + fr + ";to:" + to + ";ac:" + ac)

while True:
    print "Home Transmitter Serial Read !"
    string = _home_serial.read()
    
    if "" != string:
        #print "Home Transmitter Start Home Service !"
        #_home_service.start()
        print "Home Transmitter Stop !"
        print string
        sys.exit(0)
    
    if time.time() > timeout_start + timeout:
        print "Home Transmitter Timeout !"
        print "^fr:" + to + ";to:" + fr + ";ac:" + ac + ";msg:err:timeout$"
        #print "Home Transmitter Start Home Service !"
        #_home_service.start()
        sys.exit(1)
