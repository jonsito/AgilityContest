#!/usr/bin/python3
# -*- coding: utf-8 -*-
#
# Copyright  2018-2019 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
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
import random
import string		  # random+string to compose a unique Name

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
	SNAME = "NowRunning_2"	# default session name. should be generated from ring
	DEBUG=True
	ETH_DEVICE='eth0'		# this should be modified if using Wifi (!!NOT RECOMMENDED AT ALL!!)
	ENABLED=True
	loop=True
	reconfigure=False
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
		msg = ""
		for ifname in ni.interfaces():
			# si hemos especificado una en concreto, compara
			if NRNetwork.ETH_DEVICE != "": # an interface has been specified
				if ifname != NRNetwork.ETH_DEVICE: # doesn't match with current. skip
					continue
			# buscamos las direcciones IPv4 que tiene esta pata de red
			addresses = [i['addr'] for i in ni.ifaddresses(ifname).setdefault(ni.AF_INET, [{'addr':'No IP addr'}] )]
			for addr in addresses:
				if addr=="No IP addr": # this interface has no IPv4 address active. skip
					continue
				elif addr=="127.0.0.1": # loopbak. skip
					continue
				else:
					msg = msg + " - " + addr
			# foreach IP on each interface
		# foreach interface
		if msg=="": # no active IPv4 addresses found.
			msg = "No network connection"
		# finally send result to display
		self.debug("IP Address is: "+msg)
		self.dspHandler.setOobMessage("IP Addr: "+msg,3)
		return

	def showServerAddress(self):
		msg="Server not connected"
		if self.server != "0.0.0.0":
			msg="Server IP Addr: "+self.server
		# finally send result to display
		self.debug(msg)
		self.dspHandler.setOobMessage(msg,3)

	def setEnabled(self,state):
		NRNetwork.ENABLED=state

	def restartConnection(self):
		NRNetwork.reconfigure=True
		return

	# get all interfaces
	def lookForServer(self,ring,dspHandler):
		# buscamos las interfaces del equipo
		for ifname in ni.interfaces():
			# si hemos especificado una en concreto, compara
			if NRNetwork.ETH_DEVICE != "": # an interface has been specified
				if ifname != NRNetwork.ETH_DEVICE: # doesn't match with current. skip
					continue
			self.debug("Looking for server at interface "+ifname)
			# buscamos las direcciones IPv4 que tiene esta pata de red
			addresses = [i['addr'] for i in ni.ifaddresses(ifname).setdefault(ni.AF_INET, [{'addr':'No IP addr'}] )]
			for addr in addresses:
				if NRNetwork.loop==False:
					break

				mask="255.255.255.0" # default mask. no sense to use other in server lookup (except for loopback)
				if addr=="No IP addr":
					# this interface has no IPv4 address active
					continue
				if addr=="127.0.0.1":
					mask="255.255.255.254" # no sense to iterate over loopback; just use address itself
				ip=self.lookForServerAt(addr,mask,ring,dspHandler)
				if ip=="0.0.0.0":
					# no server found in this address. try next one
					continue
				else:
					# on received answer retrieve Session ID from requested ring
					self.server=ip
					NRNetwork.ring=ring
					self.session_id=NRNetwork.rings[NRNetwork.ring-1]
					self.debug( "Ring: "+str(ring)+ " Session ID: "+str(self.session_id) )
					dspHandler.setOobMessage("Server found at IP: "+ip,2)
					# and finally setup server IP
					return ip
			# no server found in any address from this interface. try next

		# arriving here means no server found
		self.session_id=0 # invalid sid
		NRNetwork.ENABLED = False # mark do not try to handle events
		self.debug("Console server not found.")
		dspHandler.setOobMessage("Console server not found",2)
		return "0.0.0.0"
	# def lookForServer

# scan local network to look for server
	def lookForServerAt(self,ipaddr,ipmask,ring,dspHandler,):
		# iterate on every hosts on this network/netmask. Use strict=False to ignore ip address host bits
		count=0
		url= "/agility/ajax/database/sessionFunctions.php"
		args= "?Operation=selectring" # operation to enumerate available ring sessions
		for i in ipaddress.IPv4Network(ipaddr+"/"+ipmask,strict=False).hosts():
			# on "premature" program exit, just dont poll anymore
			if NRNetwork.loop == False:
				break
			count = self.kitt(count)
			ip = str(i)
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
				# retrieve session id for each ring
				for id in range (0,3):
					NRNetwork.rings[id]=data['rows'][id]['ID']
				# clear leds and return
				self.kitt(-1)
				return ip
			except requests.exceptions.RequestException as ex:
				# self.debug ( "Http request error:" + str(ex) )
				continue
		# arriving here means no server found
		return "0.0.0.0"
	# def lookForServerAt

