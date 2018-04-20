#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys, subprocess, os, time

# see https://docs.python.org/2.7/library/subprocess.html#module-subprocess

class home_os:
    
    def pgrep(self, args):
        pattern = " ".join(args)
        
        #args.insert(0, "pgrep");
        #print "pgrep: "
        #print pattern
        try:
            return subprocess.check_output(["sudo", "pgrep", "-f", pattern]) # -f is full path
        except subprocess.CalledProcessError, e:
            return False
    
    def start(self, args, background=False):
        args.insert(0, "sudo")
        #print "start: "
        #print args
        if False == background:
            #print "!background: "
            returncode = subprocess.call(args)
            if 0 == returncode:
                if False == self.pgrep(args):
                    return False
                else:
                    return True
            else:
                return False
        else:
            #print "background: "
            # return pid do not wait untill finished
            #devnull = open(os.devnull, 'wb') #python >= 2.4 # devnull, see https://stackoverflow.com/questions/14023566/prevent-subprocess-popen-from-displaying-output-in-python
            #pid = subprocess.Popen(args, stdout=devnull, stderr=devnull).pid
            #pid = subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE, close_fds=True).pid
            pid = subprocess.Popen(args, stdout=subprocess.PIPE, stderr=subprocess.PIPE).pid 
            #p = subprocess.Popen(args, shell=False)
            #cmd = " ".join(args)
            #print "cmd: " + cmd
            #os.system(cmd + " &")
            if False == self.pgrep(args):
                return False
            else:
                return True
    
    def stop(self, args):
        #/usr/bin/pkill -TERM "home-task"
        pattern = " ".join(args)
        #pattern = "'" + pattern + "'"
        print "stop: "
        print pattern
        if False == self.pgrep(args):
            return True
        
        #returncode = subprocess.call(["/usr/bin/pkill", "-f", "-TERM", pattern], shell=True) # -f is full path
        #returncode = subprocess.call(["sudo", "/usr/bin/pkill", "-f", "-TERM", pattern], shell=True) # -f is full path
        command = 'sudo /usr/bin/pkill -f -TERM "' + pattern + '"' # works
        #print "command: "
        #print command
        returncode = subprocess.call(command, shell=True) # works
        #returncode = subprocess.call(["sudo", "/usr/bin/pkill", "-f", "-TERM", "\"" + pattern + "\""], shell=True) # -f is full path
        
        print "returncode: "
        print returncode

        print >>sys.stderr
        if 0 > returncode:
            #time.sleep(1)
            if False == self.pgrep(args):
                return True
            else:
                return False
        else:
            return False
    
    #def __init__(self, path):
    #    self.path = path
    #    return self
    
    #def __enter__(self, path):
    #    self.path = path
    #    return self
    
    #def __exit__(self):
    #    if self.ser.isOpen():
    #        self.ser.close()
    
    #def __del__(self):
    #    if self.ser.isOpen():
    #        self.ser.close()
