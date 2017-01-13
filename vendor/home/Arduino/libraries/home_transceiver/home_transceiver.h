#ifndef NODEID
#define NODEID      1
#endif

#define NETWORKID   100
#define FREQUENCY   RF69_433MHZ //Match this with the version of your Moteino! (others: RF69_433MHZ, RF69_868MHZ)
#define KEY         "sampleEncryptKey" //has to be same 16 characters/bytes on all nodes, not more not less!
#define ACK         true
#define ACK_RETRIES 2
#define ACK_WAIT    30
#define TIMEOUT     6000 // wait for respones

boolean requestACK = false;
bool promiscuousMode = false; //set to 'true' to sniff all packets on the same network

RFM69 radio;

void home_transceiver_setup() {
    radio.initialize(FREQUENCY,NODEID,NETWORKID);
    //radio.setHighPower(); //uncomment only for RFM69HW!

    // this change the bitrate from 4800 to 1200, and Frequenty level to max
    radio.writeReg(0x03,0x68);      //RegBitrateMsb 1200 bitrate
    radio.writeReg(0x04,0x2B);      //RegBitrateLsb 1200 bitrate
    radio.writeReg(0x05,0x00);      //RegFdevMsb     2000 
    radio.writeReg(0x06,0x52);      //RegFdevLsb     2000
    radio.writeReg(0x19,0x40|0x10|0x05);      //RegRxBw  DccFreq:010, RxBw_Mant:24, RxBw_Exp:5 
    radio.writeReg(0x18,0x00|0x00|0x01);      //RegLna  LnaZin:50ohm, LowPower:Off, CurrentGain:MAX

    radio.encrypt(KEY);
    radio.promiscuous(promiscuousMode);
}

void home_transceiver_send (int from, int to, char *payload){
    radio.send(to, (const void*)(&payload), sizeof(payload), false);
    delay(25); // make sure payload is send
}

bool home_transceiver_sendWithRetry (int from, int to, char *payload){
    bool successful;
    successful = radio.sendWithRetry(to, (const void*)(&payload), sizeof(payload), ACK_RETRIES, ACK_WAIT);
    return successful;
}

char *home_transceiver_receive() {
    char payload[30];
    memset(&payload, 0, sizeof(payload)); // clear it
    for (byte i = 0; i < radio.DATALEN; i++) {
      payload[i] = (char)radio.DATA[i];
    }
    
    if (radio.ACKRequested()){
        radio.sendACK();
    }
    
    return payload;
}

int home_transceiver_sender_id () {
    return radio.SENDERID;
}

int home_transceiver_rssi(){
    return radio.readRSSI();
}