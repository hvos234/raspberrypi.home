#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, serial, time, pprint

#from home_daemon_logging import logger
#from home_transmitter_logging import logger

class home_serial:
    ser = serial.Serial()
    string = ""
    
    def connect(self, seconds):
        
        # self.ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=timeout)
        #self.ser = serial.Serial('/dev/ttyUSB0', 9600, timeout=Non)
        self.ser.baudrate = 9600
        self.ser.port = '/dev/ttyUSB0'
        #self.ser.port = '/dev/tty.usbserial'
        self.ser.timeout = seconds
        #self.ser = serial.Serial()
        #self.ser.baudrate = 9600
        #self.ser.port = '/dev/ttyUSB0'
        #self.ser.open()
        if not self.ser.isOpen():
            self.open()

    
    def open(self):
        self.ser.open()
        self.ser.flushInput() #flush input buffer, discarding all its contents
        self.ser.flushOutput() #flush output buffer, aborting current output
        #and discard all that is in buffer
        time.sleep(1)
    
    def read(self):
        if not self.ser.isOpen():
            self.open()
        # check if there is something in the buffer
        #inbuff = self.ser.inWaiting()
        #if inbuff > 0:
        # catch one character rom the serial
        #character = str(self.ser.read(1))
        line = str(self.ser.readline())
        
        #line = line.replace('\\', '\\\\')
        #line = line.replace('"', '\\"')
        #line = line.replace('$', '\\$')
        #line = line.replace('`', '\\`')
        #line = line.replace('^', '\\^')
        line = line.replace('\0', '')
        line = line.replace('\r', '')
        line = line.replace('\n', '')
        
        #print "start" + line + "end"
        for character in line:
            #print character
            #    
            # check for the first character ^
            if character == "^":
                self.string = "^"
            # check for the last character $
            elif character == "$":
                self.string += character
                return self.string
            # if it is not the first or last character,
            # add it into string
            else:
                self.string += character
        
        return "";
    
    def write(self, string):
        self.ser.write(b"^" + string + "$")
    
    def __enter__(self):
        return self
    
    def __exit__(self):
        if self.ser.isOpen():
            self.ser.close()
    
    def __del__(self):
        if self.ser.isOpen():
            self.ser.close()
