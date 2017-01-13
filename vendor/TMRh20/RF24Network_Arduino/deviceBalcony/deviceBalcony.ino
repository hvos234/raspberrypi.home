#include "DHT.h"

#include <RF24Network.h>
#include <RF24.h>
#include <SPI.h>
#include <printf.h>  // Printf is used for debug

#define BAUDRATE 9600

// Uncomment whatever type you're using!
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to
#define UNIT 1      // 0 for Fahrenheit and 1 for Celsius

DHT dht(DHTPIN, DHTTYPE); // set dht

RF24 radio(7,8);                // nRF24L01(+) radio attached using Getting Started board 

RF24Network network(radio);      // Network uses that radio
const uint16_t this_node = 04;    // Address of our node in Octal format ( 04,031, etc)
const uint16_t other_node = 00;   // Address of the other node in Octal format

// declare actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

// declare variables
char payload_receive[10]; // max is 32 bytes even with enableDynamicPayloads
char payload_send[10];
char message[9];

//int task; // can not send task it gets to big
//int from;
int to;
int action;

void setup(void)
{
  Serial.begin(BAUDRATE);
  Serial.println("vendor/TMRh20/RF24Network_Arduino/deviceBalcony/");
  
  Serial.println("Printf begin.");
  printf_begin(); //Printf is used for debug
 
  Serial.println("SPI begin.");
  SPI.begin();
  
  Serial.println("Radio begin.");
  radio.begin();
  network.begin(/*channel*/ 113, /*node address*/ this_node);
  
  radio.setPayloadSize(10);
  radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
  radio.setPALevel(RF24_PA_HIGH);
  radio.setDataRate(RF24_250KBPS);
  radio.setCRCLength(RF24_CRC_8);
  
  radio.printDetails();
}

void loop(void){
  
  network.update();                  // Check the network regularly
  
  while ( network.available() ) {     // Is there anything ready for us?
    
    RF24NetworkHeader header_receive;        // If so, grab it and print it out
    
    memset(payload_receive, 0, 10); // clear it  
    network.read(header_receive, &payload_receive, 10);
     
    Serial.println("");
    Serial.print("Received: ");
    Serial.println(payload_receive);
    
    //from = 0;
    //to = 0;
    action = 0;
    sscanf((char *)payload_receive, "ac:%d", &action);
    
    if(ACTIONTEMP == action || ACTIONHUM == action || ACTIONTEMPHUM == action){
      Serial.println("");
      Serial.print("Action: ");
      Serial.println(action);
      sprintf(message, "hello wolrd !");
      
    }else {
      Serial.println("");
      Serial.println("Action does not exist !");
      sprintf(message, "err:ac no");
    }
    
    Serial.println("");
    Serial.print("Message: ");
    Serial.println(message);
    
    memset(payload_send, 0, sizeof(payload_send)); // clear it
    sprintf(payload_send, "%s\0", message);
    
    Serial.println("");
    Serial.print("Sending: ");
    Serial.println(payload_send);
    
    RF24NetworkHeader header_send(/*to node*/ other_node);
    bool ok = network.write(header_send, &payload_send, sizeof(payload_send));
    
    Serial.println("");
    if (ok){
      Serial.println("Sending ok.");
    }else{
      Serial.println("Sending failed !");
    }
  }
}

