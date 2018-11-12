#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.

import time
import argparse
import threading
import sys

import NowRunning_Display
import NowRunning_Network

def inputParser():
    global displayHandler
    while  True:
        data = sys.stdin.readline()
        if data == "\n":
            displayHandler.setNextRunning()
        else:
            print ("received '"+data+"'")
            displayHandler.setNowRunning(int(data))
        if nowRunning == 0:
            break

if __name__ == "__main__":
    global displayHandler
    parser = argparse.ArgumentParser(description='matrix_demo arguments',
        formatter_class=argparse.ArgumentDefaultsHelpFormatter)

    parser.add_argument('--cascaded', '-n', type=int, default=1, help='Number of cascaded MAX7219 LED matrices')
    parser.add_argument('--block-orientation', type=int, default=0, choices=[0, 90, -90], help='Corrects block orientation when wired vertically')
    parser.add_argument('--rotate', type=int, default=0, choices=[0, 1, 2, 3], help='Rotate display 0=0°, 1=90°, 2=180°, 3=270°')

    args = parser.parse_args()

    try:
        # ask for ring
        # askForRing()
        ring=1
        # search network for connection
        networkHandler = NowRunning_Network(ring)
        # init display handler
        displayHandler = NowRunning_Display(args.cascaded, args.block_orientation, args.rotate)
        # start display threads
        w = threading.Thread(target = setStdMessage) # setting of main message
    	w.start()
    	w = threading.Thread(target = displayHandler.displayLoop) # display message loop
        w.start()
        # start keyboard handler thread
        w = threading.Thread(target=inputParser)
    	w.start()
    	# network event threads
        server=networkHandler.lookForServer()
        if server != "0.0.0.0":
            w = threading.Thread(target = NetworkHandler.eventParser) # display message loop
            w.start()
    except KeyboardInterrupt:
        pass
