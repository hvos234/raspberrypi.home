#include <stdlib.h> // used for the dtostrf function

#include "DHT.h"

#include <SPI.h>
#include "RF24.h"
#include <printf.h>  // Printf is used for debug

#define BAUDRATE 9600

#define MASTERID 1  // number of the master device
#define MYID 2  // number of the device

// Uncomment whatever type you're using!
//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to
#define UNIT 1      // 0 for Fahrenheit and 1 for Celsius

DHT dht(DHTPIN, DHTTYPE); // set dht

RF24 radio(7,8);                // nRF24L01(+) radio attached using Getting Started board 

byte pipes[][6] = {"1Node", "2Node", "3Node", "4Node", "5Node", "6Node"};

// declare actions
#define ACTIONTEMP 1 // send temperature
#define ACTIONHUM 2 // send humidity
#define ACTIONTEMPHUM 3 // send temperature and humidity

// declare variables
int payload_size = 10; // plus 1, i think with \0 (so 9 char becomes 10))
char payload_receive[10]; // max is 32 bytes even with enableDynamicPayloads
char payload_send[10];
char message[10];

//int task; // can not send task it gets to big
//int fr;
int to;
int ac;

void setup(void)
{
  Serial.begin(BAUDRATE);
  Serial.println("vendor/TMRh20/RF24_Arduino/deviceBalcony/");
  
  Serial.println("Printf begin.");
  printf_begin(); //Printf is used for debug
  
  Serial.println("Radio begin.");
  radio.begin();
  
  radio.setPayloadSize(10);
  radio.setRetries(15,15); // optionally, increase the delay between retries & # of retries
  //radio.setAutoAck(1); // Ensure autoACK is enabled
  radio.setAutoAck(0); // Ensure autoACK is disabled
  radio.setPALevel(RF24_PA_HIGH);
  radio.setDataRate(RF24_250KBPS);
  //radio.setCRCLength(RF24_CRC_8);
  radio.setChannel(114);
  
  radio.openWritingPipe(pipes[(MASTERID -1)]);
  radio.openReadingPipe(1,pipes[(MYID -1)]);
  
  radio.startListening();
  
  radio.printDetails();
}

char *messageTempHum(int ac, char* message){
  char tem[10]; //2 int, 2 dec, 1 point, and \0
  char hum[10];
  
  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  float h = dht.readHumidity();
  float t = dht.readTemperature();
  float tf = t * 1.8 +32;  //Convert from C to F
  
  // check if returns are valid, if they are NaN (not a number) then something went wrong!
  if (isnan(t) || isnan(h)) {
    Serial.println("Failed read DHT");
    sprintf(message, "err:%s", "fa re");
    
  }else {
    Serial.print("Humidity: "); 
    Serial.print(h);
    Serial.print(" %\t");
    
    Serial.print("Temperature: "); 
    if (UNIT == 0 ){  //choose the right unit F or C
      Serial.print(tf);
      Serial.println(" *F");
    }
    else {
      Serial.print(t);
      Serial.println(" *C");
    }
      
    dtostrf(h, 2, 2, hum);  //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(t, 2, 2, tem);
    
    Serial.print("Message: ");
    Serial.print("hum: ");
    Serial.print(hum);
    Serial.print("tem: ");
    Serial.println(tem);
    
    // build message
    if(ACTIONTEMP == ac){ // temperature
      sprintf(message, "tem:%s", tem);
    }
    if(ACTIONHUM == ac){ // humidity
      sprintf(message, "hum:%s", hum);
    } 
    if(ACTIONTEMPHUM == ac){ // temperature and humidity
      sprintf(message, "tem:%s,hum:%s", tem, hum);
    }
  }
}

void loop(void){
  
  if( radio.available()){
    Serial.println("");
    
    while ( radio.available() ) {     // Is there anything ready for us?
      memset(payload_receive, 0, sizeof(payload_receive)); // clear it
      radio.read(&payload_receive, 10);
    }      
    
    Serial.println("Received ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_receive));
    Serial.print("Payload: ");
    Serial.println(payload_receive);
      
    //from = 0;
    to = 0;
    ac = 0;
    sscanf((char *)payload_receive, "to:%d,ac:%d", &to, &ac);
    
    Serial.print("Action: ");
    Serial.println(ac);
    
    memset(message, 0, sizeof(message)); // clear it
    if(ACTIONTEMP == ac || ACTIONHUM == ac || ACTIONTEMPHUM == ac){
      messageTempHum(ac, message);
      
    }else {
      Serial.println("Action does not exist !");
      sprintf(message, "err:%s", "ac no");
    }
    
    Serial.print("Message: ");
    Serial.println(message);
      
    memset(payload_send, 0, sizeof(payload_send)); // clear it
    sprintf(payload_send, "%s", message);
    
    // First, stop listening so we can talk
    radio.stopListening();
    radio.powerUp();
    
    Serial.println("Sending ..");
    Serial.print("Payload size: ");
    Serial.println(sizeof(payload_send));
    Serial.print("Payload: ");
    Serial.println(payload_send);
    
    // Send the final one back.
    //bool ok = radio.write( payload_send, 10 );
    radio.write( payload_send, 10 );
    
    
    /*if (ok){
      Serial.println("Sending ok.");
    }else{
      Serial.println("Sending failed !");
    }*/
     
    radio.powerDown();
    // Now, resume listening so we catch the next packets.
    radio.startListening();
  }
}
