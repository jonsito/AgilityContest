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

# LED assignment pin Number =  # gpio number - BreakoutBoard ID
LED_Rec	=	3	# 8 - LED_1	// Reconocimiento de pista
LED_Run =	5	# 9 - LED_2	// Crono running
LED_Int =	7	# 7 - LED_3	// Intermediate time
LED_Err	=	26	# 11 - LED_4	// Sensor error

LED_Sel1=	24	# 10 - LED_5	// Selected Ring MSB
LED_Sel0=	21	# 13 - LED_6	// Selected Ring LSB

LED_Btn =	19	# 12 - LED_7	// Button Pressed

LED_Pwr	=	23	# 14 - LED_8	// (Flashing) PWR On

# Chrono action buttons

BTN_Rec1 = 10	# 16 - Button_1 //	Start Reconocimiento
BTN_Rec2 = 11	# 0  - Button_2 //	End Reconocimiento
BTN_Sel1 = 12	# 1  - Button_3 //	Ring selection MSB
BTN_Reset= 13	# 2  - Button_4 //	Reset Chrono
BTN_Start= 15	# 3  - Button_5 //	Start chrono
BTN_Stop = 16	# 4  - Button_6 //	End chrono
BTN_Sel0 = 18	# 5  - Button_7 //	Ring selection LSB
BTN_Inter= 22	# 6  - Button_8 //	Intermediate Chrono

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

#countdown var to control LED_Btn status
button_state = 0

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
	# compose json request
	args = "?Operation=chronoEvent&Type="+type+"&TimeStamp="+str(val)+"&Source=" +SESSION_NAME
	args = args + "&Session="+str(ring)+"&Value="+str(val)
	url="http://"+SERVER+"/agility/server/database/eventFunctions.php"
	print "JSON Request: " + url + "" + args
	# send request . It is safe to ignore response
	#json.load( urllib2.urlopen( url + args ) )

# indica que se ha pulsado boton
def button_pressed(val,gpio):
	global button_state
	if val != 0 :
		print "Pressed GPIO:"+str(gpio)
		button_state = val

	if button_state != 0 :
		RPIO.output(LED_Btn,True)
		button_state = button_state - 1

# Reconocimiento de pista / Fin de reconocimiento
def handle_rec(pin):
	button_pressed(2,pin) # mark key pressed
	state = RPIO.input(LED_Rec) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	RPIO.output(LED_Rec, not state )
	#and send event to server
	json_request("crono_rec")

# Reset del cronometro
def handle_reset(pin):
	button_pressed(2,pin)
	json_request("crono_reset")

# Arranque / parada del cronometro
def handle_startstop(pin):
	button_pressed(2,pin)
	state = RPIO.input(LED_Run) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	RPIO.output(LED_Run, not state )
	#and send event to server
	if state == True :
		json_request("crono_stop")

	if state == False :
		json_request("crono_start")


# Tiempo intermedio
def handle_int(pin):
	button_pressed(2,pin)
	json_request("crono_int")

#Setup the DPad module pins and pull-ups
def ac_gpio_setup():
	global ring
	# set up leds
	leds = ( LED_Rec, LED_Run, LED_Int, LED_Err, LED_Sel1, LED_Sel0, LED_Btn, LED_Pwr )
	names = ( "Rec", "Run", "Interm.", "Error", "Sel1", "Sel0", "Button", "Power" )
	numbers = ( "1", "2","3","4","5","6","7","8" )
	gpios = ( "8", "9","7","11","10","13","12","14" )
	for led, name, number,gpio in zip(leds,names,numbers,gpios):
		print "Led:"+ number + " PIN:" + str(led) + " - GPIO:"+gpio +" - "+ name
		RPIO.setup(led, RPIO.OUT) # set up as output
		RPIO.output(led, RPIO.LOW) # turn off

	# set up buttons
	buttons = ( BTN_Rec1, BTN_Rec2,  BTN_Sel1, BTN_Reset,BTN_Start, BTN_Stop, BTN_Sel0, BTN_Inter )
	names = ( "StartRec", "EndRec", "Sel1.", "Reset", "Start", "Stop", "Sel0","Intermediate" )
	numbers = ( "1", "2","3","4","5","6","7","8" )
	gpios = ( "16", "0","1","2","3","4","5","6" )
	for button,name,number,gpio in zip(buttons,names,numbers,gpios):
		print "Button: "+ number + " PIN:" +str(button) + " - GPIO:"+gpio +" - "+ name
		RPIO.setup(button, RPIO.IN) # set up as input;

	time.sleep(.1)
	# read ring information. Notice that pull-up makes default to be "11"
	ring = 0x03 ^ ( ( RPIO.input(BTN_Sel1) << 1 ) | RPIO.input(BTN_Sel0) )
	print "Session Ring: "+str(ring+2) #defaults to "2"


def main():
	global button_state
	# Setup breakout board
	RPIO.setmode(RPIO.BOARD)
	ac_gpio_setup()
	# add event listeners
	RPIO.setmode(RPIO.BCM)
	# listen for events and share callback. NOTICE: Seems that add_interrupt callback needs gpio number, not pin number :-(
	print "add callback for Rec1"
	#RPIO.add_interrupt_callback(16,handle_rec,		edge='falling',	pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	print "add callback for Rec2"
	#RPIO.add_interrupt_callback(0, handle_rec,		edge='falling',	pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	print "add callback for Intermediate"
	#RPIO.add_interrupt_callback(2, handle_int,		edge='falling',	pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	print "add callback for Reset"
	#RPIO.add_interrupt_callback(3, handle_reset,	edge='falling',	pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	print "add callback for Start"
	#RPIO.add_interrupt_callback(4, handle_startstop, edge='falling',pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	print "add callback for Stop"
	RPIO.add_interrupt_callback(6, handle_startstop, edge='falling',pull_up_down=RPIO.PUD_UP, threaded_callback=True, debounce_timeout_ms=100)
	time.sleep(0.1)
	print "wait for interrupts"
	RPIO.wait_for_interrupts(threaded=True)  # start waiting in a separate thread to allow continue execution

	# and enter into infinite loop setting handling buttonPressed and Power Leds
	while True:
		button_pressed(0,0)
		RPIO.output(LED_Pwr,True)
		time.sleep(1)
		button_pressed(0,0)
		RPIO.output(LED_Pwr,False)
		time.sleep(1)

try:
	main()
finally:
	RPIO.cleanup()
# End
