#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.

import time
import argparse
import threading
import sys

from luma.led_matrix.device import max7219
from luma.emulator.device import pygame
from luma.core.interface.serial import spi, noop
from luma.core.render import canvas
from luma.core.legacy import text, show_message
from luma.core.legacy.font import proportional, CP437_FONT, TINY_FONT, SINCLAIR_FONT, LCD_FONT

class NowRunning_Display:


    DISPLAY = "pygame"
    #DISPLAY = "max7219"
    nowRunning = 1
    stdMessage = ""
    oobMessage = ""

    def setNowRunning(self,nr):
        NowRunning_Display.nowRunning = nr

    def setNextRunning(self):
        NowRunning_Display.nowRunning = NowRunning_Display.nowRunning + 1

    def setData(self,r,m,c,g):
        self.ring = r
        self.ronda = m
        self.categoria = c
        self.grado = g

    def setOobMessage(self,msg):
        NowRunning_Display.oobMessage = msg

    def setStdMessage(self):
        count = 0
        while NowRunning_Display.nowRunning != 0:
            msg = ""
            if ( count % 5 ) == 0:
                msg = "Ring %s %s %s-%s" % ( self.ring , self.ronda , self.categoria , self.grado )
            else:
                msg = "Now running %03d" % ( NowRunning_Display.nowRunning )
            print("setStdMessage() "+msg)
            NowRunning_Display.stdMessage = msg
            time.sleep(15)
            count = count + 1

    def initDisplay(self,cascaded,block_orientation,rotate):
        global device
        # create matrix device
        if NowRunning_Display.DISPLAY == "max7219":
            serial = spi(port=0, device=0, gpio=noop())
            dev = max7219(serial, cascaded=n or 1, block_orientation=block_orientation, rotate=rotate or 0)
        else:
            dev = pygame(width=32, height=8, rotate=rotate or 0, mode="RGB", transform="scale2x", scale=2)
            # show_message( device, "Hello, World", fill="white", font=proportional(CP437_FONT) )
        print("Created device "+NowRunning_Display.DISPLAY)
        return dev

    def displayLoop(self):
        while NowRunning_Display.nowRunning != 0:
            if NowRunning_Display.oobMessage != "":
                msg = NowRunning_Display.oobMessage
                NowRunning_Display.oobMessage = ""
                show_message( self.device, msg, fill="white", font=proportional(CP437_FONT), scroll_delay=0.01 )
                continue
            if NowRunning_Display.stdMessage != "":
                msg = NowRunning_Display.stdMessage
                NowRunning_Display.stdMessage = ""
                show_message( self.device, msg, fill="white", font=proportional(LCD_FONT), scroll_delay=0.01 )
                continue
            # arriving here means just print dog running
            msg="%03d " % (NowRunning_Display.nowRunning)
            with canvas(self.device) as draw:
                text(draw, (5, 0), msg, fill="white")
                time.sleep(2)
                continue

    def __init__(self,cascaded,block_orientation,rotate):
        NowRunning_Display.nowRunning = 1
        NowRunning_Display.stdMessage = ""
        NowRunning_Display.oobMessage = "Hello AgilityContest"

        self.ring = 1
        self.ronda = "Agility"
        self.categoria = "Std"
        self.grado = "G2"

        self.device= self.initDisplay(cascaded,block_orientation,rotate)
