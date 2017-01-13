/*char *getSerial(){
    Serial.println("Serial .. ");
    char serial[21]; // ^fr:10,to:11,ac:12$ plus \0
    String serial_string = "";
    boolean start = false;
    boolean end = false;
    int i = 0;
    
    
    while (Serial.available () > 0) {
        // https://www.arduino.cc/en/Reference/ASCIIchart
        int ascii = Serial.read ();
        char character = ascii;
        printf("%c", character);  
        //char character = Serial.read ();
        //String character_string = ""; // we need this to convert the char character from ASII to readable char
        //character_string.concat(character);
        //delay(1);
        
        //Serial.print("Read: ");
        //Serial.print("byte: ");
        //Serial.print(character);
        //Serial.print(" char: ");
        //Serial.println(character_string);
        if(start){
            serial[i] += character;
            i++;
        }
        
        //if(94 == character){
        if('^' == character){
            start = true;
            i = 0;
        }
        
        //if(36 == character){
        if('$' == character){
            start = false;
            end = true;
            i = 0;
        }
        
        if(end){
            start = false;
            end = false;
            i = 0;
            /*serial_string.toCharArray(serial, 21);
            Serial.print("End: ");
            Serial.print(character_string);
            Serial.print("string: ");
            Serial.print(serial_string);
            Serial.print(" serial: ");*/
            /*Serial.println(serial);
            
            return serial;
        }
    }
}*/

typedef struct {
  int from;
  int to;
  int task;
  int action;
  char message[30];
} HomeSerial;

HomeSerial home_serial;

/**
 * This is easier than the above function, because you have to change every indivuduele character from ASII to char
 * @return 
 */
char *home_serial_read(){
    // reset error
    error.is_error = false;
    memset(&error.message, 0, sizeof(error.message)); // clear it
    
    // declare variables
    int num_bytes = 0;
    char bytes[30]; // ^fr:10;to:10;ts:12;ac:10;$ plus \0
    
    // empty variables
    memset(&bytes, 0, sizeof(bytes)); // clear it
    //bytes[0] = '\0';
    
    num_bytes = Serial.readBytesUntil('\0', bytes, sizeof(bytes));
    
    Serial.println("Serail ..");
    
    error.is_error = true;
    strncpy( error.message, "No serial !", sizeof(error.message)-1 );    
    
    int count = 0;
    char serial[30]; // ^fr:10;to:10;ts:12;ac:10;$ plus \0
    
    boolean start = false;
    for(int i = 0; i <= num_bytes; i++){        
        if('^' == bytes[i]){
            start = true;
            count = 0;
            
            error.is_error = true;
            memset(&error.message, 0, sizeof(error.message)); // clear it
            strncpy( error.message, "Serial only started !", sizeof(error.message)-1 );
            
        }else if(start && '$' == bytes[i]){
           start = false;
           serial[count] = '\0';
           
           error.is_error = false;
           memset(&error.message, 0, sizeof(error.message)); // clear it
           
       }else if(start){
            serial[count] = bytes[i];
            count++;
        }
    }
    
    Serial.print("Serial: ");
    Serial.println(serial);
    
    return serial;
}

boolean home_serial_write(char *serial){
    Serial.print('^');
    Serial.print(serial);
    Serial.println('$');
    
    error.is_error = false;
    memset(&error.message, 0, sizeof(error.message)); // clear it
    
    return true;
}

void home_serial_sscanf(char *serial){
    home_serial.from = 0;
    home_serial.to = 0;
    home_serial.task = 0;
    home_serial.action = 0;
    memset(&home_serial.message, 0, sizeof(home_serial.message)); // clear it
    
    sscanf((char *)serial, "fr:%d;to:%d;ts:%d;ac:%d;msg:%s", &home_serial.from, &home_serial.to, &home_serial.task, &home_serial.action, &home_serial.message); 
    
    Serial.print("Serial sscanf: ");
    Serial.print("From: ");
    Serial.print(home_serial.from);
    Serial.print(" To: ");
    Serial.print(home_serial.to);
    Serial.print(" Task: ");
    Serial.print(home_serial.task);
    Serial.print(" Action: ");
    Serial.print(home_serial.action);
    Serial.print(" Message: ");
    Serial.println(home_serial.message);
}