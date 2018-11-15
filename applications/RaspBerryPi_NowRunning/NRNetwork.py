#!/usr/bin/python3
#NRNetwork.py
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

class NRNetwork:

	##### Some constants
	SESSION_NAME = "NowRunning_2"	# should be generated from ring
	DEBUG=True
	ETH_DEVICE='eth0'		# this should be modified if using Wifi (!!NOT RECOMMENDED AT ALL!!)
	ENABLED=True
	rings = ["2","3","4","5"] # array of session id's received from server To be re-evaluated later from server response

	def kitt(self,count):
		return ( count + 1 ) % 8

	def debug(self,str):
		if NRNetwork.DEBUG==True:
			print (str)

# controls from Menu

	def setRing(self,ring):
		if NRNetwork.ring != ring:
			NRNetwork.ring=ring


	def showIPAddress(self):
		for iface in ni.interfaces():
			if (iface != NRNetwork.ETH_DEVICE):
				continue
			addrs = ni.ifaddresses(iface)
			# assumed a single IP on interface
			msg=addrs[ni.AF_INET][0]['addr']
			print ("IP Address is: "+msg)
			self.dspHandler.setOobMessage(msg,2)
			return

	def setEnabled(self,state):
		NRNetwork.ENABLED=state

	def reconnect(self):
		return

# scan local network to look for server
	def lookForServer(self,ring):
		print ("loop is "+str(NRNetwork.loop))
		# look for IPv4 addresses on ETH_DEVICE [0]->use first IPv4 address found on this interface
		netinfo=ni.ifaddresses(NRNetwork.ETH_DEVICE)[ni.AF_INET][0]
		# iterate on every hosts on this network/netmask. Use strict=False to ignore ip address host bits
		count=0
		url= "/agility/ajax/database/sessionFunctions.php"
		args= "?Operation=selectring" # operation to enumerate available ring sessions
		for i in ipaddress.IPv4Network(netinfo['addr']+"/"+netinfo['netmask'],strict=False).hosts():
			# on "premature" program exit, just dont poll anymore
			if NRNetwork.loop == False:
				break
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
					NRNetwork.rings[id]=data['rows'][id]['ID']
				# clear leds and return
				self.kitt(-1)
				break
			except requests.exceptions.RequestException as ex:
				# self.debug ( "Http request error:" + str(ex) )
				continue
		else:
			# arriving here means self.server not found
			self.session_id=0 # invalid sid
			NRNetwork.ENABLED = False # mark do not try to handle events
			return "0.0.0.0"
		# on received answer retrieve Session ID from requested ring
		NRNetwork.ring=ring
		self.session_id=NRNetwork.rings[NRNetwork.ring-1]
		self.debug( "Ring: "+str(ring)+ " Session ID: "+str(self.session_id) )
		# and finally setup server IP
		return self.server

# Reconocimiento de pista / Fin de reconocimiento
	def handle_rec(self,time):
		self.debug("Reconocimiento de pista")
		# disparar un tempporizador que vaya descontando en el marcador

# Llamada a pista
	def handle_llamada(self,data):
		self.dspHandler.setNowRunning(int(data))

# comando desde consola
	def handle_command(self,data):
		if data['Oper'] != 8:
			return
		a=data['Value'].split(":") # Value = "duration:message"
		self.dspHandler.setOobMessage(a[1],int(a[0]))

# open manga
	def handle_open(self,data):
		print("data is:'%s' " % (data))
		self.dspHandler.setOobMessage(data,1)
		self.dspHandler.setRoundInfo(data)
		self.dspHandler.setNowRunning(1)

# parar bucle de eventos
	def stopNetwork(self):
		NRNetwork.loop = False

