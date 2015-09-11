#!/usr/bin/python3
#######################################################################
# Monitorize Raspberry PI GPIO pins and translate state changes into
# AgilityContest Chrono web page keyboard event keys
#
# THIS IS AN ALTERNATE BUGGY WAY TO TRANSLATE GPIO's TO KEYS to for working
# with AC Chrono web page when viewed from Raspberry PI
#
# USE ONLY FOR TESTING.
# USE ONLY FOR TESTING
# USE ONLY FOR TESTING.
# USE ONLY FOR TESTING
# USE ONLY FOR TESTING.
# USE ONLY FOR TESTING
# USE ONLY FOR TESTING.
# USE ONLY FOR TESTING
# USE ONLY FOR TESTING.
# USE ONLY FOR TESTING
#
# Based in gpiokeys.py from "Raspberry PI CookBook for python programmers"
# and code from http://raspberrywebserver.com/gpio/using-interrupt-driven-gpio.html
#
# Notice: uinput kernel module must be modprobe'd before calling this program
#(revisar lista de paquetes)
# sudo apt-get install python3 python3-pip python3-gpio
# sudo apt-get install libudev-dev
#
# compile from sources
# git clone https://github.com/tuomasjjrasanen/python-uinput.git
# sudo python setup.py install --prefix=/usr/local
#
# install from pip
# bash$ sudo pip-3.2 install python-uinput
#
# configure system
# bash$ sudo modprobe uinput
# bash$ sudo sh -c 'echo "uinput" >> /etc/modules'
# bash$ sudo echo 'SUBSYSTEM=="misc", KERNEL=="uinput", MODE="0660", GROUP="uinput"' > /etc/udev/rules.d/40-uinput.rules
# bash$ sudo addgroup uinput
# bash$ sudo adduser $USER uinput
# bash$ sudo reboot
#
# # configure X windows
# bash$ sudo mkdir -p /etc/X11/xorg.conf.d
# bash$ sudo vi /etc/X11/xorg.conf.d/00-python-uinput.conf
# Section "InputClass"
# 	Identifier	"AgilityContest chrono keyboard"
# 	MatchProduct	"python-uinput"
# 	Driver		"evdev"
# EndSection


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
# KEY_I	    : Mark Intermediate time
# KEY_ENTER : Start/Stop
# KEY_S     : Alternate Start/Stop

##### GPIO PIN Assignment
# as stated above, data related events are not generated, and have no assigned pin
GP_0     =  7   # BCM 04
GP_7     =  11  # BCM 17
GP_Reset =  13  # BCM 27
GP_Start =  15  # BCM 22
GP_Stop  =  18  # BCM 24
GP_Int   =  22  # BCM 25
GP_LED   =  26  # BCM 07

DEBUG=True
BTN = [ GP_0,   GP_7,   GP_Reset,   GP_Start,   GP_Stop,    GP_Int ]
MSG = [ "BeginRec","EndRec","Reset","Start","Stop","Intermediate"]

# current button on/off and key press/release status
btn_state = [False,False,False,False,False,False]
key_state = [False,False,False,False,False,False]

# Event handler. By default, on interrupt poll state on all buttons
def event_handler(pin):
	global btn_state
	global key_state
	global device
	global events
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

#Setup the DPad module pins and pull-ups
def ac_gpio_setup():
	# set up the wiring by pin socket number instead of BroadCom pinout number
	GPIO.setmode(GPIO.BOARD)
	# Set GP_LED as output
	GPIO.setup(GP_LED, GPIO.OUT)
	# set BTN ports as INPUTS
	for val in BTN:
		# Set up GPIO input with pull up control
		# ( pull_up_down can be: PUD_OFF -def-, PUD_UP or PUD_DOWN )
		GPIO.setup(val, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def main():
	# Setup uinput. Notice that Start and Stop pins are mapped to same (Start/Stop) Key, to allow reversal of first/last jump
	global events
	global device
	events = ( uinput.KEY_0,    uinput.KEY_7,   uinput.KEY_BACKSPACE,   uinput.KEY_ENTER,   uinput.KEY_ENTER,   uinput.KEY_I )
	device = uinput.Device(events)
	# let the kernel to take enought time to create user input device and initialize
	time.sleep(3) # seconds
	ac_gpio_setup()

	# listen for events and share callback. Use 1s for button bounce time
	GPIO.add_event_detect(GP_0,     GPIO.BOTH, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_7,     GPIO.BOTH, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Reset, GPIO.BOTH, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Start, GPIO.BOTH, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Stop,  GPIO.BOTH, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Int,   GPIO.BOTH, callback=event_handler, bouncetime=1000)

	# and enter into infinite loop setting led on/off
	count = 0
	while True:
		GPIO.output(GP_LED,True)
		time.sleep(1)
		GPIO.output(GP_LED,False)
		time.sleep(1)
		count += 1
		print ( count )

try:
	main()
finally:
	GPIO.cleanup()
# End
