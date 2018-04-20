#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, signal, os, argparse, time

# add arguments for the command line partameters
parser = argparse.ArgumentParser()
parser.add_argument('-p', '--path', help="give the full path to root", dest='p', default='0')

args = parser.parse_args()

p = args.p

# logging
#from home_daemon_logging import logger
#logger.info("Home Receiver Starting !")
print "Home Receiver Starting !"

# imports
from home_serial import home_serial

# declare variables
string = ""

# serial
print "Home Receiver Serial Connect !"
_home_serial = home_serial()
_home_serial.connect(None)
time.sleep(1) # wait to device is started up

# cleanup
def cleanup():
    print "Home Receiver Cleanup !"
    global _home_serial
    del _home_serial
    sys.exit(0)

# SIGTERM handler
def signal_term_handler(signal, frame):
    #logger.info("Home Receiver got SIGTERM !")
    print "Home Receiver got SIGTERM !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGTERM, signal_term_handler)

# SIGINT handler
def signal_int_handler(signal, frame):
    #logger.info("Home Daemon Receiver got SIGINT !")
    print "Home Receiver got SIGINT !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_int_handler)

# run
#logger.info("Home Receiver Running !")
print "Home Receiver Running !"

while True:
    print "Home Receiver Serial Read !"
    string = _home_serial.read();
    
    #if "" != string:
    #print "/usr/bin/php /var/www/html/home/yii receiver \"" + string + "\""
    print "/usr/bin/php " + p + "/yii receiver \"" + string + "\""
    #os.system ("/usr/bin/php /var/www/html/home/yii receiver " + repr(string)) # passes the command and arguments to your system's shell. This is nice because you can actually run multiple commands at once in this manner and set up pipes and input/output redirection.
    os.system ("/usr/bin/php " + p + "/yii receiver " + repr(string)) # passes the command and arguments to your system's shell. This is nice because you can actually run multiple commands at once in this manner and set up pipes and input/output redirection. 
        