# Reconocimiento de pista / Fin de reconocimiento
	def handle_rec(self,time):
		self.debug("Reconocimiento de pista")
		# disparar un tempporizador que vaya descontando en el marcador
		if time==0:
			self.debug("Course walk countdown stop")
			self.dspHandler.setOobMessage("End of Course Walk",2)
		else:
			self.dspHandler.setOobMessage("Starting Course Walk",2)
		self.dspHandler.setCountDown(time)

# Llamada a pista
	def handle_llamada(self,numero,id):
		if int(id)==0: # si perro en blanco marcamos perro numero "0"
			numero='0'
		self.dspHandler.setNowRunning(int(numero))

# comando desde consola
	def handle_command(self,data):
		if data['Oper'] != 8:
			return
		a=data['Value'].split(":") # Value = "duration:message"
		self.dspHandler.setOobMessage(a[1],int(a[0]))

# open manga
	def handle_open(self,data):
		self.debug("data is:'%s' " % (data))
		self.dspHandler.setOobMessage(data,1)
		self.dspHandler.setRoundInfo(data)
		self.dspHandler.setNowRunning(1)

# parar bucle de eventos
	def stopNetwork(self):
		NRNetwork.loop = False

	# send a "connect" request to retrieve last event id for current session
	# return -1 on  error, else last event id
	def openSession(self,current_ring):
		try:
			# evaluate SessionName to allow control from console
			self.session_id=NRNetwork.rings[current_ring-1]
			self.session_name="videowall:%s:0:0:%s@%d" % ( self.session_id,NRNetwork.SNAME,current_ring)
			event_id=0 # event ID of last "open" call in current session
			# prepare server "connect" call
			args = "?Operation=connect&Session="+self.session_id+"&SessionName="+self.session_name
			url="https://" + self.server + "/" + self.baseurl + "/ajax/database/eventFunctions.php"+args
			self.debug( "Connecting event manager on "+url)
			response = requests.get(url, verify=False, timeout=5, auth=('AgilityContest','AgilityContest'))
		except requests.exceptions.RequestException as ex:
			self.debug ( "Connect() error:" + str(ex) )
			return -1

		# if response failed, try next IP address
		if response.status_code != 200:
			self.debug("Connect: Invalid server response staus: "+str(response.status_code))
			return -1

		# response ok: retrieve event ID of last "open" call
		data=response.json()
		if data['total'] == "0":
			self.debug("Connect: empty response from server")
			return -1

		# arriving here means everything ok. return event ID
		return data['rows'][0]['ID']
	# end def

	# retrieve events newer than event id
	def getEvents(self,event_id,timestamp):
		ts=int(time.time())
		try:
			args="?Operation=getEvents&Session=%s&ID=%s&TimeStamp=%s&SessionName=%s" % (str(self.session_id),str(event_id),str(timestamp),self.session_name)
			response = requests.get("https://" + self.server + "/" + self.baseurl + "/ajax/database/eventFunctions.php"+args,
				verify=False, timeout=30, auth=('AgilityContest','AgilityContest') )
		except requests.exceptions.RequestException as ex:
			self.debug ( "getEvents() error:" + str(ex) )
			return {"total":0 , "rows":[] , "TimeStamp": ts }
		if response.status_code != 200:
			self.debug("getEvents() error: received status code:"+str(response_status_code))
			return {"total":0 , "rows":[] , "TimeStamp": ts }
		try:
			return response.json()
		except ValueError:
			self.debug("response.json thows exception")
			return {"total":0 , "rows":[] , "TimeStamp": ts }

	#end def

	def parseEvent(self,event,timestamp):
		event_id=event['ID']
		type=event['Type']
		evtdata=json.loads(event['Data'])
		value=0
		numero=0
		if 'Value' in evtdata:			# some events does not provide "Value" in data
			value = evtdata['Value']
		if 'Numero' in evtdata:
			numero = evtdata['Numero']
		self.debug ("Reveived Event ID:"+str(event_id)+" TimeStamp:"+str(timestamp)+ " Type:"+type+ " Value:"+str(value)+ " Numero:"+str(numero))
		# Eventos generales
		if type == 'null':				# null event: no action taken
			return
		if type == 'init':				# operator starts tablet application
			return
		if type == 'login':				# operador hace login en el sistema
			return
		if type == 'open':				# operator selects tanda on tablet
			self.handle_open(evtdata['NombreManga'])
			return
		if type == 'close':				# operator exit from dog data entry on tablet
			return
		# eventos de crono manual
		if type == 'salida':			# juez da orden de salida ( crono 15 segundos )
			return
		if type == 'start':				# Crono manual - value: timestamp
			return
		if type == 'stop':				# Crono manual - value: timestamp
			return
		# en crono electronico los campos "Value" y "TimeStamp" contienen la marca de tiempo del sistema
		# en el momento en que se capturo el evento
		if type == 'crono_start':		# Arranque Crono electronico
			return
		if type == 'crono_int':			# Tiempo intermedio Crono electronico
			return
		if type == 'crono_stop':		# Parada Crono electronico
			return
		if type == 'crono_rec':			# Llamada a reconocimiento de pista
			self.handle_rec(evtdata['start'])
			return
		if type == 'crono_dat':			# Envio de Falta/Rehuse/Eliminado desde el crono
			return
		if type == 'crono_restart':		# paso de crono manual a automatico (not supported here)
			return
		if type == 'crono_reset':		# puesta a cero del contador
			return
		if type == 'crono_error':		# error en alineamiento de sensores
			return
		# entrada de datos, dato siguiente, cancelar operacion
		if type == 'llamada':			# operador abre panel de entrada de datos
			self.handle_llamada(evtdata['Numero'],evtdata['Dog'])
			return
		if type == 'datos':				# actualizar datos (si algun valor es -1 o nulo se debe ignorar)
			return
		if type == 'aceptar':			# grabar datos finales
			return
		if type == 'cancelar':			# restaurar datos originales
			return
		if type == 'info':				# value: message
			return
		if type == 'crono_ready':		# crono becomes ready
			return
		if type == 'user':				# user defined event. Value=number
			return
		if type == 'command':		   # command from console Oper,Value= key:val
			self.handle_command(evtdata)
			return
		# eventos de cambio de camara para gestion de Live Stream
		# el campo "data" contiene la variable "Value" (url del stream ) y "mode" { mjpeg,h264,ogg,webm }
		if type == 'camera':			# cambio de fuente de streaming
			return
		if type == 'reconfig':			# reconfiguracion del servidor
			return
		# Si llega hasta aqui tenemos un error desconocido. Notificar e ignorar
		self.debug("Error: Unknown event type:"+type )
		return
	# parseEvent

