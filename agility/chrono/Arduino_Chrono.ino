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
volatile unsigned long isrTime; // to control debouncing
unsigned long startTime; // timestamp marks for start and stop (millis)
unsigned long stopTime;

int ledPin=13; // internal led

void setup() {
  // set up led
  pinMode(ledPin,OUTPUT);
  pinMode(2,INPUT);
  digitalWrite(2,HIGH); // input, pull up
  // set up timer
  running=0;
  startTime=0;
  stopTime=0;
  isrTime=millis();
  // catch interrupt
  attachInterrupt(digitalPinToInterrupt(2), isr, FALLING);
  // and say we are ready
  module.setDisplayToString(" HELLO ",0x00);
  delay(5000); // time to see message :-)
}

void startChrono(long timestamp) { startTime=timestamp; running=true; }
void stopChrono(long timestamp) { stopTime=timestamp; running=false; }

void isr() { // interrupt service routine
  unsigned long tstamp=millis();
  if ( (tstamp-isrTime)<50 ) return; // debouncing time is active 
  isrTime=tstamp;
  if (running) stopChrono(tstamp);
  else startChrono(tstamp);
}

void loop() {
  char display[8];
  unsigned long currentTime=millis(); // capture timestamp
  unsigned long ellapsed=currentTime-startTime;
  // mark running status
  digitalWrite(ledPin,running);
  if (!running) ellapsed=stopTime-startTime;
  module.setDisplayToDecNumber(ellapsed/10,0x04 |(running<<8),true);
  // pull i2c buttons
  module.setLEDs(module.getButtons());
  delay(50-(millis()-currentTime));
}
