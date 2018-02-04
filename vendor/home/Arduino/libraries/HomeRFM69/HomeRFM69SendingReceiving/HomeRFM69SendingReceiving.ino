#define SERIAL_BAUD 9600

#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define NODEID      1
#define NETWORKID   100
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define PROMISCUOUSMODE  false //set to 'true' to sniff all packets on the same network
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    30
#define TIMEOUT     6000 // wait for respones

#include <RFM69.h>
#include <SPI.h>
#include <HomeRFM69.h>

HomeRFM69 homerfm69;

void setup() {
  Serial.begin(SERIAL_BAUD);
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  Serial.println("Setup Finished !");
}

void loop() {
  Serial.println("Sending without ACK ..");
  
  char payload[27];
  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
    
}
