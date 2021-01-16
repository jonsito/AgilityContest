
/*
  Code for Mikan Agility Chronometer
  (c) Juan Antonio Martínez <jonsito@gmail.com>
  License GNU General Public License
  https://platformio.org/lib/show/6641/TM1637%20Driver
  https://github.com/AKJ7/TM1637
*/
#include <TM1637.h>

#define SERIAL_BUFFER_LENGTH 128

/*
 * Pin definitions (ARDUINO UNO)
 */
  int input_1=2; // input sensor 1, hw-debounced, triggers interrupt in Low-to-High transition
  int input_2=3; // input sensor 2,  hw-debounced, triggers interrupt in Low-to-High transition
  int buzzer=4; // output. On high level turns on buzzer and warning led
  int reset=5; // input, sw-debounced. equivalent to "reset" command
  // pins 6 and 7 are used by TM1637 display
  
 /*
  * pseudo-I2C TM1637 display
  * ( not a real i2c device, so cannot use arduino i2c functions to handle )
  */
  TM1637 tm1637(6, 7); // clock,data
  
/*
 * Variables
 */
 int verbose=0; // set to 1 to send debug messages to console

 // time counting related variables
 int running=0; 
 unsigned long start_time=0; // timestamp of last "start" operation
 unsigned long start_offset=0; // offset received from start serial command
 unsigned long down_time=0; // timestamp for 15 seconds start countdown
 unsigned long walk_time=0; // timestamp for 7 minutes course walk
 unsigned long elapsed=0; // elapsed time. zero or last time when running=0
 unsigned long elapsed_old=0; // to check for display refresh request
 int iteration=0; // used to track 100ms intervals in main loop

 char serialInputBuffer[SERIAL_BUFFER_LENGTH];
 char serialOutputBuffer[SERIAL_BUFFER_LENGTH];
 int serialIndex=0; // puntero a donde guardar siguiente caracter

 // gestion de mensajes temporales // int, F:R:E
 int msg_counter=0; // downcounter para presentar mensajes temporales
 char msg_buffer[8];
 
 // variables de control de estado
 int beepPending=0; // set to 1 when beep is required
 int brightness=5; // control de brillo
 int sensor_error=0; // error de sensores
 int faltas=0;
 int rehuses=0;
 int eliminado=0;
 
// the setup function runs once when you press reset or power the board
void setup() {
  // initialize digital pin LED_BUILTIN as an output.
  pinMode(LED_BUILTIN, OUTPUT);
  pinMode(buzzer,OUTPUT); // buzzer -  active:high
  pinMode(input_1,INPUT);  // entrada 1 - active:high
  pinMode(input_2,INPUT);  // entrada 2 - active:high
  pinMode(reset,INPUT);  // boton de reset del crono - active:high
  
  // activamos interrupciones.
  // el arduino uno solo permite interrupciones en los pines 2 y 3
  // por lo que hay que hacer polling a mano en el boton de reset
  attachInterrupt(digitalPinToInterrupt(input_1), input_service, RISING);
  attachInterrupt(digitalPinToInterrupt(input_2), input_service, RISING);
  
  // setup display
  tm1637.begin();
  brightness=9; // manual states range 0..9
  tm1637.setBrightness(brightness/2); // display has range 0..5
  tm1637.display("hola"); 
  
  // set serial port
  Serial.begin(115200); // arduino uno 
  while(!Serial){;} // wait for serial port to connect. Needed for native USB
  Serial.println("OK");
  
  // every thing ok; go to main loop
  doBeep(100);doBeep(100);doBeep(100);
  tm1637.colonOn();
  tm1637.display("0000");
}

/*
 * Activate the internal buzzer during requested time
 * Also, warn LED is (by hardware) turned on
 */
void doBeep(int n)  { // millisecons
  digitalWrite(buzzer,HIGH);
  delay(n);
  digitalWrite(buzzer,LOW);
}

/* clear variables when reset or start is performed */
void reset_data() { 
  faltas=0; 
  rehuses=0; 
  eliminado=0; 
  msg_counter=0;
  down_time=0;
  walk_time=0;
  start_offset=0;
}

