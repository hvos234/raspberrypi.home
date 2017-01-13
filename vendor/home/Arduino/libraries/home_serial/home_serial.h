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
    char bytes[21]; // ^fr:10,to:11,ac:12$ plus \0
    
    // empty variables
    memset(&bytes, 0, sizeof(bytes)); // clear it
    //bytes[0] = '\0';
    
    num_bytes = Serial.readBytesUntil('\0', bytes, 21);
    
    Serial.println("Serail ..");
    
    error.is_error = true;
    strncpy( error.message, "No serial !", sizeof(error.message)-1 );    
    
    int count = 0;
    char serial[21]; // ^fr:10,to:11,ac:12$ plus \0
    
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