# wait for network event messages
# this method runs in a separate thread

	def networkLoop(self):
		current_ring = -1
		timestamp = 0
		while NRNetwork.loop == True:
			# check for reconfigure command
			if NRNetwork.reconfigure == True:
				self.debug("Network re-configuration requested")
				NRNetwork.reconfigure = False
				current_ring = -1
				timestamp = 0
				server=self.lookForServer(NRNetwork.ring,self.dspHandler)
				# server not found. wait 30 seconds before retrying
				if server == "0.0.0.0":
					NRNetwork.reconfigure = True # Try to reconnect again
					time.sleep(30)
					continue
				self.debug("Reconfigure(): Server found at: "+server)

			# on ring change (or first loop iteration) open session
			if current_ring != NRNetwork.ring:
				current_ring=NRNetwork.ring
				timestamp = 0
				# call "open" on current ring
				self.debug("Trying to open session on ring:"+str(current_ring))
				event_id=self.openSession(current_ring)
				if event_id == -1:
					# "connect" error
					time.sleep(5)
					current_ring = -1 # force retry "connect" again
					continue
				else:
					# "connect" success
					self.debug("Connection done. Start parsing event loop at event ID: "+str(event_id))

			# If network is paused, do not retrieve events
			if NRNetwork.ENABLED == False:
				time.sleep(5)
				continue;

			# Open success and Network enabled: retrieve and parse events
			data = self.getEvents(event_id,timestamp)
			if int(data['total']) == 0:
				self.debug("getEvents returns no data")
				time.sleep(5) # no data available. Sleep and retry
				continue

			#self.debug("received"+str(data))
			# retrieve timestamp if provided
			if 'TimeStamp' in data:
				timestamp = data['TimeStamp']

			# finally iterate over every received events
			for event in data['rows']:
				event_id=event['ID']
				self.debug("parsing event ID"+str(event_id))
				self.parseEvent(event,timestamp)

		# while NRNetwork.loop == True
		self.debug("networkThread() exiting")
	# network thread

	def __init__(self,interface,ring,handler):
		#set up interface name and status info
		NRNetwork.ETH_DEVICE = interface
		NRNetwork.ring = ring
		NRNetwork.ENABLED = True
		NRNetwork.loop = True
		NRNetwork.reconfigure = True

		# create a random session name
		rndstr="".join([random.choice(string.ascii_letters + string.digits) for n in range(8)])
		NRNetwork.SNAME = "NowRunning_"+rndstr

		# set up displayHandler
		self.dspHandler = handler

		# some variables
		self.server = "0.0.0.0"	# to be evaluated later by querying network
		self.baseurl = "agility"		# standard /aglity base url. must be changed in nonstd installs
		self.session_id = 2				# to be retrieved from server and evaluate provided ring

		# do not throw exception on check certifcate
		requests.packages.urllib3.disable_warnings()

# End


