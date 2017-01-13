#!/usr/bin/env python
# -*- coding: utf-8 -*-

import subprocess, time

# the subprocess.call by start and stop will return failt when you try to
# start the daemon if it is already started this is also with stop, if the status
# is diffrent it will return zero if the daemon is started and not zero if the
# daemon is stopped, so will have to check wit status if the start or stop command
# was succesfull
class home_daemon:

    def start(self):
        # check if it is stopped, before
        if not self.status():
            subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'start'])
    
    def status(self):
        if not subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'status']):
            # it returns 0 if everything is oke, it is running
            return True
        else:
            return False

    def stop(self):
        # check if it is started, before
        if self.status():
            subprocess.call(['sudo', 'service', 'home-daemon-receiver', 'stop'])
            # check if the daemon is realy stopped, and check again after 11 seconds
            # linux give the daemon a stop command and after 10 seconds a kill command
            if self.status():
                time.sleep(11) # delays for 11 seconds
    
    def __enter__(self):
        return self
    
    def __exit__(self):
        # start the daemon no matter what
        self.start()

    def __del__(self):
        # start the daemon no matter what
        self.start()
