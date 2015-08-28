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
import RPi.GPIO as GPIO
import uinput

#HARDWARE SETUP

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
#
# ?Operation=chronoEvent&Type=crono_rec&TimeStamp=150936&Source=chrono_2&Session=2&Prueba=11&Jornada=81&Manga=19&Tanda=49&Value=150936

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
