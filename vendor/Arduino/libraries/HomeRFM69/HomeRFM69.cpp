/*
  HomeDHT.h - Library for sending and receiving trough the rfm69.
  Created by , April 22, 2016.
  Released into the public domain.
*/

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "HomeRFM69.h"

HomeRFM69::HomeRFM69(){
    _radio = new RFM69;
}

bool HomeRFM69::initialize(uint8_t freqBand, int nodeID, int networkID, const char* key, bool promiscuousMode, bool ack, uint8_t ack_retries, unsigned long ack_wait, unsigned long timeout){
    _successful = false;
    _successful = _radio->initialize(freqBand, nodeID, networkID);
    //radio->setHighPower(); //uncomment only for RFM69HW!

    // this change the bitrate from 4800 to 1200, and Frequenty level to max
    _radio->writeReg(0x03,0x68);      //RegBitrateMsb 1200 bitrate
    _radio->writeReg(0x04,0x2B);      //RegBitrateLsb 1200 bitrate
    _radio->writeReg(0x05,0x00);      //RegFdevMsb     2000 
    _radio->writeReg(0x06,0x52);      //RegFdevLsb     2000
    // low bitrate and you keep the low freq and db, the devices must be next to each other or they can not communicate with each other
    //_radio->writeReg(0x19,0x40|0x10|0x05);      //RegRxBw  DccFreq:010, RxBw_Mant:24, RxBw_Exp:5 
    //_radio->writeReg(0x18,0x00|0x00|0x01);      //RegLna  LnaZin:50ohm, LowPower:Off, CurrentGain:MAX

    _radio->encrypt(key);
    _radio->promiscuous(promiscuousMode); 
    
    _ack = ack;
    _ack_retries = ack_retries;
    _ack_wait = ack_wait;
    _timeout = timeout;
    
    return _successful;
}

boolean HomeRFM69::getError(){
    return _error;
}

/*char *HomeRFM69::getErrorMessage(){
    return _error_message;
}*/

int HomeRFM69::getErrorId(){
    return _error_id;
}

void HomeRFM69::resetError(){
    // reset error
    _error = false;
    //memset(&_error_message, 0, sizeof(_error_message)); // clear it
    _error_id = 0;
}

// if you pass a char array (char *) to a function, the sizeof is always 2, because it is a pointer
void HomeRFM69::send(int to, char *payload, int size){
    this->resetError();
    
    _radio->send(to, (const void*)(&payload), size, false);
    delay(25); // make sure payload is send
    
    _error = false;
}

// if you pass a char array (char *) to a function, the sizeof is always 2, because it is a pointer
bool HomeRFM69::sendWithAck(int to, char *payload, int size){
    this->resetError();
    
    uint32_t sentTime;
    
    _radio->send(to, (const void*)(&payload), size, true);
    delay(25); // make sure payload is send
    
    sentTime = millis();
    while (millis() - sentTime < _ack_wait)
    {
      if (_radio->ACKReceived(to))
      {
        //Serial.print(" ~ms:"); Serial.print(millis() - sentTime);
        _error = false;
        return true;
        
      }
    }
    
    _error = true;
    //strncpy( _error_message, "Error sendWithAck(), ACK not received !", sizeof(_error_message)-1 );
    _error_id = 31;
    
    return false;
}

bool HomeRFM69::sendWithRetry(int to, char *payload, int size){
    this->resetError();
    
    if(_radio->sendWithRetry(to, (const void*)(payload), size, _ack_retries, _ack_wait)){
        _error = false;        
        return true;
        
    }else {
        _error = true;
        //strncpy( _error_message, "Error sendWithRetry(), ACK not received !", sizeof(_error_message)-1 );
        _error_id = 41;
        return false;
    }
    
    //_error = false;
    //return false; 
}

void HomeRFM69::resetData(){
    _action = 0;
    memset(&_message, 0, sizeof(_message)); // clear it
}

char *HomeRFM69::receive(){
    this->resetError();
    this->resetData();
    
    // empty varibales
    memset(&_data, 0, sizeof(_data));
    
    if (_radio->receiveDone()){
        _error = false;
        sprintf(_data, "%s", this->getData());
        
        this->sendACKRequested();
        
    }else {
        _error = true;
        //strncpy( _error_message, "Error receive(), Nothing received !", sizeof(_error_message)-1 );
        _error_id = 51;
        return _data;
    }
    
    return _data;
}

