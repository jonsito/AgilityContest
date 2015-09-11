#!/usr/bin/python3
#RaspChrono_events.py
#
# Copyright 2013-2015 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
#
# This program is free software; you can redistribute it and/or modify it under the terms
# of the GNU General Public License as published by the Free Software Foundation;
# either version 2 of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
# without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along with this program;
# if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
#

#######################################################################
# Monitorize Raspberry PI GPIO pins and translate state changes into
# AgilityContest Chrono Event messages using AgilityContest Chrono API Protocol
#
# THIS IS A REFERENCE IMPLEMENTATION. DO NOT USE IN REAL ENVIRONMENTS
# THIS IS A REFERENCE IMPLEMENTATION. DO NOT USE IN REAL ENVIRONMENTS
# THIS IS A REFERENCE IMPLEMENTATION. DO NOT USE IN REAL ENVIRONMENTS
# THIS IS A REFERENCE IMPLEMENTATION. DO NOT USE IN REAL ENVIRONMENTS
#
# Reasons:
# - written in python. an interpreted language -> cannot guarantee response time and accuracy
# - runs on a rasperry: has no realtime clock, and poor clock stability in the long time running
# - requires an specific hardware.
#
# We are using LED&Button breakout board from AdaFruit for testing and led&button assignment
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
import threading            # to receive event messages from server
import json					# to parse Data field on event responses
import requests 			# to handle json http requests
import RPi.GPIO as GPIO		# to handle Raspberry PI GPIO pins
import datetime
import time					# to get and process timestamps
import netifaces as ni		# to discover ip address/network/netmask
import ipaddress            # to deal with IPv4 addresses

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

# use them as array in server search process
LEDS=(LED_Rec,LED_Run,LED_Int,LED_Err,LED_Sel1,LED_Sel0,LED_Btn,LED_Pwr)

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
# Session= Session ID to join. You should retrieve a list of available session ID's from server
# Source= Chronometer ID. should be in form "chrono_sessid"
# Value= Timestamp. Number of milliseconds since this application started running
# Timestamp= Timestamp. same value as "Value" ( obsoleted, but still needed )
#
# ?Operation=chronoEvent&Type=crono_rec&TimeStamp=150936&Source=chrono_2&Session=2&Value=150936
# data = json.load( urllib.urlopen('httsp://ip.addr.of.server/agility/server/database/eventFunctions.php') + arguments, verify=False )

##### Some constants
SESSION_NAME = "Chrono_2"	# should be generated from evaluated session ID
DEBUG=True
ETH_DEVICE='eth0'		# this should be modified if using Wifi (!!NOT RECOMMENDED AT ALL!!)

# some global variables
server = "192.168.1.35"		# to be evaluated later by querying network
button_state = 0			# countdown var to control LED_Btn status
start_time = datetime.datetime.now()	# to store datetime from program start
session_id = 0		        # to be retrieved from server and evaluate according GPIO Sel[10] Switches
open_time = 0				# seconds since last closed state detected on start/stop/int sensor
start_run = 0				# to store crono_start event timestamp
intermediate_time = 0		# seconds since Intermediate time received

def debug(str):
	global DEBUG
	if DEBUG==True:
		print (str)


# returns the elapsed milliseconds since the start of the program
def millis():
   dt = datetime.datetime.now() - start_time
   ms = (dt.days * 24 * 60 * 60 + dt.seconds) * 1000 + dt.microseconds / 1000.0
   return int(ms)

# change power led status, to get it blinking
def blink_powerled():
	state = GPIO.input(LED_Pwr) # read led status
	GPIO.output(LED_Pwr, not state )
		
# perform json request to send event to server
def json_request(type,value):
	global session_id
	# compose json request
	args = "?Operation=chronoEvent&Type="+type+"&TimeStamp="+str(millis())+"&Source=" +SESSION_NAME
	args = args + "&Session=" + session_id + "&Value="+value
	url="https://"+server+"/agility/server/database/eventFunctions.php"
	# debug( "JSON Request: " + url + "" + args)
	response = requests.get(url+args, verify=False)	# send request . It is safe to ignore response

# use leds as progress bar. on value<0 turn all of them off
def kitt(value):
	for i in range(8):
		GPIO.output(LEDS[i],False) # turn off all leds
	if (value >= 0):
		GPIO.output( LEDS[value%8],True) # turn on requested led
	return value+1

