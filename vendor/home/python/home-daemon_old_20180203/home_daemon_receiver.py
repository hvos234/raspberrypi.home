#!/usr/bin/env python
# -*- coding: utf-8 -*-

# logging
#from home_daemon_logging import logger
#logger.info("Home Daemon Receiver Starting !")
print "Home Daemon Receiver Starting !"

# imports
import sys, signal, time, subprocess, os
from home_serial import home_serial
#from home_mysql import home_mysql

# declare variables
ser = None

string = ""
array = ""
query = ""

# serial
print "Home Daemon Receiver Serial Connect !"
_home_serial = home_serial()
_home_serial.connect(None)
time.sleep(1) # wait to device is started up

# mysql
#_home_mysql = home_mysql()
#_home_mysql.connect()

# cleanup
def cleanup():
    print "Home Daemon Receiver Cleanup !"
    #global _home_mysql
    #del _home_mysql
    global _home_serial
    del _home_serial
    sys.exit(0)

# SIGTERM handler
def signal_term_handler(signal, frame):
    #logger.info("Home Daemon Receiver got SIGTERM !")
    print "Home Daemon Receiver got SIGTERM !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGTERM, signal_term_handler)

# SIGINT handler
def signal_int_handler(signal, frame):
    #logger.info("Home Daemon Receiver got SIGINT !")
    print "Home Daemon Receiver got SIGINT !"
    cleanup()
    sys.exit(0)

signal.signal(signal.SIGINT, signal_int_handler)

# run
#logger.info("Home Daemon Receiver Running !")
print "Home Daemon Receiver Running !"

while True:
    print "Home Daemon Receiver Serial Read !"
    string = _home_serial.read();
    
    #if "" != string:
    print "/usr/bin/php /var/www/html/home/yii receiver \"" + string + "\""    
    os.system ("/usr/bin/php /var/www/html/home/yii receiver " + repr(string)) # passes the command and arguments to your system's shell. This is nice because you can actually run multiple commands at once in this manner and set up pipes and input/output redirection. 
        
