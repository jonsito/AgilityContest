/*
Arduino_Chrono.ino

An example implementation of electronic chronometer based in Arduino Leonardo/Micro
to directly send events and timestamp marks throught onChip USB interface

- We use an Led&Key display board ( based in TM1631 I2C Chip ) to handle:
* display time, Faults and Refusals,
* mark sensor errors
* send fault, refusals, coursewalk, and so
- Events are sent as string "event:timestamp" to USB HID interface
- Sensors (start/stop/int) are handled via Arduino interrupt driven I/O pins

Copyright (C) 2011 Ricardo Batista <rjbatista at gmail dot com>

This program is free software: you can redistribute it and/or modify
it under the terms of the version 3 GNU General Public License as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

#include <TM1638.h>
#include <avr/interrupt.h>

// declare a TM1638 module at pins data:8, clock:9 and strobe:7
TM1638 module(8, 9, 7);

volatile int running; // to control chrono state
volatile int paused;
unsigned long startTime; // timestamp marks for start and stop (millis)
unsigned long stopTime;
unsigned long pauseTime;

int ledPin=13; // internal led
int irqPins[]={2,3,1};  // start/stop/intermediate input pins to trigger irq
unsigned long isrTime[]={0,0,0}; // time of each pin interrupt

void setup() {
  int n;
  // set up led
  pinMode(ledPin,OUTPUT);
  
  // set up timer
  running=0;
  paused=0;
  startTime=0;
  stopTime=0;
  // catch interrupt from input lines
  pinMode(irqPins[0],INPUT_PULLUP);
  pinMode(irqPins[1],INPUT_PULLUP);
  pinMode(irqPins[2],INPUT_PULLUP);
  attachInterrupt(digitalPinToInterrupt(irqPins[0]), startIRQ, FALLING);
  attachInterrupt(digitalPinToInterrupt(irqPins[1]), stopIRQ, FALLING);
  attachInterrupt(digitalPinToInterrupt(irqPins[2]), intermediateIRQ, FALLING);

  // and say we are ready
  module.setDisplayToString(" HELLO ",0x00);
  delay(5000); // time to see message :-)
}

void startChrono(unsigned long timestamp) { startTime=timestamp; running=true; }
void stopChrono(unsigned long timestamp) { 
  if ((timestamp-startTime)<2000) return; // 2 seconds safeguard to stop 
  stopTime=timestamp; 
  running=false;
}
void pauseChrono(unsigned long timestamp) { pauseTime=timestamp; paused=true; }

void handleIRQ(int n) { // interrupt service routine
  unsigned long tstamp=millis();
  if ( (tstamp-isrTime[n])<50 ) return; // debouncing time is active
  isrTime[n]=tstamp;
  // ok: no more bounces at pin 'n': check state. if high means low-to-high: so ignore
  int state=digitalRead(irqPins[n]);
  if (state==HIGH) return; // button release: ignore
  if (running && (n==2)) pauseChrono(tstamp);
  else {
    if (running) stopChrono(tstamp);
    else startChrono(tstamp);
  }
}

void startIRQ () { handleIRQ(0); }
void stopIRQ () { handleIRQ(1); }
void intermediateIRQ () { handleIRQ(2); }

void loop() {
  char display[8];
  unsigned long currentTime=millis(); // capture timestamp
  unsigned long ellapsed;
  
  // mark running status
  digitalWrite(ledPin,running);

  // on stopped, evaluate time to show
  if (!running && !paused) ellapsed=0;
  if (!running && paused ) ellapsed=0;
  if (running && !paused ) ellapsed=currentTime-startTime;
  if (running && paused ) ellapsed=pauseTime-startTime;
  if (!running) ellapsed=stopTime-startTime;
  if ((pauseTime-currentTime)>4000) paused=false; // restart from pause after 5 seconds
  module.setDisplayToDecNumber(ellapsed/10,0x04,true);
  
  // pull i2c buttons
  module.setLEDs(module.getButtons());
  delay(50-(millis()-currentTime));
}

