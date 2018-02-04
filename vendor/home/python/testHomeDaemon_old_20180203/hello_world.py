#!/usr/bin/python

from time import sleep

try:
    while True:
        print "Hello World"
        sleep(60)
except KeyboardInterrupt, e:
    print "Stopping..."