# scan local network to look for server
def lookForServer():
	global server
	global ETH_DEVICE
	global session_id
	rings = ["2","3","4","5"] # array of session id's received from server To be re-evaluated later from server response
	# look for IPv4 addresses on ETH_DEVICE [0]->use first IPv4 address found on this interface
	netinfo=ni.ifaddresses(ETH_DEVICE)[ni.AF_INET][0]
	# iterate on every hosts on this network/netmask. Use strict=False to ignore ip address host bits
	count=0
	for i in ipaddress.IPv4Network(netinfo['addr']+"/"+netinfo['netmask'],strict=False).hosts():
		count = kitt(count)
		ip = str(i)
		debug( "Looking for server at: " + ip)
		try:
			args= "?Operation=selectring"
			response = requests.get("https://" + ip + "/agility/server/database/sessionFunctions.php"+args,verify=False,timeout=0.5)
		except requests.exceptions.RequestException as ex:
			# debug ( "Http request error:" + str(ex) )
			continue
		# if response failed, try next IP address
		if response.status_code != 200:
			continue
		# response ok
		data=response.json()
		# store server
		server = ip
		# retrieve session id for each ring
		for id in range (0,3):
			rings[id]=data['rows'][id]['ID']
		# clear leds and return
		debug ("Found AgilityContest server at IP address: "+ip)
		kitt(-1)
		break
	else:
		# arriving here means server not found
		session_id=0 # invalid sid
		return "0.0.0.0"
	# on received answer retrieve Session ID from declared rings
	# read ring information. Notice that pull-up makes default to be "11"
	ring = 0x03 ^ ( ( GPIO.input(BTN_Sel1) << 1 ) | GPIO.input(BTN_Sel0) )
	session_id = rings[ring];
	debug( "Ring: "+str(ring)+ " Session ID: "+str(session_id) )
	# and finally setup server IP
	return server

def handle_intermediate_time():
	global intermediate_time
	state = GPIO.input(LED_Int) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	if state == True:
		intermediate_time = intermediate_time -1
	if intermediate_time <= 0:
		GPIO.output(LED_Int,False) # end of countdown: turn of led
		intermediate_time = 0 # not really needed, but...

# take care on how much time a button has been pressed
# return True on success, False on sensor error
def check_sensors():
	global open_time
	# remember that pull-ups let buttons high as iddle state
	state = GPIO.input(BTN_Start) and GPIO.input(BTN_Stop) and GPIO.input(BTN_Inter)
	if state == True: # no hay nada pulsado: all right
		GPIO.output(LED_Err,False)
		if open_time!=0:
			json_request("crono_error","0") # mark error solved
		open_time=0
		return True
	# hay algo pulsado: incrementa contador y comprueba si ha llegado al limite
	open_time = open_time + 1
	if open_time == 10: # send error to server
		json_request("crono_error","1")
	if open_time>=10:
		debug("ERROR: Comprobar sensores")
		return False
	else:
		GPIO.output(LED_Err,False)
		return True

# indica que se ha pulsado boton
def button_pressed(val,pin,txt):
	global button_state
	if (button_state==0) and (val==0): # end of countdown
		GPIO.output(LED_Btn,False)
		return False
	if (button_state==0) and (val!=0): # ready for key: accept
		GPIO.output(LED_Btn,True)
		debug( "Pressed PIN:" + str(pin) + " - " + txt )
		button_state = val
		return True
	if (button_state!=0) and (val==0): # countdown 
		GPIO.output(LED_Btn,True)
		button_state = button_state - 1
		return False
	if (button_state!=0) and (val!=0): # not ready for key: ignore
		GPIO.output(LED_Btn,True)
		return False
	
# Reconocimiento de pista / Fin de reconocimiento
def handle_rec(pin):
	if not button_pressed(1,pin,"Reconocimiento"):
		return False
	return json_request("crono_rec",str(millis()))

# Reset del cronometro
def handle_reset(pin):
	if not button_pressed(1,pin,"Reset"):
		return False
	return json_request("crono_reset",str(millis()))

# Arranque / parada del cronometro
def handle_startstop(pin):
	if not button_pressed(1,pin,"Start/Stop"):
		return False
	state = GPIO.input(LED_Run) # read led status (oh, yeah: it's an output, but rpi allows us to do this )
	#and send event to server
	if state == True :
		return json_request("crono_stop",str(millis()))
	if state == False :
		return json_request("crono_start",str(millis()))

