#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.

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
	nowRunning = 1
	menuMessage = ""
	stdMessage = ""
	oobMessage = ""
	oobDuration = 1

	#
	# Operacion normal
	def setNowRunning(self,nr):
		NRDisplay.nowRunning = nr

	def setNextRunning(self):
		NRDisplay.nowRunning = NRDisplay.nowRunning + 1

	def setRing(self,ring):
		self.ring = ring

	def setRoundInfo(self,info):
		self.ronda = info

	# ajuste del menu
	def setMenuMessage(self,str):
		NRDisplay.menuMessage=str

	def setOobMessage(self,msg,duration):
		NRDisplay.oobMessage = msg
		NRDisplay.oobDuration = duration

	def setBrightness(self,value):
		NRDisplay.device.contrast( int(value*255/9) )

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
		print("Created device "+NRDisplay.DISPLAY)
		return dev

	#
	# Thread de generacion de los mensajes a presentar
	def setStdMessage(self):
		count = 0
		while NRDisplay.nowRunning != 0:
			msg = ""
			if ( count % 5 ) == 0:
				msg = "Ring %s %s" % ( self.ring , self.ronda)
			else:
				msg = "Now running %03d" % ( NRDisplay.nowRunning )
			print("setStdMessage() "+msg)
			NRDisplay.stdMessage = msg
			time.sleep(15)
			count = count + 1

	#
	# Bucle infinito de gestion de mensajes
	def displayLoop(self):
		oldmsg=""
		while NRDisplay.nowRunning != 0:
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
			# arriving here means just print dog running
			else:
				sx=5
				msg="%03d" % (NRDisplay.nowRunning)

			# time to display. check length for scroll or just show
			if oldmsg == msg:
				time.sleep(1)
				continue # do not repaint when not needed
			oldmsg = msg
			if len(msg) <= 4:
				with canvas(NRDisplay.device) as draw:
					text(draw, (sx, 0), msg, fill="white")
			else:
				show_message( NRDisplay.device, msg, fill="white", font=font, scroll_delay=delay )

	#
	# Inicializacion de la clase
	def __init__(self,display,cascaded,block_orientation,rotate):
		# initialize vars
		NRDisplay.DISPLAY = display
		NRDisplay.stdMessage = ""
		self.setMenuMessage("")
		self.setOobMessage( "Hello AgilityContest", 1)

		# informacion de ring y manga y perro en pista
		self.setRing(1)
		self.setRoundInfo("")
		self.setNowRunning(1)

		NRDisplay.device= self.initDisplay(cascaded,block_orientation,rotate)
