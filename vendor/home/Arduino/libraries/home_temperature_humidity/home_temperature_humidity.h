#include <stdlib.h> // used for the dtostrf function

//#define DHTTYPE DHT11   // DHT 11 
#define DHTTYPE DHT22   // DHT 22  (AM2302)
//#define DHTTYPE DHT21   // DHT 21 (AM2301)
#define DHTPIN 6     // what pin the DHT is connected to
#define UNIT 1      // 0 for Fahrenheit and 1 for Celsius

DHT dht(DHTPIN, DHTTYPE); // set dht

char *home_temperature() {
    // reset error
    error.is_error = false;
    memset(&error.message, 0, sizeof(error.message)); // clear it
    
    // declare variables
    char temperature[7]; //2 int, 2 dec, 1 point, and \0
    float t = 0;
    float tf = 0;

    // empty variables
    memset(&temperature, 0, sizeof(temperature)); // clear it

    t = dht.readTemperature();
    tf = t * 1.8 +32;  //Convert from C to F

    if (isnan(t)) {
      Serial.println("Error getTemperature(), Failed read DHT !");
      error.is_error = true;
      strncpy( error.message, "Failed read DHT !", sizeof(error.message)-1 );

      return (char *) "Failed read DHT !";
    }

    error.is_error = false;

    Serial.print("Temperature: ");
    if (UNIT == 0 ){  //choose the right unit F or C
      Serial.print(tf);
      Serial.println(" *F");
      //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
      dtostrf(tf, 2, 2, temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }else {
      Serial.print(t);
      Serial.println(" *C");
      dtostrf(t, 2, 2, temperature); // dtostrf convert it to 2 before decimal and 2 after decimal
    }

    return temperature;
}

char *home_humidity() {
    // reset error
    error.is_error = false;
    memset(&error.message, 0, sizeof(error.message)); // clear it
    
    // declare variables
    char humidity[7]; //2 int, 2 dec, 1 point, and \0
    float h = 0;

    // empty variables
    memset(&humidity, 0, sizeof(humidity)); // clear it

    h = dht.readHumidity();

    if (isnan(h)) {
      Serial.println("Error getHumidity(), Failed read DHT !");
      error.is_error = true;
      strncpy( error.message, "Failed read DHT !", sizeof(error.message)-1 );

      return (char *) "Failed read DHT !";
    }

    error.is_error = false;

    Serial.print("Humidity: "); 
    Serial.print(h);
    Serial.println(" %\t");

    //Floats don't work in sprintf statements on Arduino without pain, so convert to string separately.
    dtostrf(h, 2, 2, humidity); // dtostrf convert it to 2 before decimal and 2 after decimal

    return humidity;
}
