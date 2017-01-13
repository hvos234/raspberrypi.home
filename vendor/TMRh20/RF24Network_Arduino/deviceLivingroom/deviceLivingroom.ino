#include <RF24Network.h>
#include <RF24.h>
#include <SPI.h>

RF24 radio(7,8);                // nRF24L01(+) radio attached using Getting Started board 

RF24Network network(radio);      // Network uses that radio
const uint16_t this_node = 01;    // Address of our node in Octal format ( 04,031, etc)
const uint16_t other_node = 00;   // Address of the other node in Octal format

void setup(void)
{
  Serial.begin(57600);
  Serial.println("vendor/TMRh20/RF24Network_RPi/deviceLivingroom/");
 
  SPI.begin();
  radio.begin();
  network.begin(/*channel*/ 113, /*node address*/ this_node);
  radio.printDetails();
}

void loop(void){
  
  network.update();                  // Check the network regularly
  
  while ( network.available() ) {     // Is there anything ready for us?
    
    RF24NetworkHeader header;        // If so, grab it and print it out
    
    char payload[32];
    network.read(header, &payload, sizeof(payload));
    
    Serial.print("Received packet #");
    Serial.print(payload);
  }
}

