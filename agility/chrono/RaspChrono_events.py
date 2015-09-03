#!/usr/bin/python3
#######################################################################
# Monitorize Raspberry PI GPIO pins and translate state changes into
# AgilityContest Chrono keyboard event keys
#
# we are using LED&Button breakout board from AdaFruit for testing and led&button assignment
# http://www.modmypi.com/raspberry-pi/breakout-boards/mypishop/mypi-push-your-pi-8-led-and-8-button-breakout-board
#
#####################################################################
#                                                                   #
#   Led1    Led2    Led3    Led4    Led5    Led6    Led7    Led8    #
#   Rec     Run     Int     Err     Sel1    Sel0    Btn     Pwr     #
#                                                                   #
#       Btn1            Btn2            Btn3            Btn4        #
#       Rec1            Rec2            Sel1            Rst         #
#                                                                   #
#       Btn5            Btn5            Btn7            Btn8        #
#       Start           Stop            Sel0            Int         #
#                                                                   #
#####################################################################

import json
import urllib2
import RPIO
import datetime
import time

##### GPIO PIN Assignment
# WARNING: this pinout is only valid in RPi Models B+ and 2. 
# Either models A,Brev1,and Brev2 have different pinout meaning for some gpios (

# LED assignment GPIO Number =  # pin number - BreakoutBoard ID
LED_Rec	=	8	# 3 - LED_1	// Reconocimiento de pista
LED_Run =	9	# 5 - LED_2	// Crono running
LED_Int =	7	# 7 - LED_3	// Intermediate time
LED_Err	=	11	# 26 - LED_4	// Sensor error

LED_Sel1=	10	# 24 - LED_5	// Selected Ring MSB
LED_Sel0=	13	# 21 - LED_6	// Selected Ring LSB

LED_Btn =	12	# 19 - LED_7	// Button Pressed

LED_Pwr	=	14	# 23 - LED_8	// (Flashing) PWR On

# Chrono action buttons

BTN_Rec1 = 16	# 10 - Button_1 //	Start Reconocimiento
BTN_Rec2 = 0	# 11 - Button_2 //	End Reconocimiento

BTN_Sel1 = 1	# 12 - Button_3 //	Ring selection MSB
BTN_Sel0 = 5	# 18 - Button_7 //	Ring selection LSB

BTN_Reset= 2	# 13 - Button_4 //	Reset Chrono
BTN_Start= 3	# 15 - Button_5 //	Start chrono
BTN_Inter= 6	# 22 - Button_8 //	Intermediate Chrono
BTN_Stop = 4	# 16 - Button_6 //	End chrono

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
SERVER = "192.168.122.1"	# TODO: auto detect by mean to iterate "connect" operation on every subnet IP
SESSION_ID = 2			# TODO: retrieve from server and evaluate according GPIO Sel[10] Switches
SESSION_NAME = "Chrono_2"	# should be generated from evaluated session ID
DEBUG=True

# Button used during event processing
BTN = [ BTN_Rec1, BTN_Rec2, BTN_Reset, BTN_Start, BTN_Stop, BTN_Inter ]

button_pressed = 0

# store datetime from program start
start_time = datetime.datetime.now()
ring = SESSION_ID

# returns the elapsed milliseconds since the start of the program
def millis():
   dt = datetime.now() - start_time
   ms = (dt.days * 24 * 60 * 60 + dt.seconds) * 1000 + dt.microseconds / 1000.0
   return ms

def json_reques(type):
	global ring
	val = millis()
	print "Request:'" + type+ "' Session:"+ str(ring) + " Value:" + str(val)
	# compose json request
	args = "?Operation=chronoEvent&Type="+type+"&TimeStamp="+str(val)+"&Source=" +SESSION_NAME
	args = args + "&Session="+str(ring)+"&Value="+str(val)
	url="http://"+SERVER+"/agility/server/database/eventFunctions.php"
	# send request . It is safe to ignore response
	#json.load( urllib2.urlopen( url + args ) )

