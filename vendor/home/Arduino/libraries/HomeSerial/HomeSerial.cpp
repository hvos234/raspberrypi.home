/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "HomeSerial.h"

HomeSerial::HomeSerial(){}

boolean HomeSerial::getError(){
    return _error;
}

/*char *HomeSerial::getErrorMessage(){
    return _error_message;
}*/

int HomeSerial::getErrorId(){
    return _error_id;
}

void HomeSerial::resetError(){
    // reset error
    _error = false;
    //memset(&_error_message, 0, sizeof(_error_message)); // clear it
    _error_id = 0;
}

void HomeSerial::resetData(){
    _from = 0;
    _to = 0;
    _action = 0;
    memset(&_message, 0, sizeof(_message)); // clear it
}

bool HomeSerial::readSerial(){
    _char = Serial.read();
    
    if('^' == _char){
        // empty variables
        memset(&_serial, 0, sizeof(_serial)); // clear it
        _count = 0;
        _start = true;
        
    }else if ('$' == _char){
        _serial[_count] = '\0'; // needed or the program will hang;
        _count = 0;
        _start = false;
        return true;
        
    }else if(_start){
        _serial[_count] = _char;
        _count++;
        _serial[_count] = '\0';
    }
    
    return false;
}

char *HomeSerial::getSerial(){
    return _serial;
}

char *HomeSerial::readSerialBytesUntil(){
    this->resetError();
    this->resetData();
    
    // empty variables
    _num_bytes = 0;
    memset(&_bytes, 0, sizeof(_bytes)); // clear it
    
    _num_bytes = Serial.readBytesUntil('\0', _bytes, sizeof(_bytes));
    
    // reset error
    _error = true;
    //strncpy( _error_message, "Serial must start with ^ and end with $ !", sizeof(_error_message)-1 );    
    _error_id = 11;
    
    // empty variables
    _count = 0;
    _start = false;
    memset(&_serial, 0, sizeof(_serial)); // clear it
    
    for(_i = 0; _i <= _num_bytes; _i++){        
        if('^' == _bytes[_i]){
            _start = true;
            _count = 0;
            
        }else if(_start && '$' == _bytes[_i]){
           _start = false;
           _serial[_count] = '\0';  // needed or the program will hang;
           _count = 0;
           
           this->resetError();
           
       }else if(_start){
            _serial[_count] = _bytes[_i];
            _count++;
        }
    }
    
    return _serial;
}

boolean HomeSerial::sscanfSerial(char *serial){
    this->resetError();
    this->resetData();
    
    //sscanf((char *)serial, "fr:%d;to:%d;ts:%d;ac:%d;msg:%s", &_from, &_to, &_task, &_action, &_message); 
    sscanf((char *)serial, "fr:%d;to:%d;ac:%d;msg:%s", &_from, &_to, &_action, &_message); 
    
    if (0 == _from){
        _error = true;
        //strncpy( _error_message, "Serial has no fr:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 31;
        return false;
    }
    
    if (0 == _to){
        _error = true;
        //strncpy( _error_message, "Serial has no to:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 32;
        return false;
    }
    
    /*if (0 == _task){
        _error = true;
        //strncpy( _error_message, "Serial has no ts:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 33;
        return false;
    }*/
    
    if (0 == _action){
        _error = true;
        //strncpy( _error_message, "Serial has no ac:, it must be like fr:%d;to:%d;ts:%d;ac:%d;msg:%s", sizeof(_error_message)-1 );
        _error_id = 34;
        return false;
    }
    
    return true;
}

int HomeSerial::getFrom(){
    return _from;
}

int HomeSerial::getTo(){
    return _to;
}

/*int HomeSerial::getTask(){
    return _task;
}*/

int HomeSerial::getAction(){
    return _action;
}

char *HomeSerial::getMessage(){
    return _message;
}

boolean HomeSerial::writeSerial(char *serial){
    this->resetError();
    
    Serial.print('^');
    Serial.print(serial);
    Serial.println('$');
    
    _error = false;
    //memset(&_error_message, 0, sizeof(_error_message)); // clear it
    
    return true;
}