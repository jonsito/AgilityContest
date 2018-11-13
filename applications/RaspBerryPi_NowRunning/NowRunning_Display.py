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

    DISPLAY = None # "max7219" or "pygame"
    nowRunning = 1
    stdMessage = ""
    oobMessage = ""
    oobDuration = 1

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

    def initDisplay(self,cascaded,block_orientation,rotate):
        global device
        # create matrix device
        if NowRunning_Display.DISPLAY == "max7219":
            serial = spi(port=0, device=0, gpio=noop())
            # use default if not provided by method
            dev = max7219(serial, cascaded=cascaded or 4, block_orientation=block_orientation or -90, rotate=rotate or 2)
        else:
            dev = pygame(width=32, height=8, rotate=rotate or 0, mode="RGB", transform="scale2x", scale=2 )
            # show_message( device, "Hello, World", fill="white", font=proportional(CP437_FONT) )
        print("Created device "+NowRunning_Display.DISPLAY)
        return dev

    def displayLoop(self):
        while NowRunning_Display.nowRunning != 0:
            if NowRunning_Display.oobMessage != "":
                msg = NowRunning_Display.oobMessage
                NowRunning_Display.oobMessage = ""
                delay=NowRunning_Display.oobDuration * 0.01
                show_message( self.device, msg, fill="white", font=proportional(CP437_FONT), scroll_delay=delay )
                continue
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

    def __init__(self,display,cascaded,block_orientation,rotate):
        NowRunning_Display.DISPLAY = display
        NowRunning_Display.nowRunning = 1
        NowRunning_Display.stdMessage = ""
        NowRunning_Display.oobMessage = "Hello AgilityContest"
        NowRunning_Display.oobDuration = 1

        self.ring = 1
        self.ronda = ""

        self.device= self.initDisplay(cascaded,block_orientation,rotate)
