/*
  HomeDHT.h - Library for sending and receiving trough the rfm69.
  Created by , April 22, 2016.
  Released into the public domain.
*/

#ifndef HomeRFM69_h
#define HomeRFM69_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "../RFM69/RFM69.h"
#include "SPI.h"

class HomeRFM69 
{
    public:
        HomeRFM69();
        // _ack_wait default is 40 ms at 4800 bits/s, now 160 ms at 1200 bits/s
        bool initialize(uint8_t freqBand, int nodeID, int networkID, const char* key = "sampleEncryptKey", bool promiscuousMode = false, bool _ack = true, uint8_t _ack_retries = 2, unsigned long _ack_wait = 255, unsigned long _timeout = 3000);
        
        boolean getError();
        //char *getErrorMessage();
        int getErrorId();
        void resetError();
        
        
        void send(int to, char *payload, int size);
        bool sendWithAck(int to, char *payload, int size);
        bool sendWithRetry(int to, char *payload, int size);
        void resetData();
        char *receive();
        char *receiveWithTimeOut();
        char *getData();
        bool receiveDone();
        bool ACKRequested();
        void sendACK();
        bool sendACKRequested();
        
        int getSenderId();
        int getRssi();
        
        char *sendAndreceiveWithTimeOut(int to, char *payload, int size);
        char *sendWithRetryAndreceiveWithTimeOut(int to, char *payload, int size);
        
        boolean sscanfData(char *);
        //int getTask();
        int getAction();
        char *getMessage();
    
    private:
        RFM69 *_radio;

        boolean _error;
        //char _error_message[25];
        int _error_id;
        
        bool _successful;
        
        bool _ack;
        uint8_t _ack_retries;
        unsigned long _ack_wait;
        unsigned long _timeout;
        
        char _data[33]; // max payload or data is ts:99;ac:99;msg:t:99.99,h:99.99 is 31 plus \0
        byte _byte_i;
        
        //int _task;
        int _action;
        // max message is t:99.99,h:99.99 is 15 plus \0
        char _message[17];
};

#endif