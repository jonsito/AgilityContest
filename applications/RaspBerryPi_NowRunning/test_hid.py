#!/usr/bin/python3
import evdev
from evdev import InputDevice, categorize, ecodes  # import * is evil :)

# Provided as an example taken from my own keyboard attached to a Centos 6 box:
scancodes = {
    # Scancode: ASCIICode
     0: None,  1: u'ESC', 2: u'1',     3: u'2',   4: u'3',     5: u'4',     6: u'5',   7: u'6',   8: u'7',    9: u'8',
    10: u'9', 11: u'0',  12: u'-',    13: u'=',  14: u'BKSP', 15: u'TAB',  16: u'Q',   17: u'W', 18: u'E',   19: u'R',
    20: u'T', 21: u'Y',  22: u'U',    23: u'I',  24: u'O',    25: u'P',    26: u'[',   27: u']', 28: u'CRLF',29: u'LCTRL',
    30: u'A', 31: u'S',  32: u'D',    33: u'F',  34: u'G',    35: u'H',    36: u'J',   37: u'K', 38: u'L',   39: u';',
    40: u'"', 41: u'`',  42: u'LSHFT',43: u'\\', 44: u'Z',    45: u'X',    46: u'C',   47: u'V', 48: u'B',   49: u'N',
    50: u'M', 51: u',',  52: u'.',    53: u'/',  54: u'RSHFT',56: u'LALT',100: u'RALT'
}
keypad_name='Adafruit ItsyBitsy 32u4 5V 16MHz'

# just for debug: retrieve HID device list and locate where keypad is
devices = [InputDevice(fn) for fn in evdev.list_devices()]
deviceName = ""
for device in devices:
  if device.name == keypad_name:
    deviceName=device.fn
    # print(device.fn, device.name, device.phys)

if deviceName == "":
    print("Cannot locate USB Keypad. Abort")
    exit()

device = InputDevice(deviceName)
print(device)

# read loop
for event in device.read_loop():
    # handle only key events
    if event.type == ecodes.EV_KEY:
        data = evdev.categorize(event)  # Save the event temporarily to introspect it
        # handle only key down events
        if data.keystate == 1:
            #extract and print scan code
            key_lookup = scancodes.get(data.scancode) or u'UNKNOWN:{}'.format(data.scancode)  # Lookup or return UNKNOWN:XX
            print ( u'You Pressed the {} key!'.format(key_lookup))  # Print it all out!
