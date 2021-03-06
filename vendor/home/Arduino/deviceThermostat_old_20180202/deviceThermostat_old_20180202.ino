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
#define NODEID      3
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    3000 // default is 40 ms at 4800 bits/s, now 160 ms at 1200 bits/s (160 is to low for a long distance, 510 for 10 meters)
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
#define THERMOSTATPIN A5 // what pin the Thermostat status is connected to
int thermostatStatusSwitch = 0;

long thermostatSensorPeriod = 10000; // check Thermostat status every 10 seconds
unsigned long thermostatSensorCurrentPeriod = 0;
unsigned long thermostatSensorPreviousPeriod = 0;

int thermoSensor = 0;
int thermoSensorSum = 0;
int thermoSensorAvarage = 0;
int thermoSensorAvaragePrevious = 0;

// current sensor high side http://henrysbench.capnfatz.com/henrys-bench/arduino-current-measurements/arduino-max471-current-sensor-module-tutorial/
float thermoSensorCurrent = 0.00;
float thermoSensorCurrentPrevious = 0.00;

// voltage sensor http://henrysbench.capnfatz.com/henrys-bench/arduino-voltage-measurements/arduino-25v-voltage-sensor-module-user-manual/
float thermoSensorVoltageOut = 0.00;
float thermoSensorVoltageIn = 0.00;
float thermoSensorVoltageR1 = 30000.0; //  
float thermoSensorVoltageR2 = 7500.0; // 

int thermostatStatus = 0;
int thermostatStatusPrevious = 0;

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
//#define ACTIONTEMP 1 // send temperature
//#define ACTIONHUM 2 // send humidity
//#define ACTIONTEMPHUM 3 // send temperature and humidity

#define ACTIONTHERMOON 4 // Thermostat on
#define ACTIONTHERMOOFF 5 // Thermostat off
#define ACTIONTHERMOSTATSWITCH 6 // Thermostat status switch (if it is on or off)
#define ACTIONTHERMOSTAT 7 // Thermostat status (if it is on or off)

#define ACTIONLIGHTON 8 // Light on
#define ACTIONLIGHTOFF 9 // Light off
#define ACTIONLIGHTSTATSWITCH 10 // Light status switch (if it is on or off)

/*long transPeriod = random(3600000, 3900000); //transmit a packet to gateway so often (in ms) (between 1 houre and 1 houre and 5 minutes)
unsigned long currentPeriod = 0;
unsigned long previousPeriod = 0;*/