# wait for network event messages
# this method runs in a separate thread
	def eventParser(self):
		while NRNetwork.loop == True:
			# call to "connect", to retrieve last event id and timeout
			while NRNetwork.loop == True:
				if NRNetwork.ENABLED == False:
					time.sleep(5) # do nothing during 5 seconds
					continue
				try:
					# evaluate SessionName to allow control from console
					self.session_id=NRNetwork.rings[NRNetwork.ring-1]
					sname="videowall:%s:0:0:NowRunning_%d" % ( self.session_id,NRNetwork.ring)
					event_id=0 # event ID of last "open" call in current session
					# prepare server "connect" call
					args = "?Operation=connect&Session="+self.session_id+"&SessionName="+sname
					url="https://" + self.server + "/" + self.baseurl + "/ajax/database/eventFunctions.php"+args
					self.debug( "Connecting event manager on "+url)
					response = requests.get(url, verify=False)
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
			# on stop thread requested dont try to continue
			if NRNetwork.loop == False:
				break

			# connect done, now, enter in an infinite "getEvents" request loop
			self.debug( "Connected. Waiting for Server events ...")
			timestamp=0
			while NRNetwork.loop == True:
				try:
					if NRNetwork.ENABLED == False:
						time.sleep(5) # do nothing during 5 seconds
						continue
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
				timestamp = 0
				if 'TimeStamp' in data:
					timestamp = data['TimeStamp']
				for i in data['rows']:
					event_id=i['ID']
					type=i['Type']
					evtdata=json.loads(i['Data'])
					value=0
					numero=0
					if 'Value' in evtdata:			# some events does not provide "Value" in data
						value = evtdata['Value']
					if 'Numero' in evtdata:
						numero = evtdata['Numero']
					self.debug ("Reveived Event ID:"+str(event_id)+" TimeStamp:"+str(timestamp)+ " Type:"+type+ " Value:"+str(value)+ " Numero:"+str(numero))
					# Eventos generales
					if type == 'null':				# null event: no action taken
						continue
					if type == 'init':				# operator starts tablet application
						continue
					if type == 'login':				# operador hace login en el sistema
						continue
					if type == 'open':				# operator selects tanda on tablet
						self.handle_open(evtdata['NombreManga'])
						continue
					if type == 'close':				# operator exit from dog data entry on tablet
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
						self.handle_llamada(evtdata['Numero'])
						continue
					if type == 'datos':				# actualizar datos (si algun valor es -1 o nulo se debe ignorar)
						continue
					if type == 'aceptar':			# grabar datos finales
						continue
					if type == 'cancelar':			# restaurar datos originales
						continue
					if type == 'info':				# value: message
						continue
					if type == 'crono_ready':		# crono becomes ready
						continue
					if type == 'user':				# user defined event. Value=number
						continue
					if type == 'command':		   # command from console Oper,Value= key:val
						self.handle_command(evtdata)
						continue
					# eventos de cambio de camara para gestion de Live Stream
					# el campo "data" contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
					if type == 'camera':			# cambio de fuente de streaming
						continue
					if type == 'reconfig':			# reconfiguracion del servidor
						continue
					# Si llega hasta aqui tenemos un error desconocido. Notificar e ignorar
					self.debug("Error: Unknown event type:"+type )
				# for data in rows

				# No more events. Check for ring change
				if self.session_id != NRNetwork.rings[NRNetwork.ring-1]:
					self.session_id = NRNetwork.rings[NRNetwork.ring-1]
					break
			# getEvents from opened session loop
		# eventParser global loop
	# def eventParser

	def __init__(self,interface,handler):
		#set up interface name and status info
		NRNetwork.ETH_DEVICE = interface
		NRNetwork.ENABLED = True
		NRNetwork.loop = True

		# set up displayHandler
		self.dspHandler = handler

		# some variables
		self.server = "192.168.1.35"	# to be evaluated later by querying network
		self.baseurl = "agility"		# standard /aglity base url. must be changed in nonstd installs
		self.session_id = 2				# to be retrieved from server and evaluate provided ring

		# do not throw exception on check certifcate
		requests.packages.urllib3.disable_warnings()

# End


