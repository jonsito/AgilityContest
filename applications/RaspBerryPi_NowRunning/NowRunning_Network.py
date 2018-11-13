#!/usr/bin/python3
#NowRunning_Network.py
#
# Copyright  2013-2018 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
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
# Class for network related functions in Raspberry NowRunning extension kit
#####################################################################
import os
import threading			# to receive event messages from server
import json					# to parse Data field on event responses
import requests 			# to handle json http requests
import datetime
import time					# to get and process timestamps
import netifaces as ni		# to discover ip address/network/netmask
import ipaddress			# to deal with IPv4 addresses
import math				 # to handle timestamps

# AgilityContest chrono json request parameter definition
# ================================================================
# Request to server are made by sending json request to:
#
# http://ip.addr.of.server/base_url/ajax/database/eventFunctions.php
#
# Parameter list
# Operation=chronoEvent
# Type= one of : ( from ajax/database/Eventos.php )
#		'crono_start'	// Arranque Crono electronico
#		'crono_int'		// Tiempo intermedio Crono electronico
#		'crono_stop'	// Parada Crono electronico
#		'crono_rec'		// comienzo/fin del reconocimiento de pista
#		'crono_dat'		// Envio de Falta/Rehuse/Eliminado desde el crono
#		'crono_reset'	// puesta a cero del contador
#		'crono_error'	// sensor error detected (Value=1) or solved (Value=0)
#		'crono_ready'	// chrono synced and listening (Value=1) or disabled (Value=0)
# Session= Session ID to join. You should select it from retrieved list of available session ID's from server
# Source= Chronometer ID. should be in form "chrono_sessid"
# Value= start/stop/int: time of event detection
#		  error: 1: detected 0:solved
# Timestamp= time mark of last event parsed as received from server
#
# example
# ?Operation=chronoEvent&Type=crono_rec&TimeStamp=150936&Source=chrono_2&Session=2&Value=150936
# data = json.load( urllib.urlopen('https://ip.addr.of.server/base_url/ajax/database/eventFunctions.php') + arguments, verify=False )

class NowRunning_Network:
	##### Some constants
	SESSION_NAME = "NowRunning_2"	# should be generated from evaluated session ID
	DEBUG=True
	ETH_DEVICE='eth0'		# this should be modified if using Wifi (!!NOT RECOMMENDED AT ALL!!)

	def kitt(self,count):
		return ( count + 1 ) % 8

	def debug(self,str):
		if NowRunning_Network.DEBUG==True:
			print (str)
		
# perform json request to send event to server
	def json_request(self,type,value):
		# compose json request
		args = "?Operation=chronoEvent&Type="+type+"&TimeStamp="+str(math.floor(millis()/1000))+"&Source=" + NowRunning_Network.SESSION_NAME
		args = args + "&Session=" + self.session_id + "&Value="+value
		url="https://"+self.server+"/"+self.baseurl+"/ajax/database/eventFunctions.php"
		# self.debug( "JSON Request: " + url + "" + args)
		response = requests.get(url+args, verify=False)	# send request . It is safe to ignore response

# scan local network to look for server
	def lookForServer(self,ring):
		rings = ["2","3","4","5"] # array of session id's received from server To be re-evaluated later from server response
		# look for IPv4 addresses on ETH_DEVICE [0]->use first IPv4 address found on this interface
		netinfo=ni.ifaddresses(NowRunning_Network.ETH_DEVICE)[ni.AF_INET][0]
		# iterate on every hosts on this network/netmask. Use strict=False to ignore ip address host bits
		count=0
		url= "/agility/ajax/database/sessionFunctions.php"
		args= "?Operation=selectring" # operation to enumerate available ring sessions
		for i in ipaddress.IPv4Network(netinfo['addr']+"/"+netinfo['netmask'],strict=False).hosts():
			count = self.kitt(count)
			ip = str(i)
			self.debug( "Looking for server at: " + ip)
			# time to look for server. To do this, we send an http request to retrieve available session rings, with
			# their ID to be evaluated according our dip-switches
			try:
				# Some stupid routers, instead of 404 in nonexistent pager requests for basic authentication
				# so take care on it by providing a fake auth, so the router fails and return 401 error
				response = requests.get("https://" + ip + url + args, verify=False, timeout=0.5, auth=('AgilityContest','AgilityContest'))
				# if response failed, try next IP address
				if response.status_code != 200:
					continue
				# response ok. Extract json message (will throw an exception on fail)
				data=response.json()
				# arriving here means server found. store server IP
				self.debug ("Found AgilityContest server at IP address: "+ip)
				self.server = ip
				# retrieve session id for each ring
				for id in range (0,3):
					rings[id]=data['rows'][id]['ID']
				# clear leds and return
				self.kitt(-1)
				break
			except requests.exceptions.RequestException as ex:
				# self.debug ( "Http request error:" + str(ex) )
				continue
		else:
			# arriving here means self.server not found
			self.session_id=0 # invalid sid
			return "0.0.0.0"
		# on received answer retrieve Session ID from requested ring
		self.session_id = rings[ring];
		self.debug( "Ring: "+str(ring)+ " Session ID: "+str(self.session_id) )
		# and finally setup server IP
		return self.server

