#define SERIAL_BAUD 9600

//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to

#include <DHT.h> // work 1.0, 1.1
#include <HomeDHT.h>

HomeDHT homedht(DHTPIN, DHTTYPE); // work 1.0

void setup() {
  Serial.begin(SERIAL_BAUD);
}

void loop() {
  char temperature[7];
  memset(&temperature, 0, sizeof(temperature)); // clear it
  strncpy( temperature, homedht.getTemperature(1), sizeof(temperature)-1 );
  
  if(homedht.getError()){
    Serial.print("Temperature Error:  ");
    Serial.println(homedht.getErrorMessage());
  }else {
    Serial.print("Temperature: ");
    Serial.println(temperature);
  }
    
  char humdity[7];
  memset(&humdity, 0, sizeof(humdity)); // clear it
  strncpy( humdity, homedht.getHumdity(), sizeof(humdity)-1 );
  
  if(homedht.getError()){
    Serial.print("Humdity Error:  ");
    Serial.println(homedht.getErrorMessage());
  }else {
    Serial.print("Humdity: ");
    Serial.println(humdity);
  }
  
  delay(5000);
}