/**
 * Interrupt service routine
 * 
 * Notice that cannot call functions related to timers inside interruption service routines, 
 * so cannot beep nor sending i2c data nor serial communication
 */
void input_service() {
  if (walk_time!=0) return; // Ignore sensor info when in course walk. Just use reset to clear state
  unsigned long now = millis();
  if ( (now-start_time) <= 2000 ) return; // ignore if latest event was less than 2 seconds
  elapsed = now - start_time;
  start_time=now; // set start_time and fix 2000 msecs guard time until next sensor event
  if (running==0) {
    running=1;
    sprintf(serialOutputBuffer,"START 0\n"); // cannot send serial data inside interrupt
    reset_data();
    // no need to display data: job is done in main loop
  } else {
    sprintf(serialOutputBuffer,"STOP %d\n",elapsed); // milliseconds elapsed
    running=0;
  }
  beepPending=1;
}

/*
 * Called when operator receives or performs reset
 * on flag:1, also send reset command via serial port
 */
void reset_service(int flag) { 
  if (flag) Serial.println("RESET");
  running=0;
  start_time=0;
  elapsed=0;
  elapsed_old=-1; // force re-display
  reset_data();
  beepPending=1;
}

/*
 * check for sensor(s) error/recovery. executed every 100 msecs
 */
int check_sensorError() {
  static int i1=0;
  static int i2=0;
  if (digitalRead(input_1)==1) i1++; else i1=0; // check sensor 1
  if (digitalRead(input_2)==1) i2++; else i2=0; // check sensor 2
  if ( (i1>=20) || (i2>=20) ) {
    if (sensor_error==0) Serial.println("FAIL");
    // send fail every (aprox) second
    if ( (i1>20) && ((i1%10)==0) ) Serial.println("FAIL");
    if ( (i2>20) && ((i2%10)==0) ) Serial.println("FAIL");
    sensor_error=1;
  } else {
    if (sensor_error==1) Serial.println("OK");
    sensor_error=0;
  }
  return sensor_error;
}

/*
 * Check reset button. on trigger call reset service
 * called every 100 msecs
 * if button is pressed more than 4 seconds, performs a "hardware" reset
 */
void handle_reset() {
  static int rcount=0;
  int st=digitalRead(reset);
  if (digitalRead(reset)!=0) rcount++; else rcount=0;
  if (rcount==3) reset_service(1); // 300 msecs for digital debouncing
  if (rcount==40) { // after 4 seconds perform software reset by JMP 0x0000
    tm1637.display("boot");
    if (verbose) Serial.println("Resetting device");
    doBeep(500);
    asm("jmp 0x0000"); 
  }
}

/*
 * Handle data received via serial port after '\n' is received
 * parse command stored in serialInputBuffer
 * do not re-route command to serial port as we have received it
 */
