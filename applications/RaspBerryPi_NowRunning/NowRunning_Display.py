#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.

import time
import argparse
import threading
import sys

from luma.led_matrix.device import max7219
from luma.core.interface.serial import spi, noop
from luma.core.render import canvas
from luma.core.virtual import viewport
from luma.core.legacy import text, show_message
from luma.core.legacy.font import proportional, CP437_FONT, TINY_FONT, SINCLAIR_FONT, LCD_FONT

class NowRunning_Display:

    def setNowRunning(nr):
        self.nowRunning = nr

    def setNextRunning():
        self.nowRunning = self.nowRunning + 1

    def setData(r,m,c,g):
        self.ring = r
        self.ronda = m
        self.categoria = c
        self.grado = g

    def setOobMessage(msg):
        self.oobMessage = msg

    def setStdMessage():
        count = 0
        while nowRunning != 0:
            msg = ""
            if ( count % 4 ) == 0:
                msg = "Ring %s %s %s-%s " % ( self.ring , self.ronda , self.categoria , self.grado )
            else:
                msg = "Now running %03d " % ( self.nowRunning )
            print(msg)
            self.stdMessage = msg
            time.sleep(5)
            count = count + 1

    def initDisplay(cascaded,block_orientation,rotate):
        global device
        # create matrix device
        serial = spi(port=0, device=0, gpio=noop())
        device = max7219(serial, cascaded=n or 1, block_orientation=block_orientation, rotate=rotate or 0)
        print("Created device")

    def displayLoop():
        global device
        while self.nowRunning != 0:
            time.sleep(1)
            if self.oobMessage != "":
                msg = self.oobMessage
                self.oobMessage = ""
                print(msg)
                show_message( device, msg, fill="white", font=proportional(CP437_FONT) )
                continue
            if stdMessage != "":
                msg = self.stdMessage
                self.stdMessage = ""
                print(msg)
                show_message( device, msg, fill="white", font=proportional(LCD_FONT) , scroll_delay=0.05 )
                continue
            # arriving here means just print dog running
            with canvas(device) as draw:
                text(draw, (5, 0), "%03d " % (self.nowRunning), fill="white")
                continue

    def __init__(self,cascaded,block_orientation,rotate):
        global device
        self.oobMessage = ""
        self.stdMessage = "Hello AgilityContest"
        self.nowRunning = 1

        self.ring = 1
        self.ronda = "Agility"
        self.categoria = "Std"
        self.grado = "G2"

        device = initDisplay(cascaded,block_orientation,rotate)
