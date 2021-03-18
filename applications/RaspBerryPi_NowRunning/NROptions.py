#!/usr/bin/env python3
# -*- coding: utf-8 -*-
#
# Copyright  2018-2021 by Juan Antonio Martinez ( juansgaviota at gmail dot com )
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
#
import getch
import time
import NRVersion

class NROptions:

	def exitMenu(self): # menu index 0
		self.endLoop=True
		return

	def setRing(self): # menu index 1
		ring= int(self.menuEntries[1][self.menuItems[1]][0])
		# send ring info to display handler
		self.dspHandler.setRing(ring)
		# send ring info to network handler
		self.netHandler.setRing(ring)
		return

	def setRoundInfo(self): # menu index 2,3 y 4
		round=self.menuEntries[2][self.menuItems[2]][1]
		cat=self.menuEntries[3][self.menuItems[3]][1]
		grad=self.menuEntries[4][self.menuItems[4]][1]
		self.dspHandler.setRoundInfo(round,cat,grad)
		return

	def getCountDown(self):
		return self.countDown*60

	def setDirectCountDown(self,val):
		self.countDown=val

	def setCountDown(self): # menu index 5
		mins=self.menuEntries[5][self.menuItems[5]][0]
		self.countDown=int(mins)
		print ("set course walk time to "+mins)

	def setBrillo(self): # menu index 6
		self.dspHandler.setBrightness(int(self.menuEntries[6][self.menuItems[6]][0]))
		return

	def setupEthernet(self): # menu index 7
		code=self.menuItems[7]
		if code==0: # status
			self.sendMenuMessage("")
			self.netHandler.showIPAddress()
			time.sleep(2)
			self.sendMenuMessage()
		if code==1: # server address
			self.sendMenuMessage("")
			self.netHandler.showServerAddress()
			time.sleep(2)
			self.sendMenuMessage()
		if code==2: # start
			self.netHandler.setEnabled(True)
		if code==3: # stop
			self.netHandler.setEnabled(False)
		if code==4: # restart
			self.netHandler.restartConnection()
		return

	def miscFunctions(self): # menu index 8
		code=self.menuItems[8]
		if code==0: # About
			msg=NRVersion.NRVersion().toString()
			# turn menu off, send msg and back menu again
			self.sendMenuMessage("")
			self.dspHandler.setOobMessage(msg,2)
			time.sleep(2)
			self.sendMenuMessage()
		return

	def powerOff(self): # menu index 9
		code=self.menuItems[9] # 0:Stop 1:Restart 2:Halt
		# set menuIndex and menuItems to zero
		self.menuIndex=0
		self.menuItems[0]=0
		self.endLoop=True
		self.returnCode = code+1
		return

	def __init__(self):
		self.countDown=7  # course walk default time as defined in self.menuItems
		self.endLoop = False
		self.menuIndex = 0
		self.menuItems = [ 0, 0, 0, 0, 0, 2, 5, 0, 0, 0 ]
		self.menuAutoExec = [ 0, 0, 0, 0, 0, 0, 1, 0, 0, 0 ]
		self.menuNames = [
			[' <<'],
			['Rng'],
			['Mng'],
			['Cat'],
			['Grd'],
			['Rec'],
			['Bri'],
			['IP ','Srv','Net','Net','Rec'],
			['Inf'],
			['Sto','Rst','Off'] ]
		self.menuFunctions = [
			self.exitMenu,
			self.setRing,
			self.setRoundInfo, # round name
			self.setRoundInfo, # category
			self.setRoundInfo, # grade. Same function to be called on set round, category and grade
			self.setCountDown, # course walk time
			self.setBrillo, # LED Display brighness
			self.setupEthernet,
			self.miscFunctions,
			self.powerOff
		]
		self.menuEntries = [
			[ [ '<', 'Volver' ]],
			[ [ '1','Ring 1'],['2','Ring 2'],['3','Ring 3'], ['4','Ring 4'] ],
			[ [ 'A','Agility'],['J','Jumping'],['S','Snooker'],['G','Gambler'],['K','K.O'],['T','TunnelCup'],[' ','Special' ] ],
			[ [ 'X','X-Large'],[ 'L','Large'],['M','Medium'],['S','Small'],['T','Toy'] ],
			[ [ '1','Grado 1'],['2','Grado 2'],['3','Grado 3'],['P','Pre-Agility'],['J','Junior'],['S','Senior'],['O','Open'] ],
			[ [ '0','Stop'],[ '6','6 min.'],['7','7 min.'],['8','8 min.'],['9','9 min'] ],
			[ [ '1','1'],['2','2'],['3','3'], ['4','4'],['5','5'],['6','6'],['7','7'], ['8','8'],['9','9'] ],
			[ [ '?','Ip Address'],['?','Server Address'],[ '\x18','Network On'],['\x19','Network Off'],['\x1a','Reinit Network'] ], # ^,v, and > arrows
			[ [ 'o','About'] ],
			[ [ 'p','Exit'],[' ','Restart'],[' ','Pwr Off' ] ]
		]

	def sendMenuMessage(self,msg="empty"):
		if msg=="empty":
			# prefix
			nidx=self.menuItems[self.menuIndex]%len(self.menuNames[self.menuIndex])
			prefix=self.menuNames[self.menuIndex][nidx]
			suffix= self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0]
			# suffix
			msg="%s%s" % (prefix,suffix)
		self.dspHandler.setMenuMessage(msg)

	def runMenu(self,dspHandler,netHandler):
		import time
		self.endLoop = False
		self.dspHandler = dspHandler
		self.netHandler = netHandler
		self.returnCode = 0 # 0:normal, 1:stop 2:restart 3:shutdown

		self.sendMenuMessage()
		while self.endLoop == False:
			c= getch.getch()
			# print("getch() return '%c'" %(c))
			if c=='+' : # next entry inside item
				size= len(self.menuEntries[self.menuIndex])
				self.menuItems[self.menuIndex] = ( 1 + self.menuItems[self.menuIndex] ) % size
			if c=='-' : # previous entry inside item
				size= len(self.menuEntries[self.menuIndex])
				self.menuItems[self.menuIndex] = self.menuItems[self.menuIndex] - 1
				if self.menuItems[self.menuIndex] < 0:
					self.menuItems[self.menuIndex] = size - 1
			if (c=='\n') or (c=='\r'): # next menu entry
				size = len(self.menuItems)
				self.menuIndex = (1+self.menuIndex) % size
			if (c=='\b') or (c=='\x7f'): # backspace or delete -> exit menu
				self.endLoop=True
				continue
			if (c=='*'): # * activate selected option
				# invocamos la funcion a ejecutar en funcion de la seleccion
				dspHandler.setGlitch(1)
				self.menuFunctions[self.menuIndex]()
			if c in ['1','2','3','4','5','6','7','8','9']: # numbers 1..9
				size= len(self.menuEntries[self.menuIndex])
				# buscamos el indice que coincide con el numero indicado
				for i in range(size):
					if self.menuEntries[self.menuIndex][i][0] == str(c):
						self.menuItems[self.menuIndex] = i
			#depuracion
			# print ("menuIndex:%d entryName:%s entryValue:%s entryStr:%s" %(
			#	self.menuIndex,
			#	self.menuNames[self.menuIndex],
			#	self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0],
			#	self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][1] ) )
			# ajustamos display con datos del menu
			self.sendMenuMessage()
			# if marked as "autoexec", run associated method
			if self.menuAutoExec[self.menuIndex] == 1:
				self.menuFunctions[self.menuIndex]()
		# exit to normal display mode
		self.sendMenuMessage("")
		return self.returnCode
	# end def
