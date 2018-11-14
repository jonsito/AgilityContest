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
import NowRunning_Options

def inputParser():
    global displayHandler
    while  True:
        data = sys.stdin.readline()
        if data == "\n":
            displayHandler.setNextRunning()
        elif data == "000\n":
            menuHandler.runMenu(displayHandler)
        else:
            print ("received '"+data+"'")
            displayHandler.setNowRunning(int(data))
        if data == "0\n":
            break

if __name__ == "__main__":
    global displayHandler
    global networkHandler
    global menuHandler
    parser = argparse.ArgumentParser(description='matrix_demo arguments',
        formatter_class=argparse.ArgumentDefaultsHelpFormatter)
    parser.add_argument('--display','-d',type=str,default='max7219',help='Display mode "pygame" or "max7219"')
    parser.add_argument('--ring','-r', type=int, default=1, help='Ring to listen events from (1..4)')
    parser.add_argument('--interface','-i', type=str, default='eth0',help='Network interface to look for server, or "none"')
    parser.add_argument('--cascaded', '-n', type=int, default=4, help='Number of cascaded MAX7219 LED matrices')
    parser.add_argument('--block-orientation', type=int, default=-90, choices=[0, 90, -90], help='Corrects block orientation when wired vertically')
    parser.add_argument('--rotate', type=int, default=2, choices=[0, 1, 2, 3], help='Rotate display 0=0°, 1=90°, 2=180°, 3=270°')

    args = parser.parse_args()

    try:
        # init display handler
        displayHandler = NowRunning_Display.NowRunning_Display(args.display,args.cascaded, args.block_orientation, args.rotate)
        displayHandler.setRing(int(args.ring))
        # search network for connection
        networkHandler = NowRunning_Network.NowRunning_Network(args.interface,displayHandler)
        # start display threads
        w = threading.Thread(target = displayHandler.setStdMessage) # setting of main message
    	w.start()
    	w = threading.Thread(target = displayHandler.displayLoop) # display message loop
        w.start()
        # create menu handler
        menuHandler = NowRunning_Options.NowRunning_Options()
        # start keyboard handler thread
        w = threading.Thread(target=inputParser)
    	w.start()
    	# network event threads
        server=networkHandler.lookForServer(args.ring)
        if server != "0.0.0.0":
            w = threading.Thread(target = networkHandler.eventParser) # display message loop
            w.start()
    except KeyboardInterrupt:
        displayHandler.setNowRunning(0)
        networkHandler.stopEventParser()
        pass