# indica que se ha pulsado boton
def button_pressed(val):
	global button_state
	if val != 0 :
		button_state = val

	if button_state != 0 :
		RPIO.output(LED_Btn,True)
		button_state = button_state - 1

# Reconocimiento de pista / Fin de reconocimiento
def handle_rec(pin):
	button_pressed(2) # mark key pressed
	state = RPIO.input(LED_Rec) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	RPIO.output(LED_Rec, not state )
	#and send event to server
	json_request("crono_rec")

# Reset del cronometro
def handle_reset(pin):
	button_pressed(2)
	json_request("crono_reset")

# Arranque / parada del cronometro
def handle_startstop(pin):
	button_pressed(2)
	state = RPIO.input(LED_Run) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	RPIO.output(LED_Run, not state )
	#and send event to server
	if state == True :
		json_request("crono_stop")

	if state == False :
		json_request("crono_start")


# Tiempo intermedio
def handle_int(pin):
	button_pressed(2)
	json_request("crono_int")

#Setup the DPad module pins and pull-ups
def ac_gpio_setup():
	global ring
	# set up leds
	leds = ( LED_Rec, LED_Run, LED_Int, LED_Err, LED_Sel1, LED_Sel0, LED_Btn, LED_Pwr )
	lnames = ( "Rec", "Run", "Interm.", "Error", "Sel1", "Sel0", "Button", "Power" )
	for idx in range (0,7):
		print "Led:"+ str(idx+1) + " GPIO:" + str(leds[idx]) + " - " + lnames[idx]
		RPIO.setup(leds[idx], RPIO.OUT) # set up as output
		RPIO.output(leds[idx], False) # turn off

	# set up buttons
	buttons = ( BTN_Rec1, BTN_Rec2,  BTN_Sel1, BTN_Sel0, BTN_Reset, BTN_Start, BTN_Inter,  BTN_Stop )
	for button in buttons:
		print "Button: "+ str(button)
		RPIO.setup(button, RPIO.IN, pull_up_down=RPIO.PUD_UP ) # set up as input; default pull up
	time.sleep(.1)
	# read ring information. Notice that pull-up makes default to be "11"
	ring = 0x03 ^ ( ( RPIO.input(BTN_Sel1) << 1 ) | RPIO.input(BTN_Sel0) )


def main():
	global button_state
	# set up the wiring by BroadCom pinout number instead of pin socket number
	RPIO.setmode(RPIO.BCM) # seems that RPIO does not properly handle RPIO.BOARD
	# Setup breakout board
	ac_gpio_setup()
	# listen for events and share callback. Use 1s for button bounce time
	print "add callback for Rec1"
	RPIO.add_interrupt_callback(BTN_Rec1,  handle_rec, edge='falling', debounce_timeout_ms=100)
	print "add callback for Rec2"
	RPIO.add_interrupt_callback(BTN_Rec2,  handle_rec, edge='falling', debounce_timeout_ms=100)
	print "add callback for Intermediate"
	RPIO.add_interrupt_callback(BTN_Inter, handle_int, edge='falling', debounce_timeout_ms=100)
	print "add callback for Reset"
	RPIO.add_interrupt_callback(BTN_Reset, handle_reset, edge='falling', debounce_timeout_ms=100)
	print "add callback for Start"
	RPIO.add_interrupt_callback(BTN_Start, handle_startstop, edge='falling', debounce_timeout_ms=100)
	print "add callback for Stop"
	RPIO.add_interrupt_callback(BTN_Stop,  handle_startstop, edge='falling', debounce_timeout_ms=100)
	print "wait for interrupts"
	RPIO.wait_for_interrupts(Threaded=True)	

	# and enter into infinite loop setting handling buttonPressed and Power Leds
	while True:
		button_pressed(0)
		RPIO.output(LED_Pwr,True)
		time.sleep(1)
		button_pressed(0)
		RPIO.output(LED_Pwr,False)
		time.sleep(1)

try:
	main()
finally:
	RPIO.cleanup()
# End