void handle_serial() {
  char buff[16]; 
  // on verbose mode trace data
  if(verbose) { Serial.print("received: "); Serial.println(serialInputBuffer); }
  
  // start parsing available commands from higher to lower priority
  if (strcmp("RESET",serialInputBuffer)==0) { // RESET
    reset_service(0);
  }
  else if (strncmp("START",serialInputBuffer,5)==0) { // START [XXXXX] ( msecs. if no data, just assume 0 )
    if (running!=0) return; // already running
    long int offset=0;
    if (strlen(serialInputBuffer)>=6) offset= atol(&serialInputBuffer[6]); 
    start_time = millis(); // mark start time
    running=1;
    beepPending=1;
    reset_data();
    start_offset=offset; // store received start offset ( if any )
  } 
  else if (strncmp("STOP ",serialInputBuffer,5)==0) { // STOP xxxxx (msecs) 
    running=0;
    elapsed=atol(&serialInputBuffer[5])-start_offset; // let main loop handle display time
    beepPending=1;
  }
  else if (strncmp("FAULT ",serialInputBuffer,6)==0) { // FAULTS [+,-,#]
    switch(serialInputBuffer[6]) {
      case '+': faltas++; if (faltas>=10) faltas=9; break;
      case '-': faltas--; if (faltas<0) faltas=0; break;
      default: faltas=atol(&serialInputBuffer[6]); break;
    }
    sprintf(msg_buffer,"F%dR%d",faltas, rehuses);
    msg_counter=20; // set 2 seconds display
  }
  else if (strncmp("REFUSAL ",serialInputBuffer,8)==0) { // REFUSAL [+,-,#]
    switch(serialInputBuffer[8]) {
      case '+': rehuses++; if (rehuses>=10) rehuses=9; break;
      case '-': rehuses--; if (rehuses<0) rehuses=0; break;
      default: rehuses=atol(&serialInputBuffer[8]); break;
    }
    sprintf(msg_buffer,"F%dR%d",faltas, rehuses);
    msg_counter=20; // set 2 seconds display
  }
  // on eliminated message, let main loop to handle display
  else if (strncmp("ELIM ",serialInputBuffer,5)==0) { // "ELIM +/-/1/0" set eliminated mask
    switch(serialInputBuffer[5]) {
      case '-': 
      case '0': eliminado=0; break;
      case '+': 
      case '1': 
      default: eliminado=1; break;
      
    }
  }
  else if (strncmp("ELIM",serialInputBuffer,4)==0) { // "ELIM"
    eliminado=1;
  }
  else if (strncmp("DATA ",serialInputBuffer,5)==0) { // DATA F:R:E
    faltas=atoi(&serialInputBuffer[5]);
    rehuses=atoi(&serialInputBuffer[7]);
    eliminado=atoi(&serialInputBuffer[9]);
    if (eliminado!=0) return; // let eliminado to be handled in main loop
    sprintf(msg_buffer,"F%dR%d",faltas, rehuses); msg_counter=20; 
  }
  else if (strncmp("INT ",serialInputBuffer,4)==0) { // INT XXXXX (msecs) intermediate time 
    // notice that this should be generated by chrono. just for debugging purposes 
    sprintf(msg_buffer,"%04d",( atol(&serialInputBuffer[4]) - start_offset ) /10 );
    msg_counter=20; // set 2 seconds display
    beepPending=1; // mark beep
  }
  else if (strncmp("DOWN",serialInputBuffer,4)==0) { // DOWN [XX] 15(default) seconds countdown
    if (running) return; // ignore this command when chrono is running
    down_time=15000; // default 15000 msecs
    start_time=millis(); // do not mark running
    if (strlen(serialInputBuffer)>=6) down_time=1000*atol(&serialInputBuffer[5]); // conver to msecs
  }
  else if (strncmp("WALK",serialInputBuffer,4)==0) { // WALK [XXX] 300..600 420(default) seconds course walk
    if (running) return; // ignore this command when chrono is running
    walk_time=420000;
    start_time=millis(); // do not mark running
    if (strlen(serialInputBuffer)>=6) walk_time=1000*atol(&serialInputBuffer[5]); // convert to msecs
  }
  else if (strncmp("DORSAL ",serialInputBuffer,7)==0) {
    sprintf(msg_buffer,"d%3d",atol(&serialInputBuffer[7]));
    msg_counter=10;
  }
  else if (strncmp("BRIGHT ",serialInputBuffer,7)==0) { // BRIGHT [+,-,#] (0..9)
    switch(serialInputBuffer[7]) {
      case '+': brightness++; if (brightness>=10) brightness=9; break;
      case '-': brightness--; if (brightness<0) brightness=0; break;
      default: brightness=atol(&serialInputBuffer[7]); break;
    }
    tm1637.setBrightness(brightness/2); // notice that display range is 0..4
    if (running) return; // auto-refresh on next show data
    // not running: need to refresh display with previous data
    sprintf(buff,"%04d",elapsed/10);
    tm1637.display(&buff[0]); 
  }
  else if (strncmp("DEBUG ",serialInputBuffer,6)==0) { // DEBUG 0..7
    if (atol(&serialInputBuffer[6])>5) verbose=1; else verbose=0;
  }
  else if (verbose) { // UNKNOWN command
    Serial.print("Unknown command: "); 
    Serial.println(serialInputBuffer); 
  }
}

