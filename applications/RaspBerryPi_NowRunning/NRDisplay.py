#!/usr/bin/env python3
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

#system
import time
import argparse
import threading
import sys
import os.path

#image handler
from PIL import Image, ImageFont, ImageDraw

# devices
from luma.led_matrix.device import max7219
from luma.emulator.device import pygame

from luma.core.interface.serial import spi, noop
from luma.core.render import canvas
from luma.core.legacy import text, show_message
from luma.core.virtual import viewport
from luma.core.legacy.font import proportional, CP437_FONT, TINY_FONT, SINCLAIR_FONT, LCD_FONT

class NRDisplay:

	DISPLAY = None # "max7219" or "pygame"
	loop = True
	nowRunning = 0
	menuMessage = ""
	stdMessage = ""
	oobMessage = ""
	oobDuration = 1
	contrast=5
	countDown=0
	glitch=0
	clockMode=False
	categoria=''
	grado=''

	# enable/disable clock mode (true,false)
	def setClockMode(self,value):
		NRDisplay.clockMode=value

	def getClockMode(self):
		return NRDisplay.clockMode

	# set countDowntime for course walk
	# value=seconds. 0 means stop
	def setCountDown(self,value):
		if value == 0:
			NRDisplay.countDown = 0
		else:
			NRDisplay.countDown=time.time() + float(value)

	def getCountDown(self):
		return NRDisplay.countDown

	# finalize display threads
	def stopDisplay(self):
		NRDisplay.loop=False

	# Operacion normal
	def setNowRunning(self,nr):
		if nr >= 0:
			NRDisplay.nowRunning = nr

	def getNowRunning(self):
		return NRDisplay.nowRunning

	def setNextRunning(self):
		NRDisplay.nowRunning = ( NRDisplay.nowRunning + 1 ) % 10000

	def setPrevRunning(self):
		nr=NRDisplay.nowRunning -1
		if nr < 0:
			nr=9999
		NRDisplay.nowRunning = nr

	def setRing(self,ring):
		self.ring = ring

	def getRing(self):
		return self.ring

	def setRoundInfo(self,manga,cat,grad):
		self.manga = cat
		self.categoria = cat
		self.grado = grad
		str= "%s %s - %s" %(self.manga,self.categoria,self.grado)
		self.ronda = str

	def getCategoria(self):
		return self.categoria

	def getManga(self):
		return self.manga

	def getGrado(self):
		return self.grado

	def getRoundInfo(self):
		return self.ronda

	# ajuste del menu
	def setMenuMessage(self,str):
		NRDisplay.menuMessage=str

	def setOobMessage(self,msg,duration):
		#make sure that oob message is longer than 4 characters
		while len(msg) <= 4:
			msg = " " + msg
		NRDisplay.oobMessage = msg
		NRDisplay.oobDuration = duration

	def setBrightness(self,value):
		NRDisplay.contrast = value

	def getBrightness(self):
		return NRDisplay.contrast

	def setGlitch(self,value):
		NRDisplay.glitch = int(value)

	#
	# Inicializacion del display
	def initDisplay(self,cascaded,block_orientation,rotate):
		# create matrix device
		if NRDisplay.DISPLAY == "max7219":
			serial = spi(port=0, device=0, gpio=noop())
			# use default if not provided by method
			dev = max7219(serial, cascaded=cascaded or 4, block_orientation=block_orientation or -90, rotate=rotate or 2)
		else:
			dev = pygame(width=32, height=8, rotate=0, mode="RGB", transform="scale2x", scale=2 )
		# set default bright level
		dev.contrast( int(5*255/9) )
		dev.show()
		print("Created device "+NRDisplay.DISPLAY)
		return dev

	#
	# Thread de generacion de los mensajes a presentar
	def setStdMessage(self):
		count = 0
		delay=15
		while NRDisplay.loop == True:
			msg = ""
			if NRDisplay.clockMode == True:
				msg = ""
			elif NRDisplay.countDown != 0:
				msg = "Running course walk"
				delay = 20
			elif (count%5) == 0:
				msg = "Ring %s %s" % ( self.ring , self.ronda)
			else:
				if NRDisplay.nowRunning == 0:
					msg = "Runing test dog"
					delay = 20
				else:
					msg = "Now running %03d" % ( NRDisplay.nowRunning )
			print("setStdMessage() "+msg)
			NRDisplay.stdMessage = msg
			time.sleep(delay)
			count = count + 1
		# while
		print("setStdMessageThread() exiting")
	# end def

	#
	# Bucle infinito de gestion de mensajes
	def displayLoop(self):
		oldmsg=""
		contrast=5
		while NRDisplay.loop == True:
			# si tenemos orden de glitch, jugamos con el contraste
			if NRDisplay.glitch != 0:
				NRDisplay.glitch = 0
				NRDisplay.device.contrast( 0 )
				time.sleep(0.5)
				NRDisplay.device.contrast( int(contrast*255/9) )
				continue
			# si cambia el contraste, reajustamos
			# lo tenemos que hacer desde este thread para evitar problemas de concurrencia
			# en el servidor Xcb
			if contrast != NRDisplay.contrast:
				contrast = NRDisplay.contrast
				NRDisplay.device.contrast( int(contrast*255/9) )
			# si menu activo pasa a visualizacion de menu
			if NRDisplay.menuMessage != "" :
				msg=NRDisplay.menuMessage
				sx=1
			# Los mensajes Out-Of-Band tienen precedencia absoluta
			elif NRDisplay.oobMessage != "":
				msg = NRDisplay.oobMessage
				NRDisplay.oobMessage = ""
				delay=NRDisplay.oobDuration * 0.01
				font=font=proportional(CP437_FONT)
			# si hay mensajes "normales" pendientes, muestralos
			elif NRDisplay.stdMessage != "":
				msg = NRDisplay.stdMessage
				NRDisplay.stdMessage = ""
				font=font=proportional(LCD_FONT)
				delay=0.02
			# si el temporizador está activo, mostramos tiempo restante
			elif NRDisplay.countDown != 0:
				remaining=NRDisplay.countDown - time.time()
				if remaining <= 0.0:
					txt="End of Course Walk"
					print(txt)
					self.setOobMessage(txt,2)
					NRDisplay.countDown=0 # will erase "Fin" msg at next iteration
				else:
					min = int(remaining/60)
					secs= int(remaining)%60
					msg="%d:%02d" %(min,secs)
					sx=1
			# si el reloj esta activo, presentamos la hora
			elif NRDisplay.clockMode == True:
				tm= time.localtime()
				if tm.tm_sec%2 == 0:
					msg = time.strftime("%H:%M",tm)
				else:
					msg = time.strftime("%H.%M",tm)
				sx=0
			# arriving here means just print dog running
			else:
				sx=5
				msg="%03d" % (NRDisplay.nowRunning)
			# time to display. check length for scroll or just show
			if oldmsg == msg:
				time.sleep(0.5)
				continue # do not repaint when not needed
			oldmsg = msg
			if len(msg) >5:
				show_message( NRDisplay.device, msg, fill="white", font=font, scroll_delay=delay )
			elif len(msg) == 5:
				with canvas(NRDisplay.device) as draw:
					text(draw, (sx, 0), msg, fill="white",font=proportional(CP437_FONT))
			else:
				with canvas(NRDisplay.device) as draw:
					text(draw, (sx, 0), msg, fill="white",font=CP437_FONT)
		# while loop=True
		NRDisplay.device.clear()
		NRDisplay.device.hide()
		print("displayLoopThread() exiting")
	# end thread loop


	#
	# Inicializacion de la clase
	def __init__(self,display,cascaded,block_orientation,rotate):
		# initialize vars
		NRDisplay.DISPLAY = display
		NRDisplay.loop = True
		NRDisplay.stdMessage = ""
		self.setBrightness(5)
		self.setMenuMessage("")
		self.setOobMessage( "Hello AgilityContest", 1)

		# informacion de ring y manga y perro en pista
		self.setRing(1)
		self.setRoundInfo("Agility","Large","Grado 1")
		self.setNowRunning(0)

		NRDisplay.device= self.initDisplay(cascaded,block_orientation,rotate)