void setup() {
  Serial.begin(SERIAL_BAUD);
  
  // HomeRFM69
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  // Thermostat
  pinMode(THERMOPIN, OUTPUT); // sets the digital pin as output, in output mode it can send voltage, in input mode only receives it
  digitalWrite(THERMOPIN, LOW); // turn thermostate off
  
  pinMode(THERMOSTATPIN, INPUT); // sets the analog pin as input, it only have to receive it
  
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
      Serial.print("sscanfData failt: ");
      Serial.println(homerfm69.getAction());
    }else {
      
      if(ACTIONTHERMOON != homerfm69.getAction() && ACTIONTHERMOOFF != homerfm69.getAction() && ACTIONTHERMOSTATSWITCH != homerfm69.getAction() && ACTIONTHERMOSTAT != homerfm69.getAction() && ACTIONLIGHTON != homerfm69.getAction() && ACTIONLIGHTOFF != homerfm69.getAction() && ACTIONLIGHTSTATSWITCH != homerfm69.getAction()){
        sprintf(message, "err:%s", "no ac");
      }
      
      // Temperature and humidity
      /*if(ACTIONTEMP == homerfm69.getAction()){
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
      }*/
      
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
      
      if(ACTIONTHERMOSTAT == homerfm69.getAction()){
        thermostatStatus = digitalRead(THERMOSTATPIN); 
        
        if(3 <= thermostatStatus){
          sprintf(message, "s:%d", 1); // is on
        }else {
          sprintf(message, "s:%d", 0); // is off
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
  
  // Thermostate status
  // check every 10 seconds
  unsigned long thermostatSensorCurrentPeriod = millis();
  if (thermostatSensorCurrentPeriod - thermostatSensorPreviousPeriod >= thermostatSensorPeriod || thermostatSensorCurrentPeriod < thermostatSensorPreviousPeriod) {
    thermostatSensorPreviousPeriod = thermostatSensorCurrentPeriod;
  
    //Serial.println("Thermostate Sensor");
    
    thermoSensorSum = 0;
    thermoSensor = analogRead(THERMOSTATPIN);
    
    thermoSensorAvarage = 0;
    thermoSensorAvarage = thermoSensor;
    
    thermoSensorSum = 0;
    /*for (int i=0; i <= 10; i++){
      thermoSensor = 0;
      thermoSensor = analogRead(THERMOSTATPIN);
      thermoSensorSum = thermoSensorSum + thermoSensor;
    }
    thermoSensorAvarage = 0;
    thermoSensorAvarage = thermoSensorSum / 10;*/
    
    //Serial.print("thermoSensorAvarage: ");
    //Serial.println(thermoSensorAvarage);
    
    //thermoSensorCurrent = (thermoSensorAvarage * 5.0) / 1024.0;
    
    //Serial.print("thermoSensorCurrent: ");
    //Serial.println(thermoSensorCurrent, 3);
    
    thermoSensorVoltageOut = 0;
    thermoSensorVoltageIn = 0;
    
    thermoSensorVoltageOut = (thermoSensorAvarage * 5.0) / 1024.0; // see text
    thermoSensorVoltageIn = thermoSensorVoltageOut / (thermoSensorVoltageR2/(thermoSensorVoltageR1+thermoSensorVoltageR2));
    
    //Serial.print("thermoSensorVoltageOut: ");
    //Serial.println(thermoSensorVoltageOut, 2);
    
    //Serial.print("thermoSensorVoltageIn: ");
    //Serial.println(thermoSensorVoltageIn, 2);
    
    if( 4.00 < thermoSensorVoltageOut){
      thermostatStatus = 0; // is off, there is more than 4 volt
    }
    if( 2.00 > thermoSensorVoltageOut){
      thermostatStatus = 1; // is on, there is less than 2 volt
    }
    
    //Serial.print("thermostatStatus: ");
    //Serial.println(thermostatStatus);
    
    /*if( 7 <= (thermoSensorAvarage - thermoSensorAvaragePrevious)){ // above 3 is on
      thermostatStatus = 1; // is on
    }
    if( 7 <= (thermoSensorAvaragePrevious - thermoSensorAvarage)){ // above 3 is on
      thermostatStatus = 0; // is off
    }
    
    thermoSensorCurrentPrevious = thermoSensorCurrent;
    thermoSensorAvaragePrevious = thermoSensorAvarage;*/
    
    if(thermostatStatus != thermostatStatusPrevious){
      thermostatStatusPrevious = thermostatStatus;
      
      memset(&message, 0, sizeof(message)); // clear it
      
      if(1 == thermostatStatus){
        sprintf(message, "s:%d", 1); // is on
      }else {
        sprintf(message, "s:%d", 0); // is off
      }
      
      memset(&payload, 0, sizeof(payload)); // clear it
      sprintf(payload, "ac:%d;msg:%s", ACTIONTHERMOSTAT, message);
      
      Serial.print("Sending:  ");
      Serial.println(payload);
      
      bool success;
      success = homerfm69.sendWithRetry(1, payload, strlen(payload)+2);
      delay(1000);
      
      if(homerfm69.getError()){
        Serial.print("err:rfm69,");
        Serial.println(homerfm69.getErrorId());
      }
    }
  }
  
  // fail safe, turn thermostate off every hour
  unsigned long thermostatFailSafeCurrentPeriod = millis();
  if (thermostatFailSafeCurrentPeriod - thermostatFailSafePreviousPeriod >= thermostatFailSafePeriod || thermostatFailSafeCurrentPeriod < thermostatFailSafePreviousPeriod) {
    thermostatFailSafePreviousPeriod = thermostatFailSafeCurrentPeriod;
    
    memset(&message, 0, sizeof(message)); // clear it
    
    // Thermostat
    digitalWrite(THERMOPIN, HIGH);
    sprintf(message, "on:%d", 1);
    
    //digitalWrite(THERMOPIN, LOW);
    //sprintf(message, "off:%d", 0);
    
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
  
  /*unsigned long currentPeriod = millis();
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
  }*/
}