char *HomeRFM69::receiveWithTimeOut(){
    this->resetError();
    this->resetData();
    
    // Wait here until we get a response, or timeout (250ms)
    unsigned long sentTime = millis();
    bool successful = true;
    
    // wait for respones
    while( ! _radio->receiveDone() && successful ) {
      if (millis() - sentTime > _timeout ) {
        successful = false;
      }
    }
    
    // empty varibales
    memset(&_data, 0, sizeof(_data));
    
    if (!successful) {
        sprintf(_data, "%s", "Timeout !");
        _error = true;
        //strncpy( _error_message, "Error receiveWithTimeOut(), Timeout !", sizeof(_error_message)-1 );
        _error_id = 61;
        
    }else{
        _error = false;
        sprintf(_data, "%s", this->getData());
        
        this->sendACKRequested();
    }
    
    return _data;
}

char *HomeRFM69::getData(){
    this->resetError();
    this->resetData();
    
    // empty varibales
    memset(&_data, 0, sizeof(_data)); // clear it
    
    for (_byte_i = 0; _byte_i < _radio->DATALEN; _byte_i++) {
      _data[_byte_i] = (char)_radio->DATA[_byte_i];
    }
    
    return _data;
}

bool HomeRFM69::receiveDone(){
    return _radio->receiveDone();
}

bool HomeRFM69::ACKRequested(){
    return _radio->ACKRequested();
}

void HomeRFM69::sendACK(){
    _radio->sendACK();
    //delay(25);
}

bool HomeRFM69::sendACKRequested(){
    if (this->ACKRequested()){
        this->sendACK();
        return true;
    }
    return false;
}

int HomeRFM69::getSenderId(){
    return _radio->SENDERID;
}

int HomeRFM69::getRssi(){
    return _radio->readRSSI();
}

char *HomeRFM69::sendAndreceiveWithTimeOut(int to, char *payload, int size){
    this->resetError();
    
    // empty varibales
    memset(&_data, 0, sizeof(_data));
    
    this->send(to, payload, size);
    if(this->getError()){
        //sprintf(_data, "Error sendAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        sprintf(_data, "err:111,%d", this->getErrorId());
        return _data;
    }
    
    sprintf(_data, "%s", this->receiveWithTimeOut());
    if(this->getError()){
        memset(&_data, 0, sizeof(_data)); // clear it
        //sprintf(_data, "Error sendAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        sprintf(_data, "err:112,%d", this->getErrorId());
    }
    
    return _data;
}
    
char *HomeRFM69::sendWithRetryAndreceiveWithTimeOut(int to, char *payload, int size){
    this->resetError();
    
    // empty varibales
    memset(&_data, 0, sizeof(_data));
    
    this->sendWithRetry(to, payload, size);
    if(this->getError()){
        //sprintf(_data, "Error sendWithRetryAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        sprintf(_data, "err:121,%d", this->getErrorId());
        return _data;
    }
    
    sprintf(_data, "%s", this->receiveWithTimeOut());
    if(this->getError()){
        memset(&_data, 0, sizeof(_data)); // clear it
        //sprintf(_data, "Error sendWithRetryAndreceiveWithTimeOut(): %s", this->getErrorMessage());
        sprintf(_data, "err:122,%d", this->getErrorId());
    }
    
    return _data;
}

boolean HomeRFM69::sscanfData(char *data){
    this->resetError();
    this->resetData();
    
    /*_from = 0;
    _to = 0;*/
    //_task = 0;
    _action = 0;
    memset(&_message, 0, sizeof(_message)); // clear it
    
    //sscanf((char *)data, "ts:%d;ac:%d;msg:%s", &_task, &_action, &_message); 
    sscanf((char *)data, "ac:%d;msg:%s", &_action, &_message); 
    
    /*if (0 == _from){
        _error = true;
        //strncpy( _error_message, "Serial has no fr:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 131;
        return false;
    }*/
    
    /*if (0 == _to){
        _error = true;
        //strncpy( _error_message, "Serial has no to:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 132;
        return false;
    }*/
    
    /*if (0 == _task){
        _error = true;
        //strncpy( _error_message, "Serial has no ts:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 133;
        return false;
     }*/
    
    if (0 == _action){
        _error = true;
        //strncpy( _error_message, "Serial has no ac:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 134;
        return false;
    }
    
    return true;
}

/*int HomeRFM69::getTask(){
    return _task;
}*/

int HomeRFM69::getAction(){
    return _action;
}

char *HomeRFM69::getMessage(){
    return _message;
}