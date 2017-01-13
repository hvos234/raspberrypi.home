//#include <cstdlib>
#include <RF24/RF24.h>
#include <RF24Network/RF24Network.h>
#include <iostream>
#include <ctime>
#include <stdio.h>
#include <time.h>

/**
 * g++ -L/usr/lib main.cc -I/usr/include -o main -lrrd
 **/
using namespace std;

// CE Pin, CSN Pin, SPI Speed

// Setup for GPIO 22 CE and GPIO 25 CSN with SPI Speed @ 1Mhz
//RF24 radio(RPI_V2_GPIO_P1_22, RPI_V2_GPIO_P1_18, BCM2835_SPI_SPEED_1MHZ);

// Setup for GPIO 22 CE and CE0 CSN with SPI Speed @ 4Mhz
//RF24 radio(RPI_V2_GPIO_P1_15, BCM2835_SPI_CS0, BCM2835_SPI_SPEED_4MHZ); 

// Setup for GPIO 22 CE and CE1 CSN with SPI Speed @ 8Mhz
RF24 radio(RPI_V2_GPIO_P1_15, BCM2835_SPI_CS0, BCM2835_SPI_SPEED_8MHZ);  

RF24Network network(radio);

// Address of our node in Octal format (01,021, etc)
const uint16_t this_node = 00;

// Address of the other node
const uint16_t other_node = 04;

struct payload_t {                  // Structure of our payload
  unsigned long ms;
  unsigned long counter;
};

int main(int argc, char** argv) 
{
    
    printf("vendor/TMRh20/RF24Network_RPi/Task\n");
    
    // Refer to RF24.h or nRF24L01 DS for settings

    radio.begin();

    delay(5);
    network.begin(/*channel*/ 113, /*node address*/ this_node);
    
    radio.setPayloadSize(10);
    radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
    radio.setPALevel(RF24_PA_HIGH);
    radio.setDataRate(RF24_250KBPS);
    radio.setCRCLength(RF24_CRC_8);
    
    radio.printDetails();

    network.update();
    
    char payload[10] = "";
    sprintf(payload,"%s", "ac:4");

    printf("Sending ..\n");
    printf(payload);
    printf("\n");
    
    RF24NetworkHeader header(/*to node*/ other_node);
    bool ok = network.write(header, &payload, sizeof(payload));
    if (ok){
        printf("ok.\n");
    }else{ 
        printf("failed.\n");
        return 1;
    }

    return 0;
}

