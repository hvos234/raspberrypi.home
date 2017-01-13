#define SERIAL_BAUD 9600

#include <HomeSerial.h>
HomeSerial homeserial;

void setup() {
  Serial.begin(SERIAL_BAUD);
}

void loop() {
  //process any serial input
  if (Serial.available() > 0) {
    char serial[50];
    memset(&serial, 0, sizeof(serial)); // clear it
    strncpy( serial, homeserial.readSerial(), sizeof(serial)-1 );
    
    if(homeserial.getError()){
      Serial.print("Serial Error:  ");
      Serial.println(homeserial.getErrorMessage());
    }else {
      Serial.print("Serial Received: ");
      Serial.println(serial);
      
      if(!homeserial.sscanfSerial()){
        Serial.print("Serial Sscanf Error:  ");
        Serial.println(homeserial.getErrorMessage());
      }else {
        int from = homeserial.getFrom();
        int to = homeserial.getTo();;
        int task = homeserial.getTask();;
        int action = homeserial.getAction();;
        char message[30];
        memset(&message, 0, sizeof(message)); // clear it
        strncpy( message, homeserial.getMessage(), sizeof(message)-1 );
        
        Serial.print("Serial Sscanf: ");
        Serial.print("From: ");
        Serial.print(from);
        Serial.print(" To: ");
        Serial.print(to);
        Serial.print(" Task: ");
        Serial.print(task);
        Serial.print(" Action: ");
        Serial.print(action);
        Serial.print(" Message: ");
        Serial.println(message);
      }     
    }
  }
}
