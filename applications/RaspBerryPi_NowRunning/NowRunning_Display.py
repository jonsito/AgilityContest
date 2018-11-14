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
from PIL import Image

# devices
from luma.led_matrix.device import max7219
from luma.emulator.device import pygame

from luma.core.interface.serial import spi, noop
from luma.core.render import canvas
from luma.core.legacy import text, show_message
from luma.core.virtual import viewport
from luma.core.legacy.font import proportional, CP437_FONT, TINY_FONT, SINCLAIR_FONT, LCD_FONT

class NowRunning_Display:

    DISPLAY = None # "max7219" or "pygame"
    nowRunning = 1
    stdMessage = ""
    oobMessage = ""
    oobDuration = 1

    # ajuste del menu
    def setMenuState(self,key,value):
        self.menuKey=key # if key==-1 menu is not active
        self.menuValue=value

    #
    # Operacion normal
    def setNowRunning(self,nr):
        NowRunning_Display.nowRunning = nr

    def setNextRunning(self):
        NowRunning_Display.nowRunning = NowRunning_Display.nowRunning + 1

    def setRing(self,ring):
        self.ring = ring

    def setRoundInfo(self,info):
        self.ronda = info

    def setOobMessage(self,msg,duration):
        NowRunning_Display.oobMessage = msg
        NowRunning_Display.oobDuration = duration

    #
    # Inicializacion del display
    def initDisplay(self,cascaded,block_orientation,rotate):
        # create matrix device
        if NowRunning_Display.DISPLAY == "max7219":
            serial = spi(port=0, device=0, gpio=noop())
            # use default if not provided by method
            dev = max7219(serial, cascaded=cascaded or 4, block_orientation=block_orientation or -90, rotate=rotate or 2)
        else:
            dev = pygame(width=32, height=8, rotate=0, mode="RGB", transform="scale2x", scale=2 )

        # prepara datos graficos para el menu
        img_path = os.path.abspath(os.path.join(os.path.dirname(__file__),'menu.png'))
        self.pixel_art =Image.open(img_path).convert(dev.mode)

        print("Created device "+NowRunning_Display.DISPLAY)
        return dev

    #
    # Thread de generacion de los mensajes a presentar
    def setStdMessage(self):
        count = 0
        while NowRunning_Display.nowRunning != 0:
            msg = ""
            if ( count % 5 ) == 0:
                msg = "Ring %s %s" % ( self.ring , self.ronda)
            else:
                msg = "Now running %03d" % ( NowRunning_Display.nowRunning )
            print("setStdMessage() "+msg)
            NowRunning_Display.stdMessage = msg
            time.sleep(15)
            count = count + 1

    #
    # presentacion del menu
    def handleMenu(self,key,value):
        if self.oldMenuKey != key:
            self.oldMenuKey = key
            # display menuKey
            pos=(0,8*key)
            w, h = self.pixel_art.size
            virtual = viewport(self.device, width=w, height=h)
            virtual.display(self.pixel_art)
            virtual.set_position(pos)

        # show menuValue
        if self.oldMenuValue != value:
            self.oldMenuValue = value
            with canvas(self.device) as draw:
                text(draw, (25, 0), value, fill="white")
        # wait for painting
        time.sleep(1)

    #
    # Bucle infinito de gestion de mensajes
    def displayLoop(self):
        while NowRunning_Display.nowRunning != 0:
            # si menu activo pasa a visualizacion de menu
            if self.menuKey >0 :
                self.handleMenu(self.menuKey,self.menuValue)
                continue
            # Los mensajes Out-Of-Band tienen precedencia absoluta
            if NowRunning_Display.oobMessage != "":
                msg = NowRunning_Display.oobMessage
                NowRunning_Display.oobMessage = ""
                delay=NowRunning_Display.oobDuration * 0.01
                show_message( self.device, msg, fill="white", font=proportional(CP437_FONT), scroll_delay=delay )
                continue
            # si hay mensajes "normales" pendientes, muestralos
            if NowRunning_Display.stdMessage != "":
                msg = NowRunning_Display.stdMessage
                NowRunning_Display.stdMessage = ""
                show_message( self.device, msg, fill="white", font=proportional(LCD_FONT), scroll_delay=0.02 )
                continue
            # arriving here means just print dog running
            msg="%03d " % (NowRunning_Display.nowRunning)
            with canvas(self.device) as draw:
                text(draw, (5, 0), msg, fill="white")
                time.sleep(2)
                continue

    #
    # Inicializacion de la clase
    def __init__(self,display,cascaded,block_orientation,rotate):
        # initialize vars
        NowRunning_Display.DISPLAY = display
        NowRunning_Display.stdMessage = ""
        self.setOobMessage = ( "Hello AgilityContest", 1)

        # informacion de ring y manga y perro en pista
        self.setRing(1)
        self.setRoundInfo("")
        self.setNowRunning(1)

        # informacion de menu ( key:-1 -> No menu activo)
        self.setMenuState(-1," ")
        self.oldMenuKey=-1
        self.oldMenuValue=" "

        self.device= self.initDisplay(cascaded,block_orientation,rotate)
