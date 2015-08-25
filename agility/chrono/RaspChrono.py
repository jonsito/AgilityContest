#!/usr/bin/python3
#######################################################################
# monitorize GPIO pins and translate state changes into keyboard events
#
# Based in gpiokeys.py from "Raspberry PI CookBook for python programmers"
# Notice: uinput kernel module must be modprobe'd before calling this program
#
import time
import RPi.GPIO as GPIO
import uinput

#HARDWARE SETUP

# AgilityContest chrono mapping keys from chrono.js
### Course learning
# we can use any of these keys to start/stop course learning
# KEY_0 : Course learning end
# KEY_7 : Course learning start ( 7 minutes )

#### Send data to Agility Contest
# Reserved, but not used: as 2.0 version, these function should be performed from tablet.
# KEY_F : Fault
# KEY_R : Refusal
# KEY_T : Touch
# KEY_E : Eliminated
# KEY_N : Not Present

#### Chrono related functions
# KEY_BACKSPACE : Reset chrono
# KEY_HOME  : Start chrono count
# KEY_END   : End chrono
# KEY_PAUSE : Mark Intermediate time
# KEY_ENTER : Start/Stop
# KEY_S     : Alternate Start/Stop

##### GPIO PIN Assignment
# as stated above, data related events are not generated, and have no assigned pin
GP_0     =  7   # BCM 4
GP_7     =  11  # BCM 17
GP_Reset =  13  # BCM 27
GP_Start =  15  # BCM 22
GP_Stop =   18  # BCM 24
GP_Int =    22  # BCM 25

DEBUG=True
BTN = [ GP_0,   GP_7,   GP_Reset,   GP_Start,   GP_Stop,    GP_Int ]
MSG = [ "BeginRec","EndRec","Reset","Start","Stop","Intermediate"]

#Setup the DPad module pins and pull-ups
def ac_gpio_setup():
	# set up the wiring by pin socket number instead of BroadCom pinout number
	GPIO.setmode(GPIO.BOARD)
	# set BTN ports as INPUTS
	for val in BTN:
		# Set up GPIO input with pull up control
		# ( pull_up_down can be: PUD_OFF -def-, PUD_UP or PUD_DOWN )
		GPIO.setup(val, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def main():
	# Setup uinput. Notice that Start and Stop pins are mapped to same (Start/Stop) Key, to allow reversal of first/last jump
	events = ( uinput.KEY_0,    uinput.KEY_7,   uinput.KEY_BACKSPACE,   uinput.KEY_ENTER,   uinput.KEY_ENTER,   uinput.KEY_PAUSE )
	device = uinput.Device(events)
	# let the kernel to take enought time to create user input device and initialize
	time.sleep(3) # seconds
	ac_gpio_setup()
	btn_state = [False,False,False,False,False,False]
	key_state = [False,False,False,False,False,False]
	while True:
		# Catch all the buttons pressed before pressing related keys
		for idx, val in enumerate(BTN):
			if GPIO.input(val) == False:
				btn_state[idx]=True
			else:
				btn_state[idx]=False

		# Perform the button presses/releases,( but only change state once )
		for idx, val in enumerate(btn_state):
			if val == True and key_state[idx] == False:
				if DEBUG: print (str(val) + ":" + MSG[idx] + " Press")
				device.emit(events[idx],1) # press
				key_state[idx]=True
			elif val == False and key_state[idx] == True:
				if DEBUG: print (str(val) + ":" + MSG[idx] + " Release")
				device.emit(events[idx],0) # release
				key_state[idx]=False

        # Note that due to next line time precission becomes to cents of second
        # So need to rewrite this code in an event driven way
		time.sleep(.01) # enought to bypass bounce buttons

try:
	main()
finally:
	GPIO.cleanup()
# End