
/*  Keypadtest.pde
 *
 *  Demonstrate the simplest use of the  keypad library.
 *
 *  The first step is to connect your keypad to the
 *  Arduino  using the pin numbers listed below in
 *  rowPins[] and colPins[]. If you want to use different
 *  pins then  you  can  change  the  numbers below to
 *  match your setup.
 *
 */
#include <Keyboard.h>
#include <Keypad.h>

const byte ROWS = 4; // Four rows
const byte COLS = 3; // Three columns
// Define the Keymap
char keys[ROWS][COLS] = {
  {'1','2','3'},
  {'4','5','6'},
  {'7','8','9'},
  {'*','0','#'}
};
// Don't use pin 13 (led)
// Connect keypad ROW0, ROW1, ROW2 and ROW3 to these Arduino pins.
byte rowPins[ROWS] = { 12,5,7,10 };
// Connect keypad COL0, COL1 and COL2 to these Arduino pins.
byte colPins[COLS] = { 11,4,9 }; 
int ledState;

// Create the Keypad
Keypad kpd = Keypad( makeKeymap(keys), rowPins, colPins, ROWS, COLS );

#define ledpin 13

void setup()
{
  int ledState=HIGH;
  pinMode(ledpin,OUTPUT);
  digitalWrite(ledpin, ledState);
}

void loop()
{
  char key = kpd.getKey();
  if(key)  // Check for a valid key.
  {
    switch (key)
    {
      case '*':
        key='\b';
        ledState=LOW;
        digitalWrite(ledpin, ledState);
        break;
      case '#':
        key='\n';
        ledState=HIGH;
        digitalWrite(ledpin, ledState);
        break;
      default:
        digitalWrite(ledpin, (ledState==LOW)?HIGH:LOW);
        delay(100);
        digitalWrite(ledpin,ledState);
    }
    Keyboard.write(key);
  }
}
