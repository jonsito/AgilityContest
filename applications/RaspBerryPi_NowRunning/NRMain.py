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

import time
import argparse
import threading
import sys

import NRDisplay
import NRNetwork
import NROptions

def isInteger(val):
	try:
		int(val)
		return True
	except ValueError:
		return False

def inputParser():
	global displayHandler
	global networkHandler
	global menuHandler
	loop = True
	while loop==True:
		data = sys.stdin.readline()
		if (data == "\n") or (data == "+\n"):
			displayHandler.setNextRunning()
		elif data == "-\n":
		    displayHandler.setPrevRunning()
		elif data == "*9\n":
			networkHandler.stopNetwork()
			displayHandler.stopDisplay()
			loop = False
		elif data == "*0\n":
			print("Course walk countdown stop")
			displayHandler.setOobMessage("End of Course Walk",2)
			displayHandler.setCountDown(0)
		elif data == "*1\n":
			print("Course walk countdown start")
			displayHandler.setOobMessage("Starting Course Walk",2)
			displayHandler.setCountDown(menuHandler.getCountDown())
		elif data == "**\n":
			print("Enter in menu")
			menuHandler.runMenu(displayHandler,networkHandler)
		elif isInteger(data) == False:
			print ("Unrecongnized data entry: '%s'" % (data))
		else:
			print ("received '"+data+"'")
			displayHandler.setNowRunning(int(data))

if __name__ == "__main__":
	global displayHandler
	global networkHandler
	global menuHandler
	parser = argparse.ArgumentParser(description='matrix_demo arguments',
		formatter_class=argparse.ArgumentDefaultsHelpFormatter)
	parser.add_argument('--display','-d',type=str,default='max7219',help='Display mode "pygame" or "max7219"')
	parser.add_argument('--ring','-r', type=int, default=1, help='Ring to listen events from (1..4)')
	parser.add_argument('--interface','-i', type=str, default='',help='Use specific network interface to look for server')
	parser.add_argument('--cascaded', '-n', type=int, default=4, help='Number of cascaded MAX7219 LED matrices')
	parser.add_argument('--block-orientation', type=int, default=-90, choices=[0, 90, -90], help='Corrects block orientation when wired vertically')
	parser.add_argument('--rotate', type=int, default=2, choices=[0, 1, 2, 3], help='Rotate display 0=0°, 1=90°, 2=180°, 3=270°')

	args = parser.parse_args()

	try:
		threads=[]
		# init display handler
		displayHandler = NRDisplay.NRDisplay(args.display,args.cascaded, args.block_orientation, args.rotate)
		displayHandler.setRing(int(args.ring))
		# search network for connection
		networkHandler = NRNetwork.NRNetwork(args.interface,args.ring,displayHandler)
		# start display threads
		w = threading.Thread(target = displayHandler.setStdMessage) # setting of main message
		threads.append(w)
		w.start()
		w = threading.Thread(target = displayHandler.displayLoop) # display message loop
		threads.append(w)
		w.start()
		# create menu handler
		menuHandler = NROptions.NROptions()
		# start keyboard handler thread
		w = threading.Thread(target=inputParser)
		threads.append(w)
		w.start()
		# network event threads
		w = threading.Thread(target = networkHandler.networkLoop) # network thread loop
		threads.append(w)
		w.start()
		# wait for all threads to die
		for x in threads:
			x.join()
	except KeyboardInterrupt:
		networkHandler.stopNetwork()
		displayHandler.stopDisplay()
		pass
