#define THERMOSTATPIN A4

// rest
#define SERIAL_BAUD 9600

float thermoSensor = 0.0f;
int thermoSensorAvarage = 0;

float thermoSensorVoltageOut = 0.00;
float thermoSensorVoltageIn = 0.00;
float thermoSensorVoltageR1 = 30000.0; //  
float thermoSensorVoltageR2 = 7500.0; // 

void setup() {
  Serial.begin(SERIAL_BAUD);

  pinMode(THERMOSTATPIN, INPUT); // sets the analog pin as input, it only have to receive it
  
  Serial.println("Setup Finished !");
}

void loop() {
  thermoSensor = analogRead(THERMOSTATPIN);

  Serial.print("thermoSensor: ");
  Serial.println(thermoSensor, 2);

  // https://www.youtube.com/watch?v=dJ1vHbwdKJQ
  thermoSensorVoltageOut = (thermoSensor * 5.0) / 1024.0; // see text
  thermoSensorVoltageIn = thermoSensorVoltageOut / (thermoSensorVoltageR2/(thermoSensorVoltageR1+thermoSensorVoltageR2));

  Serial.print("thermoSensorVoltageOut: ");
  Serial.println(thermoSensorVoltageOut, 2);

  Serial.print("thermoSensorVoltageIn: ");
  Serial.println(thermoSensorVoltageIn, 2);

  delay(10000);
}

