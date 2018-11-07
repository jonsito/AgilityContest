#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.

import re
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

nowRunning = 1

def inputParser():
    global nowRunning
    while  True:
        data = sys.stdin.readline()
        if data == "\n":
            nowRunning = nowRunning + 1
        else:
            print ("received '"+data+"'")
            nowRunning = int(data)
        if nowRunning == 0:
            break

def loop(n, block_orientation, rotate):
    global nowRunning
    # create matrix device
    serial = spi(port=0, device=0, gpio=noop())
    device = max7219(serial, cascaded=n or 1, block_orientation=block_orientation, rotate=rotate or 0)
    print("Created device")

    # start demo
    msg = "Hello AgilityContest"
    print(msg)
    show_message(device, msg, fill="white", font=proportional(CP437_FONT))
    time.sleep(1)

    count = 0
    while nowRunning != 0:
        r = nowRunning
        if ( count % 4 ) == 0:
            msg = "Ring 1 Agility STD-G2 "
        else:
            msg = "Now running %03d " % (r)
        print(msg)
        show_message(device, msg, fill="white", font=proportional(LCD_FONT), scroll_delay=0.05)
        with canvas(device) as draw:
            text(draw, (5, 0), "%03d " % (r), fill="white")
        count = count + 1
        time.sleep(5)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='matrix_demo arguments',
        formatter_class=argparse.ArgumentDefaultsHelpFormatter)

    parser.add_argument('--cascaded', '-n', type=int, default=1, help='Number of cascaded MAX7219 LED matrices')
    parser.add_argument('--block-orientation', type=int, default=0, choices=[0, 90, -90], help='Corrects block orientation when wired vertically')
    parser.add_argument('--rotate', type=int, default=0, choices=[0, 1, 2, 3], help='Rotate display 0=0°, 1=90°, 2=180°, 3=270°')

    args = parser.parse_args()

    try:
        w = threading.Thread(target=inputParser)
    	w.start()
        loop(args.cascaded, args.block_orientation, args.rotate)
    except KeyboardInterrupt:
        pass
