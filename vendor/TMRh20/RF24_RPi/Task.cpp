#include <cstdlib>
#include <iostream>
#include <sstream>
#include <string>
#include <unistd.h>
#include <RF24/RF24.h>

/**
 * g++ -L/usr/lib main.cc -I/usr/include -o main -lrrd
 **/
using namespace std;

// Radio CE Pin, CSN Pin, SPI Speed
// See http://www.airspayce.com/mikem/bcm2835/group__constants.html#ga63c029bd6500167152db4e57736d0939 and the related enumerations for pin information.

// Setup for GPIO 22 CE and CE0 CSN with SPI Speed @ 4Mhz
//RF24 radio(RPI_V2_GPIO_P1_22, BCM2835_SPI_CS0, BCM2835_SPI_SPEED_4MHZ);

// NEW: Setup for RPi B+
//RF24 radio(RPI_BPLUS_GPIO_J8_15,RPI_BPLUS_GPIO_J8_24, BCM2835_SPI_SPEED_8MHZ);

// Setup for GPIO 15 CE and CE0 CSN with SPI Speed @ 8Mhz
//RF24 radio(RPI_V2_GPIO_P1_15, RPI_V2_GPIO_P1_24, BCM2835_SPI_SPEED_8MHZ);

// RPi generic:
RF24 radio(22,0);

/*** RPi Alternate ***/
//Note: Specify SPI BUS 0 or 1 instead of CS pin number.
// See http://tmrh20.github.io/RF24/RPi.html for more information on usage

//RPi Alternate, with MRAA
//RF24 radio(15,0);

//RPi Alternate, with SPIDEV - Note: Edit RF24/arch/BBB/spi.cpp and  set 'this->device = "/dev/spidev0.0";;' or as listed in /dev
//RF24 radio(22,0); 

// Radio pipe addresses for the 2 nodes to communicate.
const uint8_t pipes[][6] = {"1Node", "2Node", "3Node", "4Node", "5Node", "6Node"};
int payload_size = 10; // plus 1, i think with \0 (so 9 char becomes 10))

int main(int argc, char** argv) 
{
    int fr = atoi(argv[1]);
    int to = atoi(argv[2]);
    int ac = atoi(argv[3]);
    
    printf("vendor/TMRh20/RF24_RPi/Task\n");
    
    // Refer to RF24.h or nRF24L01 DS for settings

    radio.begin();

    delay(5);
    
    radio.setPayloadSize(10);
    radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
    //radio.setAutoAck(1); // Ensure autoACK is enabled
    radio.setAutoAck(0); // Ensure autoACK is disabled
    radio.setPALevel(RF24_PA_HIGH);
    radio.setDataRate(RF24_250KBPS);
    //radio.setCRCLength(RF24_CRC_8);
    radio.setChannel(114);
    
    radio.openWritingPipe(pipes[(to-1)]); // atoi() change a char to a int
    radio.openReadingPipe(1,pipes[(fr-1)]);
    
    radio.startListening();
    
    radio.printDetails();
    
    // First, stop listening so we can talk.
    radio.stopListening();
    radio.powerUp();
    
    // Take the time, and send it.  This will block until complete

    char payload_send[10] = "";
    snprintf(payload_send, 10, "to:%d,ac:%d", to, ac);
    
    char payload_send_size[10] = "";
    snprintf(payload_send_size, 10, "%d", sizeof(payload_send));
    
    printf("Sending ..\n");
    printf("Payload size: ");
    printf(payload_send_size);
    printf("\n");
    printf("Payload: ");
    printf(payload_send);
    printf("\n");

    //bool ok = radio.write( &payload_send, 10 );
    radio.write( &payload_send, 10 );

    /*if (!ok){
        printf("failed.\n");
        exit(1);
    }*/
    
    //radio.powerDown();
    
    // Now, continue listening
    radio.startListening();

    // Wait here until we get a response, or timeout (250ms)
    unsigned long started_waiting_at = millis();
    bool timeout = false;
    
    // Reading temperature or humidity takes about 250 milliseconds!
    // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
    while ( ! radio.available() && ! timeout ) {
        if (millis() - started_waiting_at > 3000 ) {
            timeout = true;
        }
    }


    // Describe the results
    if ( timeout ) {
        printf("err: response timed out.\n");
        exit(1);
        
    } else {
        // Grab the response, compare, and send to debugging spew
        char payload_receive[10] = "";
        radio.read( &payload_receive, 10 );

        char payload_receive_size[10] = "";
        snprintf(payload_receive_size, 32, "%d", 10);
        // Spew it
        printf("Received ..\n");
        printf("Payload size: ");
        printf(payload_receive_size);
        printf("\n");
        printf("Payload: ");
        printf(payload_receive);
        printf("\n");
        
        printf(payload_receive);
        exit(0);
    }
    exit(1);
}