# Tiempo intermedio
def handle_int(pin):
	if not button_pressed(1,pin,"T. Intermedio"):
		return False
	return json_request("crono_int",str(millis()))

#Setup the DPad module pins and pull-ups
def ac_gpio_setup():
	# set up leds
	leds = ( LED_Rec, LED_Run, LED_Int, LED_Err, LED_Sel1, LED_Sel0, LED_Btn, LED_Pwr )
	names = ( "Rec", "Run", "Interm.", "Error", "Sel1", "Sel0", "Button", "Power" )
	numbers = ( "1", "2","3","4","5","6","7","8" )
	gpios = ( "8", "9","7","11","10","13","12","14" )
	for led, name, number,gpio in zip(leds,names,numbers,gpios):
		debug( "Led:"+ number + " Pin:" + str(led) + " - GPIO:"+gpio +" - "+ name)
		GPIO.setup(led, GPIO.OUT) # set up as output
		GPIO.output(led, GPIO.LOW) # turn off

	# set up buttons
	buttons = ( BTN_Rec1, BTN_Rec2,  BTN_Sel1, BTN_Reset, BTN_Start, BTN_Stop, BTN_Sel0, BTN_Inter )
	names = ( "StartRec", "EndRec", "Sel1.", "Reset", "Start", "Stop", "Sel0","Intermediate" )
	numbers = ( "1", "2","3","4","5","6","7","8" )
	gpios = ( "16", "0","1","2","3","4","5","6" )
	for button,name,number,gpio in zip(buttons,names,numbers,gpios):
		debug( "Button: "+ number + " Pin:" +str(button) + " - GPIO:"+gpio +" - "+ name)
		GPIO.setup(button, GPIO.IN,pull_up_down=GPIO.PUD_UP) # set up as input
	time.sleep(.1)

# declare and trigger events for each button
def ac_gpio_addevents():
	# listen for events and share callback.
	debug( "add callback for Rec1")
	GPIO.add_event_detect(BTN_Rec1,	GPIO.FALLING,callback=handle_rec,	bouncetime=250)
	debug( "add callback for Rec2")
	GPIO.add_event_detect(BTN_Rec2,	GPIO.FALLING,callback=handle_rec,	bouncetime=250)
	debug( "add callback for Intermediate")
	GPIO.add_event_detect(BTN_Inter,GPIO.FALLING,callback=handle_int,	bouncetime=250)
	debug( "add callback for Reset")
	GPIO.add_event_detect(BTN_Reset,GPIO.FALLING,callback=handle_reset,	bouncetime=250)
	debug( "add callback for Start")
	GPIO.add_event_detect(BTN_Start,GPIO.FALLING,callback=handle_startstop, bouncetime=250)
	debug( "add callback for Stop")
	GPIO.add_event_detect(BTN_Stop,	GPIO.FALLING,callback=handle_startstop, bouncetime=250)
	time.sleep(0.1)