# Reconocimiento de pista / Fin de reconocimiento
	def handle_rec(self,time):
		self.debug("Reconocimiento de pista")
		# disparar un tempporizador que vaya descontando en el marcador

# Llamada a pista
	def handle_llamada(self,data):
		self.debug("Now running: ")

	def handle_message(self,msg):
		self.debug("Sending message: " +  msg)

# parar bucle de eventos
	def stopEventParser():
		self.loop = False

# wait for network event messages
# this method runs in a separate thread
	def eventParser(self):
		event_id=0 # event ID of last "open" call in current session
		# call to "connect", to retrieve last event id and timeout
		self.debug( "Connecting event manager on server ...")
		while True:
			try:
				args = "?Operation=connect&Session="+self.session_id
				response = requests.get("https://" + self.server + "/" + self.baseurl + "/ajax/database/eventFunctions.php"+args, verify=False)
			except requests.exceptions.RequestException as ex:
				self.debug ( "Connect() error:" + str(ex) )
				time.sleep(5) # wait 5 seconds and try again
				continue
			# if response failed, try next IP address
			if response.status_code != 200:
				self.debug("Invalid response. Try again")
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
		self.debug( "Connected. Waiting for Server events ...")
		timestamp=0
		while self.loop:
			try:
				args="?Operation=getEvents&Session=" + self.session_id + "&ID=" + str(event_id) + "&TimeStamp=" + str(timestamp)
				response = requests.get("https://" + self.server + "/" + self.baseurl + "/ajax/database/eventFunctions.php"+args, verify=False )
			except requests.exceptions.RequestException as ex:
				self.debug ( "getEvents() error:" + str(ex) )
				time.sleep(5) # wait 5 seconds and try again
				continue
			# if response failed, try next IP address
			if response.status_code != 200:
				time.sleep(5) # wait 5 seconds and retry
				continue
			# response ok: retrieve event ID of last "open" call
			data=response.json()
			if data['total'] == "0":
				time.sleep(5) # no data available. Sleep and retry
				continue
			if 'TimeStamp' in data:
				timestamp=data['TimeStamp']
			else:
				self.debug ("ERROR: Reveived Event without Timestamp. ID:"+str(event_id)+" Type:"+type+ " Value:"+str(value))
			for i in data['rows']:
				event_id=i['ID']
				type=i['Type']
				evtdata=json.loads(i['Data'])
				value=0
				if 'Value' in evtdata:			# some events does not provide "Value" in data
					value=evtdata['Value']
				self.debug ("Reveived Event ID:"+str(event_id)+" TimeStamp:"+str(timestamp)+ " Type:"+type+ " Value:"+str(value))
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
					continue
				if type == 'crono_int':			# Tiempo intermedio Crono electronico
					continue
				if type == 'crono_stop':		# Parada Crono electronico
					continue
				if type == 'crono_rec':			# Llamada a reconocimiento de pista
					self.handle_rec(value)
					continue
				if type == 'crono_dat':			# Envio de Falta/Rehuse/Eliminado desde el crono
					continue
				if type == 'crono_restart':		# paso de crono manual a automatico (not supported here)
					continue
				if type == 'crono_reset':		# puesta a cero del contador
					continue
				if type == 'crono_error':		# error en alineamiento de sensores
					continue
					# entrada de datos, dato siguiente, cancelar operacion
				if type == 'llamada':			# operador abre panel de entrada de datos
					self.handle_llamada(evtdata)
					continue
				if type == 'datos':				# actualizar datos (si algun valor es -1 o nulo se debe ignorar)
					continue
				if type == 'aceptar':			# grabar datos finales
					continue
				if type == 'cancelar':			# restaurar datos originales
					continue
				if type == 'info':				# value: message
					self.handle_message(evtdata)
					continue
				if type == 'crono_ready':		# crono becomes ready
					continue
				if type == 'user':				# user defined event. Value=number
					continue
				# eventos de cambio de camara para gestion de Live Stream
				# el campo "data" contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
				if type == 'camera':			# cambio de fuente de streaming
					continue
				if type == 'reconfig':			# reconfiguracion del servidor
					continue
				# Si llega hasta aqui tenemos un error desconocido. Notificar e ignorar
				self.debug("Error: Unknown event type:"+type )

	def __init__(self,interface):
		#set up interface name
		NowRunning_Network.ETH_DEVICE = interface
		# some variables
		self.server = "192.168.1.35"	# to be evaluated later by querying network
		self.baseurl = "agility"		# standard /aglity base url. must be changed in nonstd installs
		self.session_id = 2				# to be retrieved from server and evaluate provided ring
		self.loop = True
		# do not throw exception on check certifcate
		requests.packages.urllib3.disable_warnings()

# End


