// HomeRFM69
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      3
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    3000 // this does not work // default is 40 ms at 4800 bits/s, now 160 ms at 1200 bits/s (160 is to low for a long distance, 510 for 10 meters)
#define TIMEOUT     6000 // wait for respones

byte sendSize=0;
boolean requestACK = false;

#include <RFM69.h>
#include <SPIFlash.h>
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

// Thermostat
#define THERMOPIN 3     // what pin the Thermostat switch is connected to
int thermostatStatusSwitch = 0;

// fail safe, turn the thermostate every houre of, no matter what
long thermostatFailSafePeriod = (1000L * 60 * 60); //transmit a packet to gateway so often (in ms) (every 1 hour) // the L is for warning: integer overflow in expression ,see https://github.com/arduino/Arduino/issues/3590
unsigned long thermostatFailSafeCurrentPeriod = 0;
unsigned long thermostatFailSafePreviousPeriod = 0;

// Light
#define LIGHTPIN 5 // what pin the light switch is connected to
int lightStatusSwitch = 0;

// rest
#define SERIAL_BAUD 9600

// actions
#define ACTIONTHERMOON 4 // Thermostat on
#define ACTIONTHERMOOFF 5 // Thermostat off
#define ACTIONTHERMOSTATSWITCH 6 // Thermostat status switch (if it is on or off)

#define ACTIONLIGHTON 8 // Light on
#define ACTIONLIGHTOFF 9 // Light off
#define ACTIONLIGHTSTATSWITCH 10 // Light status switch (if it is on or off)

void setup() {
  Serial.begin(SERIAL_BAUD);
  
  // HomeRFM69
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  // Thermostat
  pinMode(THERMOPIN, OUTPUT); // sets the digital pin as output, in output mode it can send voltage, in input mode only receives it
  digitalWrite(THERMOPIN, LOW); // turn thermostate off
  
  // Light
  pinMode(LIGHTPIN, OUTPUT); // sets the digital pin as output, in output mode it can send voltage, in input mode only receives it
  digitalWrite(LIGHTPIN, HIGH); // turn thermostate off // this relay the LOW and HIGH are reverst, HIGH is off
  
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
      
      if(ACTIONTHERMOON != homerfm69.getAction() && ACTIONTHERMOOFF != homerfm69.getAction() && ACTIONTHERMOSTATSWITCH != homerfm69.getAction() && ACTIONLIGHTON != homerfm69.getAction() && ACTIONLIGHTOFF != homerfm69.getAction() && ACTIONLIGHTSTATSWITCH != homerfm69.getAction()){
        sprintf(message, "err:%s", "no ac");
      }
      
      // Thermostat
      if(ACTIONTHERMOON == homerfm69.getAction()){
        Serial.println("Thermostat ON !");
        Serial.print("THERMOPIN:  ");
        Serial.println(THERMOPIN);
        
        digitalWrite(THERMOPIN, HIGH);
        sprintf(message, "on:%d", 1);
      }
      
      if(ACTIONTHERMOOFF == homerfm69.getAction()){
        Serial.println("Thermostat OFF !");
        Serial.print("THERMOPIN:  ");
        Serial.println(THERMOPIN);
        
        digitalWrite(THERMOPIN, LOW);
        sprintf(message, "off:%d", 0);
      }
      
      if(ACTIONTHERMOSTATSWITCH == homerfm69.getAction()){
        thermostatStatusSwitch = digitalRead(THERMOPIN); 
        
        if(0 == thermostatStatusSwitch){
          sprintf(message, "ss:%d", 1); // is on
        }else {
          sprintf(message, "ss:%d", 0); // is off
        }        
      }
      
      // light
      if(ACTIONLIGHTON == homerfm69.getAction()){
        digitalWrite(LIGHTPIN, LOW); // LOW and HIGH are reverts, LOW is on
        sprintf(message, "on:%d", 1);
      }
      
      if(ACTIONLIGHTOFF == homerfm69.getAction()){
        digitalWrite(LIGHTPIN, HIGH);  // LOW and HIGH are reverts, HIGH is off
        sprintf(message, "off:%d", 0);
      }
      
      if(ACTIONLIGHTSTATSWITCH == homerfm69.getAction()){
        lightStatusSwitch = digitalRead(LIGHTPIN); 
        
        if(0 == lightStatusSwitch){ // LOW and HIGH are reverts, 0 off, 1 is on
          sprintf(message, "ls:%d", 0); // is off
        }else {
          sprintf(message, "ls:%d", 1); // is on
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
  
  // fail safe, turn thermostate off every hour
  unsigned long thermostatFailSafeCurrentPeriod = millis();
  if (thermostatFailSafeCurrentPeriod - thermostatFailSafePreviousPeriod >= thermostatFailSafePeriod || thermostatFailSafeCurrentPeriod < thermostatFailSafePreviousPeriod) {
    thermostatFailSafePreviousPeriod = thermostatFailSafeCurrentPeriod;
    
    memset(&message, 0, sizeof(message)); // clear it
    
    // Thermostat
    //digitalWrite(THERMOPIN, HIGH);
    //sprintf(message, "on:%d", 1);
    
    digitalWrite(THERMOPIN, LOW);
    sprintf(message, "off:%d", 0);
    
    memset(&payload, 0, sizeof(payload)); // clear it
    sprintf(payload, "ac:%d;msg:%s", homerfm69.getAction(), message);
    
    Serial.print("Sending:  ");
    Serial.println(payload);
    
    bool success;
    success = homerfm69.sendWithRetry(1, payload, strlen(payload)+2);
    
    if(homerfm69.getError()){
      Serial.print("err:rfm69,");
      Serial.println(homerfm69.getErrorId());
    }
  }
}
