#define THERMOPIN 3
#define LIGHTPIN 5

// rest
#define SERIAL_BAUD 9600

void setup() {
  Serial.begin(SERIAL_BAUD);

  pinMode(THERMOPIN, OUTPUT);
  digitalWrite(THERMOPIN, LOW);

  pinMode(LIGHTPIN, OUTPUT);
  digitalWrite(LIGHTPIN, LOW);

  Serial.println("Setup Finished !");
}

void loop() {
  Serial.println("On");
  digitalWrite(THERMOPIN, HIGH);

  delay(1000);
  digitalWrite(LIGHTPIN, HIGH);

  delay(9000);
  
  Serial.println("Off");
  digitalWrite(THERMOPIN, LOW);
  
  delay(1000);
  digitalWrite(LIGHTPIN, LOW);

  delay(9000);
}

