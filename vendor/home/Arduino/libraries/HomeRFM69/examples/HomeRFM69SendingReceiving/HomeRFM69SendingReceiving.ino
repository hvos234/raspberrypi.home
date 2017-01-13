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

#define TONODEID  2

void setup() {
  Serial.begin(SERIAL_BAUD);
  homerfm69.initialize(FREQUENCY, NODEID, NETWORKID, KEY, PROMISCUOUSMODE, ACK, ACK_RETRIES, ACK_WAIT, TIMEOUT);
  
  Serial.println("Setup Finished !");
}

void loop() {
  char payload[27];
  char data[27];
  
  // Sending
  Serial.println("Sending ..");
  Serial.println("Sending without ACK ..");
  
  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  homerfm69.send(TONODEID, payload);
  
  /* is not needed, there is no error at all
  if(homerfm69.getError()){
    Serial.print("Sending without ACK Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending without ACK: Oke !");
  }*/
  
  Serial.println("Sending with ACK ..");

  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  homerfm69.sendWithAck(TONODEID, payload);
  
  if(homerfm69.getError()){
    Serial.print("Sending with ACK Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending with ACK: Oke !");
  }
  
  Serial.println("Sending with retry ..");

  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  homerfm69.sendWithRetry(TONODEID, payload);
  
  if(homerfm69.getError()){
    Serial.print("Sending with retry Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending with retry: Oke !");
  }
  
  // Receiving
  Serial.println("Receiving ..");
  
  memset(&data, 0, sizeof(data)); // clear it
  strncpy( data, homerfm69.receive(), sizeof(data)-1 );
  
  if(homerfm69.getError()){
    Serial.print("Receiving Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Recieved: ");
    Serial.println(data);
  }
  
  // this is a beter option i think
  Serial.println("Receiving 2 ..");
  if (homerfm69.receiveDone()){

    memset(&data, 0, sizeof(data)); // clear it
    strncpy( data, homerfm69.getData(), sizeof(data)-1 );
    
    if(homerfm69.getError()){
      Serial.print("Receiving 2 Error:  ");
      Serial.println(homerfm69.getErrorMessage());
    }else {
      Serial.print("Recieved 2: ");
      Serial.println(data);
    }
  }
  
  // Sending and receiving
  Serial.println("Sending and receiving ..");
  Serial.println("Sending and receiving without ACK ..");

  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  homerfm69.send(TONODEID, payload);
  
  memset(&data, 0, sizeof(data)); // clear it
  strncpy( data, homerfm69.receiveWithTimeOut(), sizeof(data)-1 );
  
  if(homerfm69.getError()){
    Serial.print("Sending and receiving without ACK Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending and receiving without ACK received: ");
    Serial.println(data);
  }
  
  // Sending and receiving with timeout
  Serial.println("Sending and receiving with timeout ..");
  Serial.println("Sending and receiving with timeout without ACK ..");

  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  memset(&data, 0, sizeof(data)); // clear it
  strncpy( data, homerfm69.sendAndreceiveWithTimeOut(TONODEID, payload), sizeof(data)-1 );
  
  if(homerfm69.getError()){
    Serial.print("Sending and receiving with timeout without ACK Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending and receiving with timeout without ACK received: ");
    Serial.println(data);
  }
  
  Serial.println("Sending and receiving with timeout with ACK ..");
  memset(&payload, 0, sizeof(payload)); // clear it
  sprintf(payload, "%s", "Hello World !");
  
  memset(&data, 0, sizeof(data)); // clear it
  strncpy( data, homerfm69.sendWithRetryAndreceiveWithTimeOut(TONODEID, payload), sizeof(data)-1 );
  
  if(homerfm69.getError()){
    Serial.print("Sending and receiving with timeout with ACK Error:  ");
    Serial.println(homerfm69.getErrorMessage());
  }else {
    Serial.print("Sending and receiving with timeout with ACK received: ");
    Serial.println(data);
  }
}
