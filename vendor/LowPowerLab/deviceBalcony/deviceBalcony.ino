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
#define NODEID      4
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
master        ->  raspberry Pi  ac:99;msg:t:99.99,h:99.99 
*/

// max payload or data is ac:99;msg:t:99.99,h:99.99 is 31 plus \0
char payload[33];
char data[33];

// max message is t:99.99,h:99.99 is 15 plus \0
char message[17];

// rest
#define SERIAL_BAUD 9600

// actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

long transPeriod = random(3600000, 3900000); //transmit a packet to gateway so often (in ms) (between 1 houre and 1 houre and 5 minutes)
unsigned long currentPeriod = 0;
unsigned long previousPeriod = 0;

void setup() {
  Serial.begin(SERIAL_BAUD);
  
  // HomeRFM69
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  // if analog input pin 0 is unconnected, random analog
  // noise will cause the call to randomSeed() to generate
  // different seed numbers each time the sketch runs.
  // randomSeed() will then shuffle the random function.
  randomSeed(analogRead(0));
  
  Serial.println("Setup Finished !");
}

void loop() {
  //process any receiving data
  if (homerfm69.receiveDone()){
    memset(&message, 0, sizeof(message)); // clear it
    
    memset(&data, 0, sizeof(data)); // clear it
    strncpy( data, homerfm69.getData(), sizeof(data)-1 );
    
    homerfm69.sendACKRequested();
    
    Serial.print("Received: ");
    Serial.println(data);
    
    if(!homerfm69.sscanfData(data)){
      sprintf(message, "err:rfm69,%d", homerfm69.getErrorId());
    }else {
      
      if(ACTIONTEMP != homerfm69.getAction() && ACTIONHUM != homerfm69.getAction() && ACTIONTEMPHUM != homerfm69.getAction()){
        sprintf(message, "err:%s", "no ac");
      }
      
      // Temperature and humidity
      if(ACTIONTEMP == homerfm69.getAction()){
        memset(&temperature, 0, sizeof(temperature)); // clear it
        strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
        if(homedht.getError()){
          sprintf(message, "err:dht,%d", homedht.getErrorId());
              
        }else {
          sprintf(message, "t:%s", temperature);
        }
      }
          
      if(ACTIONHUM == homerfm69.getAction()){
        memset(&humdity, 0, sizeof(humdity)); // clear it
        strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
            
        if(homedht.getError()){
          sprintf(message, "err:dht,%d", homedht.getErrorId());
        }else {
          sprintf(message, "h:%s", humdity);
        }
      }
          
      if(ACTIONTEMPHUM == homerfm69.getAction()){
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
    }
        
    memset(&payload, 0, sizeof(payload)); // clear it
    sprintf(payload, "ac:%d;msg:%s", homerfm69.getAction(), message);
    
    Serial.print("Sending:  ");
    Serial.println(payload);
    
    bool success;
    success = homerfm69.sendWithRetry(homerfm69.getSenderId(), payload, strlen(payload)+2);
    
    if(homerfm69.getError()){
      Serial.print("err:rfm69,");
      Serial.println(homerfm69.getErrorId());
    }
  }
  
  unsigned long currentPeriod = millis();
  if (currentPeriod - previousPeriod >= transPeriod || currentPeriod < previousPeriod) {
    previousPeriod = currentPeriod;
    transPeriod = random(3600000, 3900000); //transmit a packet to gateway so often (in ms) (between 1 houre and 1 houre and 5 minutes)
    
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
    
    memset(&payload, 0, sizeof(payload)); // clear it
    sprintf(payload, "ac:%d;msg:%s", 3, message);
    
    Serial.print("Sending Period:  ");
    Serial.println(payload);
    
    bool success;
    success = homerfm69.sendWithRetry(1, payload, strlen(payload)+2);
    
    if(homerfm69.getError()){
      Serial.print("err:rfm69,");
      Serial.println(homerfm69.getErrorId());
    }
  }
}
