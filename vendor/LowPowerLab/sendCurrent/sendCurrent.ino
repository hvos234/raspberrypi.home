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

#define THERMOSTATPIN A6 // what pin the Thermostat status is connected to
int thermoSensor = 0;

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
  thermoSensor = 0;
  thermoSensor = analogRead(THERMOSTATPIN);
  Serial.print("Current = "); // shows the voltage measured
  Serial.println(thermoSensor);
  
  // send current
  sprintf(message, "current:%d", thermoSensor);
  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", message);
    
  Serial.print("Sending Period:  ");
  Serial.println(payload);
  bool success;
  success = homerfm69.sendWithRetry(1, payload, strlen(payload)+2);
  
  if(homerfm69.getError()){
    Serial.print("err:rfm69,");
    Serial.println(homerfm69.getErrorId());
  }
  
  delay(2500);
}
