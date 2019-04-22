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
import os

import NRDisplay
import NRNetwork
import NROptions
import NRWeb

def isInteger(val):
	try:
		int(val)
		return True
	except ValueError:
		return False

def restart(mode): # 0:exit 1:restart 2:shutdown
	import getch
	global displayHandler
	global networkHandler
	global webHandler
	global displayName
	msgoob = [ 'Exit','Restart','Shut down']
	msgs = [ 'Exiting...','Restarting...','Shutting down...']
	displayHandler.setOobMessage("Confirm "+msgoob[mode]+" +/-?",1)
	time.sleep(2)
	displayHandler.setMenuMessage('+/-?')
	# get confirm. clear prompt
	c= getch.getch()
	displayHandler.setMenuMessage('')
	if c!='+':
		return True # continue loop
	displayHandler.setOobMessage(msgs[mode],1)
	time.sleep(2)
	# start closing threads
	networkHandler.stopNetwork()
	displayHandler.stopDisplay()
	webHandler.stopWeb()
	if displayName == "pygame":
		# do not restart nor shutdown on pygame, just stop
		return False
	else:
		os._exit(mode)

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
			loop=restart(0)
		elif data == "*0\n":
			print("Return to normal mode")
			displayHandler.setCountDown(0)
			displayHandler.setClockMode(False)
		elif data == "*1\n":
			print("Enter Course walk mode")
			# parar reloj, activar reconocimiento
			displayHandler.setClockMode(False)
			displayHandler.setOobMessage("Course Walk",2)
			displayHandler.setCountDown(menuHandler.getCountDown())
		elif data == "*2\n":
			print("Enter clock mode")
			# parar reconocimiento, activar relog
			displayHandler.setOobMessage("Clock Mode",2)
			displayHandler.setCountDown(0)
			displayHandler.setClockMode(True)
		elif data == "**\n":
			print("Enter in menu")
			# paramos reloj y reconocimiento
			displayHandler.setCountDown(0)
			displayHandler.setClockMode(False)
			res = menuHandler.runMenu(displayHandler,networkHandler)
			if res > 0: # 1:stop 2:restart 3:shutdown
				loop=restart(res-1) # 0:stop 1:restart 2:shuthdown
		elif isInteger(data) == False:
			print ("Unrecongnized data entry: '%s'" % (data))
		else:
			print ("received '"+data+"'")
			displayHandler.setNowRunning(int(data))
	# end def
	print("inputLoopThread() exiting")
# end def

if __name__ == "__main__":
	global displayName
	global displayHandler
	global networkHandler
	global menuHandler
	global webHandler

	parser = argparse.ArgumentParser(description='SuTurno cmdline arguments',
		formatter_class=argparse.ArgumentDefaultsHelpFormatter)
	parser.add_argument('--display','-d',type=str,default='hub08',help='Display mode "pygame", "max7219", or "hub08"')
	parser.add_argument('--ring','-r', type=int, default=1, help='Ring to listen events from (1..4)')
	parser.add_argument('--interface','-i', type=str, default='',help='Use specific network interface to look for server')
	parser.add_argument('--port','-p', type=int, default=80, help='Port to attach Web server interface (0:disable)')
	parser.add_argument('--cascaded', '-n', type=int, default=4, help='Number of cascaded MAX7219 LED matrices')
	parser.add_argument('--block_orientation', type=int, default=-90, choices=[0, 90, -90], help='Corrects block orientation when wired vertically')
	parser.add_argument('--rotate', type=int, default=2, choices=[0, 1, 2, 3], help='Rotate display 0=0°, 1=90°, 2=180°, 3=270°')

	args = parser.parse_args()
	displayName= args.display
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
		# web server event thread
		if args.port != 0 :
			webHandler = NRWeb.NRWeb(args.port,displayHandler,networkHandler,menuHandler)
			w = threading.Thread(target = webHandler.webLoop) # network thread loop
			threads.append(w)
			w.start()

		# wait for all threads to die
		for x in threads:
			x.join()
	except KeyboardInterrupt:
		networkHandler.stopNetwork()
		displayHandler.stopDisplay()
		webHandler.stopWeb()
		pass