# wait for network event messages
# this method runs in a separate thread
def eventParser():
	global server
	global session_id
	global start_run
	event_id=0 # event ID of last "open" call in current session
	# call to "connect", to retrieve last event id and timeout
	debug( "Connecting event manager on server ...")
	while True:
		try:
			args = "?Operation=connect&Session="+session_id
			response = requests.get("https://" + server + "/agility/server/database/eventFunctions.php"+args, verify=False)
		except requests.exceptions.RequestException as ex:
			debug ( "Connect() error:" + str(ex) )
			time.sleep(5) # wait 5 seconds and try again
			continue
		# if response failed, try next IP address
		if response.status_code != 200:
			debug("Invalid response. Try again")
			time.sleep(5) # wait 5 seconds and retry
			continue
		# response ok: retrieve event ID of last "open" call
		data=response.json()
		if data['total'] == "0":
			time.sleep(5) # no data available. Sleep and retry
			continue
		event_id = data['rows'][0]['ID']
		break
	# connect done, now, enter in an infinite "getEvents" request loop
	debug( "Connected. Waiting for Server events ...")
	timestamp=0
	while True:
		try:
			args="?Operation=getEvents&Session=" + session_id + "&ID=" + str(event_id) + "&TimeStamp=" + str(timestamp)
			response = requests.get("https://" + server + "/agility/server/database/eventFunctions.php"+args, verify=False )
		except requests.exceptions.RequestException as ex:
			debug ( "getEvents() error:" + str(ex) )
			time.sleep(5) # wait 5 seconds and try again
			continue
		# if response failed, try next IP address
		if response.status_code != 200:
			time.sleep(5) # wait 5 seconds and retry
			continue
		# response ok: retrieve event ID of last "open" call
		data=response.json()
		if data['total'] == 0:
			time.sleep(5) # no data available. Sleep and retry
			continue
		timestamp=data['TimeStamp']
		for i in data['rows']:
			event_id=i['ID']
			type=i['Type']
			value=json.loads(i['Data'])['Value']
			debug ("Reveived Event ID:"+str(event_id)+" TimeStamp:"+str(timestamp)+ " Type:"+type+ " Value:"+str(value))
			# Eventos generales
			if type == 'null':				# null event: no action taken
				continue
			if type == 'init':				# operator starts tablet application
				continue
			if type == 'login':				# operador hace login en el sistema
				continue
			if type == 'open':				# operator selects tanda on tablet
				continue
			# eventos de crono manual
			if type == 'salida':			# juez da orden de salida ( crono 15 segundos )
				continue
			if type == 'start':				# Crono manual - value: timestamp
				continue
			if type == 'stop':				# Crono manual - value: timestamp
				continue
			# en crono electronico los campos "Value" y "TimeStamp" contienen la marca de tiempo del sistema
			# en el momento en que se capturo el evento
			if type == 'crono_start':		# Arranque Crono electronico
				start_run=value		# store timestamp mark
				GPIO.output(LED_Run, True )
				continue
			if type == 'crono_int':			# Tiempo intermedio Crono electronico
				global intermediate_time
				intermediate_time=10
				GPIO.output(LED_Int, True ) # turn led on and start countdown
				continue
			if type == 'crono_stop':		# Parada Crono electronico
				GPIO.output(LED_Run, False )
				elapsed= (value-start_run) # miliseconds
				debug("End of course. Total time: %.2f" % (elapsed/1000.0) )
				continue
			if type == 'crono_rec':			# Llamada a reconocimiento de pista
				state = GPIO.input(LED_Rec) # read led status
				GPIO.output(LED_Rec, not state )
				continue
			if type == 'crono_dat':			# Envio de Falta/Rehuse/Eliminado desde el crono
				continue
			if type == 'crono_reset':		# puesta a cero del contador
				GPIO.output(LED_Rec, False )
				GPIO.output(LED_Run, False )
				GPIO.output(LED_Int, False )
				GPIO.output(LED_Err, False )
				continue
			if type == 'crono_error':		# error en alineamiento de sensores
				if value=='1':
					GPIO.output(LED_Err, True ) # check_sensors() will turn of when solved
				continue
			# entrada de datos, dato siguiente, cancelar operacion
			if type == 'llamada':			# operador abre panel de entrada de datos
				continue
			if type == 'datos':				# actualizar datos (si algun valor es -1 o nulo se debe ignorar)
				continue
			if type == 'aceptar':			# grabar datos finales
				continue
			if type == 'cancelar':			# restaurar datos originales
				continue
			if type == 'info':				# value: message
				continue
			# eventos de cambio de camara para gestion de Live Stream
			# el campo "data" contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
			if type == 'camera':			# cambio de fuente de streaming
				continue
			# Si llega hasta aqui tenemos un error desconocido. Notificar e ignorar
			debug("Error: Unknown event type:"+type )

def main():
	# Setup breakout board
	GPIO.setmode(GPIO.BOARD)
	ac_gpio_setup()
	# look for server
	lookForServer()
	# add event listeners
	ac_gpio_addevents()
	# add thread for receive events from server
	w = threading.Thread(target=eventParser)
	w.start()


	# and enter into infinite loop setting handling buttonPressed and Power Leds
	debug( "waiting for GPIO's ...")
	while True:
		blink_powerled() # make led power blink
		button_pressed(0,0,"") # countdown keypressed led
		check_sensors() # check for permanently closed start/stop/intermediate sensors
		handle_intermediate_time() # check coundount on intermediate time led
		time.sleep(0.5) # delay and loop again

try:
	# do not throw exception on check certifcate
	requests.packages.urllib3.disable_warnings()
	main()
	
finally:
	GPIO.cleanup()
# End
