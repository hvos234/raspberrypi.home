// HomeSerail
#include <HomeSerial.h>
HomeSerial homeserial;

// max serial is fr:99;to:99;ac:99;msg:t:99.99,h:99.99 is 37 plus ^$ plus \0
char serial[39];
// max message is t:99.99,h:99.99 is 15 plus \0
char message[17];

// HomeDHT
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to

#include <DHT.h> // work 1.0, 1.1
#include <HomeDHT.h>

HomeDHT homedht(DHTPIN, DHTTYPE); // work 1.0

// temperature or humdity 20.02 is 5 plus \0
char temperature[7];
char humdity[7];

// HomeRFM69
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      1
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    1000 // default is 40 ms at 4800 bits/s, now 160 ms at 1200 bits/s (160 is to low for a long distance, 510 for 10 meters)
#define TIMEOUT     3000 // wait for respones

byte sendSize=0;
boolean requestACK = false;

#include <RFM69.h>
#include <SPI.h>
#include <HomeRFM69.h>

HomeRFM69 homerfm69;

/*
to 
raspberry Pi  ->  master        fr:99;to:99;ac:99
master        ->  device        ac:99

back
device        ->  master        ac:99;msg:t:99.99,h:99.99
master        ->  raspberry Pi  fr:99;to:99;ac:99;msg:t:99.99,h:99.99 
*/

// max payload or data is ac:99;msg:t:99.99,h:99.99 is 31 plus \0
char payload[33];
char data[33];

// rest
#define SERIAL_BAUD 9600

// actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

void setup() {
  Serial.begin(SERIAL_BAUD);
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  Serial.println("Setup Finished !");
}

void loop() {
  //process any serial input
  if (Serial.available() > 0) {
    
    if(homeserial.readSerial()){
      
      memset(&serial, 0, sizeof(serial)); // clear it
      strncpy( serial, homeserial.getSerial(), sizeof(serial)-1 );
      
      Serial.print("Serial Received: ");
      Serial.println(serial);
      
      if(!homeserial.sscanfSerial(serial)){
        memset(&serial, 0, sizeof(serial)); // clear it
        sprintf(serial, "fr:%d;to:%d;ac:%d;msg:err:ser,%d", 0, 0, 0, homeserial.getErrorId());
        homeserial.writeSerial(serial);
        exit(0);
      }
      
      memset(&message, 0, sizeof(message)); // clear it
      strncpy( message, homeserial.getMessage(), sizeof(message)-1 );
          
      if(NODEID == homeserial.getFrom() && NODEID == homeserial.getTo()){ // if its for me
        memset(&message, 0, sizeof(message)); // clear it
        
        if(ACTIONTEMP != homeserial.getAction() && ACTIONHUM != homeserial.getAction() && ACTIONTEMPHUM != homeserial.getAction()){
          sprintf(message, "err:%s", "no ac");
        }
                  
        if(ACTIONTEMP == homeserial.getAction()){
          memset(&temperature, 0, sizeof(temperature)); // clear it
          strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
          if(homedht.getError()){
            sprintf(message, "err:dht,%d", homedht.getErrorId());
            
          }else {
            sprintf(message, "t:%s", temperature);
          }
        }
        
        if(ACTIONHUM == homeserial.getAction()){
          memset(&humdity, 0, sizeof(humdity)); // clear it
          strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
          
          if(homedht.getError()){
            sprintf(message, "err:dht,%d", homedht.getErrorId());
            
          }else {
            sprintf(message, "h:%s", humdity);
          }
        }
        
        if(ACTIONTEMPHUM == homeserial.getAction()){
          memset(&temperature, 0, sizeof(temperature)); // clear it
          strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
          if(homedht.getError()){
            sprintf(message, "err:dht,%d", homedht.getErrorId());
            
          }else {        
            memset(&humdity, 0, sizeof(humdity)); // clear it
            strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
            
            if(homedht.getError()){
              sprintf(message, "err:dht,%d", homedht.getErrorId());
              
            }else {
              sprintf(message, "t:%s,h:%s", temperature, humdity);
            }
          }
        }
        
        memset(&serial, 0, sizeof(serial)); // clear it
        sprintf(serial, "fr:%d;to:%d;ac:%d;msg:%s", homeserial.getTo(), homeserial.getFrom(), homeserial.getAction(), message);
        
        Serial.print("Serial write: ");
        Serial.println(serial);
        homeserial.writeSerial(serial);
      }
          
      if(NODEID == homeserial.getFrom() && 1 != homeserial.getTo()){ // if its for someone else
        memset(&message, 0, sizeof(message)); // clear it
        
        memset(&payload, 0, sizeof(payload)); // clear it
        sprintf(payload, "ac:%d;msg:%s", homeserial.getAction(), message);
        
        Serial.print("Sending:  ");
        Serial.println(payload);
        
        memset(&data, 0, sizeof(data)); // clear it
        strncpy( data, homerfm69.sendWithRetryAndreceiveWithTimeOut(homeserial.getTo(), payload, strlen(payload)+2), sizeof(data)-1 );
              
        if(homerfm69.getError()){
          sprintf(message, "err:rfm69,%d", homerfm69.getErrorId());
          
        }else {        
          Serial.print("Received:  ");
          Serial.println(data);
          
          if(!homerfm69.sscanfData(data)){
            sprintf(message, "err:rfm69,%d", homerfm69.getErrorId());
            
          }else {
            memset(&message, 0, sizeof(message)); // clear it
            sprintf(message, "%s", homerfm69.getMessage());
          }
        }
        
        memset(&serial, 0, sizeof(serial)); // clear it
        sprintf(serial, "fr:%d;to:%d;ac:%d;msg:%s", homeserial.getFrom(), homeserial.getTo(), homerfm69.getAction(), message);
        
        Serial.print("Serial write: ");
        Serial.println(serial);
        homeserial.writeSerial(serial);
        
        //delay(TIMEOUT); // or else it can occure that it will receive the message agian
      }
    }
  }
  
  //process any receiving data
  if (homerfm69.receiveDone()){ 
    memset(&message, 0, sizeof(message)); // clear it

    memset(&data, 0, sizeof(data)); // clear it
    strncpy( data, homerfm69.getData(), sizeof(data)-1 );
    
    homerfm69.sendACKRequested();
    
    Serial.print("Received done:  ");
    Serial.println(data);
    
    memset(&serial, 0, sizeof(serial)); // clear it
    if(!homerfm69.sscanfData(data)){
      sprintf(message, "err:rfm69,%d", homerfm69.getErrorId());
      
    }else {
      sprintf(message, "%s", homerfm69.getMessage());
    }
    
    sprintf(serial, "fr:%d;to:%d;ac:%d;msg:%s", homerfm69.getSenderId(), NODEID, homerfm69.getAction(), message);
    
    Serial.print("Serial write done: ");
    Serial.println(serial);
    homeserial.writeSerial(serial);
  }
}
