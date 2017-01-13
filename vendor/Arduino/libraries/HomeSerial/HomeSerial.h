/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#ifndef HomeSerial_h
#define HomeSerial_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

class HomeSerial 
{
    public:
        HomeSerial();
        
        boolean getError();
        //char *getErrorMessage();
        int getErrorId();
        void resetError();
        
        void resetData();
        bool readSerial();
        char *getSerial();
        char *readSerialBytesUntil();
        boolean sscanfSerial(char *);
        boolean writeSerial(char *);
        
        int getFrom();
        int getTo();
        //int getTask();
        int getAction();
        char *getMessage();     
    
    private:
        boolean _error;
        //char _error_message[25];
        int _error_id;
        
        char _char; 
        char _bytes[39]; // max bytes or serial is fr:99;to:99;ac:99;msg:t:99.99,h:99.99 is 37 plus \0
        char _serial[39]; // max bytes or serial is fr:99;to:99;ac:99;msg:t:99.99,h:99.99 is 37 plus \0
        int _num_bytes = 0;
        int _count = 0;
        int _i = 0;
        bool _start = false;        
        
        int _from;
        int _to;
        //int _task;
        int _action;
        // max message is t:99.99,h:99.99 is 15 plus \0
        char _message[17];
};

#endif