/** 
 * the loop function runs over and over again forever
 * this code is re-executed every 100 miliseconds ( aprox. accuracy is not really required, as we use millis() to handle time )
 */
void loop() {
  char buff[16]; 
  // update display information
  // when running need to re-evaluate 'elapsed' time since start
  // on stop, 'elapsed' is preserved
  // on reset, 'elapsed' is set to zero
  if( running ) {
    // update elapsed time
    elapsed=(millis()-start_time);
    // every 200 msecs turn internal led on to mark running state
    if ((iteration&0x01)==0) digitalWrite(LED_BUILTIN, HIGH);
  }

  // handle sensor failure and "eliminado" conditions showing message every 800ms
  if ( (iteration&0x08) && ( (eliminado==1) || check_sensorError() ) ) {
    tm1637.colonOff();
    if (eliminado) tm1637.display("ELI "); else {tm1637.display("FAIL"); if (iteration&0x02) beepPending=1;}
    elapsed_old=-1;
  } else { // not eliminated nor sensor fail
    tm1637.colonOn();
    if (msg_counter>1) { // handle temporary messages. notice that colon may be disabled
      msg_counter--;
      // on show dorsal disable colon
      if (msg_buffer[0]=='d') tm1637.colonOff();
      tm1637.display(msg_buffer);
      elapsed_old=-1;
    } else { // no temporary messages pending
      // if in course walk mode display it
      if (walk_time!=0) {
        long int spent=millis() - start_time;
        if (spent>walk_time) { // course walk ended. set to zero
          start_time=0;
          walk_time=0;
          elapsed_old=-1; // force refresh on next iteration
          tm1637.display("0000");
          beepPending=1;
        } else { // evaluate and display remaining time ( min:secs )
          long int secs=(walk_time-spent)/1000;
          sprintf(buff,"%02d",secs/60);
          sprintf(&buff[2],"%02d",secs%60);
          tm1637.display(&buff[0]);
        }
      }
      // else if in countdown mode display it
      else if (down_time!=0) {
        long int spent=millis() - start_time;
        if (spent>down_time) { // 15 seconds countdown ended. set to zero
          start_time=0;
          down_time=0;
          elapsed_old=-1; // force refresh on next iteration
          tm1637.display("0000");
          beepPending=1;
        } else { // evaluate and display remaining time  secs:cents
          sprintf(buff,"%04d",(down_time -spent)/10);
          tm1637.display(&buff[0]);
        }
      }
      // else we are in stop or running mode
      // if required update display. we use an auxiliar variable 'elapsed_old' to track changes
      else if (elapsed_old!=elapsed) {
        sprintf(buff,"%04d",elapsed/10);
        tm1637.display(&buff[0]); 
        elapsed_old=elapsed; 
      }
    }
  }

  // if serial data available handle it
  while (Serial.available()) {
    char c=Serial.read();
    switch(c) {
      case '\r': // ignore '\r' just wait for '\n'
        break;
      case '\n':
        serialInputBuffer[serialIndex]='\0';
        handle_serial();
        serialIndex=0;
        break;
      default:
        serialInputBuffer[serialIndex++]=toupper(c);
        break;
    }
    // check for buffer overflow
    if (serialIndex>=SERIAL_BUFFER_LENGTH) {
      serialIndex=0;
      if(verbose) Serial.println("Serial buffer overflow");
    }
  }

  // check for (hw)reset button
  handle_reset(); 
  
  // if serial data pending to be sent from irq service, proceed
  if (serialOutputBuffer[0]!='\0') { Serial.print(serialOutputBuffer); serialOutputBuffer[0]='\0'; }
  
  // if beep pending, proceed. notice that doBeep() calls delay() inside
  if(beepPending) { doBeep(100); beepPending=0; }
  else delay(100);

  // make sure to set proper state before repeat loop
  digitalWrite(LED_BUILTIN, LOW); // set internal led to off if before state was high
  iteration++; // next iteration
}
