#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, serial

#from home_daemon_logging import logger
#from home_transmitter_logging import logger

class home_serial:
    ser = None
    
    def connect(self):
        self.ser = serial.Serial('/dev/ttyAMA0', 9600, timeout=None)
        self.ser.open()
        if self.ser.isOpen():
            self.ser.flushInput() #flush input buffer, discarding all its contents
            self.ser.flushOutput()#flush output buffer, aborting current output
            #and discard all that is in buffer

    def read(self):
        string = ""
        while True:
            if not self.ser.isOpen():
                self.connect()
            # check if there is something in the buffer
            #inbuff = self.ser.inWaiting()
            #if inbuff > 0:
            # catch one character rom the serial
            for character in self.ser.read():
                
                # check for the first character ^
                if character == "^":
                    string = ""
                # check for the last character $
                elif character == "$":
                    return string
                # if it is not the first or last character,
                # add it into string
                else:
                    string += str(character)
    
    def write(self, string):
        self.ser.write("^" + string + "$")
    
    def __enter__(self):
        return self
    
    def __exit__(self):
        self.ser.close()

    def __del__(self):
        self.ser.close()
