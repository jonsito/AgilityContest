#!/usr/bin/python3
#######################################################################
# Monitorize Raspberry PI GPIO pins and translate state changes into
# AgilityContest Chrono keyboard event keys
#
# Based in gpiokeys.py from "Raspberry PI CookBook for python programmers"
# and code from http://raspberrywebserver.com/gpio/using-interrupt-driven-gpio.html
#
# Notice: uinput kernel module must be modprobe'd before calling this program
#
import time
import json
import urllib2
import RPi.GPIO as GPIO
import uinput

from datetime import datetime
from datetime import timedelta

##### GPIO PIN Assignment

# Chrono action buttons
GP_0     =   3  # GPIO 02
GP_7     =   5  # GPIO 03
GP_Reset =   7  # GPIO 04
GP_Start =  11  # GPIO 17
GP_Stop  =  13  # GPIO 27
GP_Int   =  15  # GPIO 22

# Session ID 00..11 -> 1st, 2nd, 3rd, 4th available Ring session ( default 2) TODO: to be discovered
GP_SES0  =  22  # GPIO 25
GP_SES1  =  24  # GPIO 08

# Output led
GP_LED   =  26  # GPIO 07

# AgilityContest chrono json request based sensor monitor
#Request to server are made by sending json request to:
#
# http://ip.addr.of.server/agility/server/database/eventFunctions.php
#
# Parameter list
# Operation=chronoEvent
# Type= one of : ( from server/database/Eventos.php )
#		'crono_start',	// Arranque Crono electronico
#		'crono_int',	// Tiempo intermedio Crono electronico
#		'crono_stop',	// Parada Crono electronico
#		'crono_rec',	// comienzo/fin del reconocimiento de pista
#		'crono_dat',    // Envio de Falta/Rehuse/Eliminado desde el crono
#		'crono_reset',	// puesta a cero del contador
# Session= Session ID to join. "2" means normally ring 1
# Source= Chronometer ID. should be in form "chrono_sessid"
# Value= Timestamp. Number of milliseconds since chrono started to run
# Timestamp= Timestamp. same value as "Value" ( obsoleted, but still needed )
#
# ?Operation=chronoEvent&Type=crono_rec&TimeStamp=150936&Source=chrono_2&Session=2&Value=150936
# data = json.load( urllib2.urlopen('http://ip.addr.of.server/agility/server/database/eventFunctions.php') + arguments )

##### default server config
SERVER = "192.168.122.1"	# TODO: auto detect
SESSION_ID = 2				# TODO: read from GPIO Switches
SESSION_NAME = "Chrono_2"	# should be generated from evaluated session ID
DEBUG=True

# buttons array and their names
BTN = [ GP_0,   GP_7,   GP_Reset,   GP_Start,   GP_Stop,    GP_Int ]
MSG = [ "BeginRec","EndRec","Reset","Start","Stop","Intermediate"]
# current button on/off and key press/release status
btn_state = [False,False,False,False,False,False]
key_state = [False,False,False,False,False,False]

# store datetime from program start
start_time = datetime.now()

# returns the elapsed milliseconds since the start of the program
def millis():
   dt = datetime.now() - start_time
   ms = (dt.days * 24 * 60 * 60 + dt.seconds) * 1000 + dt.microseconds / 1000.0
   return ms

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

	timestamp= millis()
	# Perform the button presses/releases,( but only change state once )
	for idx, val in enumerate(btn_state):
		if val == True and key_state[idx] == False:
			if DEBUG: print (str(val) + ":" + MSG[idx] + " Press")
			# compose json request
			args = ?Operation=chronoEvent&Type=crono_rec&TimeStamp=150936&Source=chrono_2&Session=2&Value=150936
			url="http://"+SERVER+"/agility/server/database/eventFunctions.php"
			# send request . It is safe to ignore response
			json.load( urllib2.urlopen( url + args ) )
			key_state[idx]=True
		elif val == False and key_state[idx] == True:
			if DEBUG: print (str(val) + ":" + MSG[idx] + " Release")
			# no action needed on release
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
	GPIO.add_event_detect(GP_0,     GPIO.FALLING, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_7,     GPIO.FALLING, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Reset, GPIO.FALLING, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Start, GPIO.FALLING, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Stop,  GPIO.FALLING, callback=event_handler, bouncetime=1000)
	GPIO.add_event_detect(GP_Int,   GPIO.FALLING, callback=event_handler, bouncetime=1000)

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
