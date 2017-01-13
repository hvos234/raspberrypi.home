/*
  HomeDHT.h - Library for reading the temperature and humidity.
  Created by , April 4, 2016.
  Released into the public domain.
*/

#ifndef HomeDHT_h
#define HomeDHT_h

#if ARDUINO >= 100
 #include "Arduino.h"
#else
 #include "WProgram.h"
#endif

#include "stdlib.h"
#include "../DHT/DHT.h" // work 1.0

/*#ifndef DHT_h // work also 1.1
    #include "../DHT/DHT.h"
#endif*/

//#include "../DHT/DHT.h" // work 1.0, work 1.1 (not needed)
//class DHT;  // work also 1.0

class HomeDHT 
{
    public:
        HomeDHT(uint8_t pin, uint8_t type, uint8_t count=6); // work 1.0
        
        boolean getError();
        //char *getErrorMessage();
        int getErrorId();
        void resetError();
        
        char *getTemperature(int unit);
        char *getHumdity();
    
    private:
        DHT *_dht; // work 1.0

        boolean _error;
        //char _error_message[25];
        int _error_id;
        
        char _temperature[7]; //2 int, 2 dec, 1 point, and \0
        float _t = 0;
        float _tf = 0;
        
        char _humidity[7]; //2 int, 2 dec, 1 point, and \0
        float _h = 0;
};

#endif