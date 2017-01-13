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

#include "HomeDHT.h"

//#include "stdlib.h" // work 1.1 (not even needed)

/*#ifndef DHT_h
    #include "../DHT/DHT.h" // work 1.0
#endif*/
//#include "../DHT/DHT.h"  // work 1.0, work 1.1 (not even needed)

HomeDHT::HomeDHT(uint8_t pin, uint8_t type, uint8_t count){ // work 1.0
    _dht = new DHT(pin, type, count); // work 1.0
}

boolean HomeDHT::getError(){
    return _error;
}

/*char *HomeDHT::getErrorMessage(){
    return _error_message;
}*/

int HomeDHT::getErrorId(){
    return _error_id;
}

void HomeDHT::resetError(){
    // reset error
    _error = false;
    //memset(&_error_message, 0, sizeof(_error_message)); // clear it
    _error_id = 0;
}

char *HomeDHT::getTemperature(int unit){
    this->resetError();
    
    // empty variables
    memset(&_temperature, 0, sizeof(_temperature));
    _t = 0;
    _tf = 0;
    
    _t = _dht->readTemperature(); // work 1.0
    _tf = _t * 1.8 +32;  //Convert from C to F
    
    if (isnan(_t)) {
      _error = true;
      //strncpy( _error_message, "Failed read DHT !", sizeof(_error_message)-1 );
      _error_id = 11;

      return (char *) "Failed read DHT !";
    }

    _error = false;
    
    if (0 == unit){  //choose the right unit F or C
      //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
      dtostrf(_tf, 2, 2, _temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }else {
      dtostrf(_t, 2, 2, _temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }

    return _temperature;
}

char *HomeDHT::getHumdity() {
    this->resetError();
    
    // empty variables
    memset(&_humidity, 0, sizeof(_humidity));
    _h = 0;

    _h = _dht->readHumidity(); // work 1.0
    
    if (isnan(_h)) {
      _error = true;
      //strncpy( _error_message, "Failed read DHT !", sizeof(_error_message)-1 );
      _error_id = 21;

      return (char *) "Failed read DHT !";
    }

    _error = false;

    //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(_h, 2, 2, _humidity); // dtostrf convert it to 2 before decimal and 2 after decimal

    return _humidity;
}