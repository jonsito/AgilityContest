#!/usr/bin/env python

#
# Utility classes to get a single character from standard input
import getch
import time

class NROptions:

    def exitMenu(self): # menu index 0
        # PENDING: confirm exit menu
        if self.menuItems[0]==1:
            self.menuItems[0]=0 # to avoid auto-exit on next "enter menu"
            self.endLoop=True

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
        str= "%s %s - %s" %(round,cat,grad)
        self.dspHandler.setRoundInfo(str)
        return

    def setBrillo(self): # menu index 5
        self.dspHandler.setBrightness(int(self.menuEntries[5][self.menuItems[5]][0]))
        return

    def setupEthernet(self): # menu index 6
        code=self.menuItems[6]
        if code==0: # status
            self.sendMenuMessage("")
            self.netHandler.showIPAddress()
            time.sleep(2)
            self.sendMenuMessage()
        if code==1: # start
            self.netHandler.setEnabled(True)
        if code==2: # stop
            self.netHandler.setEnabled(False)
        if code==3: # restart
            self.netHandler.reconnect()
        return

    def restartApp(self): # menu index 7
        # PENDING: ask confirm reinit application ( and perform it :-) )
        if self.menuItems[7]==1:
            self.menuItems[7]=0 # to avoid auto-reset on next "enter menu"
            self.endLoop=True
        return

    def __init__(self):
        self.endLoop = False
        self.menuIndex = 0
        self.menuItems = [ 0, 0, 0, 0, 0, 5, 0, 0 ]
        self.menuNames = [ '<<<','Rng','Mng','Cat','Grd','Bri','Red','Rst']
        self.menuFunctions = [
            self.exitMenu,
            self.setRing,
            self.setRoundInfo,
            self.setRoundInfo,
            self.setRoundInfo, # same function to be called on set round, category and grade
            self.setBrillo,
            self.setupEthernet,
            self.restartApp
        ]
        self.menuEntries = [
            [ [ ' ', 'Ignorar' ], [ '*', 'Salir' ] ],
            [ [ '1','Ring 1'],['2','Ring 2'],['3','Ring 3'], ['4','Ring 4'] ],
            [ [ 'A','Agility'],['J','Jumping'],['S','Snooker'],['G','Gambler'],['K','K.O'],['T','TunnelCup'],[' ','Special' ] ],
            [ [ 'L','Large'],['M','Medium'],['S','Small'],['T','Toy'] ],
            [ [ '1','Grado 1'],['2','Grado 2'],['3','Grado 3'],['P','Pre-Agility'],['J','Junior'],['S','Senior'],['O','Open'] ],
            [ [ '1','1'],['2','2'],['3','3'], ['4','4'],['5','5'],['6','6'],['7','7'], ['8','8'],['9','9'] ],
            [ [ '?','Info'],[ '^','On'],['v','Off'],['*','Reiniciar'] ],
            [ [ ' ','Ignorar'], [ '*', 'Reiniciar' ] ]
        ]

    def sendMenuMessage(self,msg="empty"):
        if msg=="empty":
            msg="%s%s" % ( self.menuNames[self.menuIndex], self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0])
        self.dspHandler.setMenuMessage(msg)

    def runMenu(self,dspHandler,netHandler):
        import time
        self.endLoop = False
        self.dspHandler = dspHandler
        self.netHandler = netHandler

        self.sendMenuMessage()
        while self.endLoop == False:
            c= getch.getch()
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
            if c in ['1','2','3','4','5','6','7','8','9']: # numbers 1..9
                size= len(self.menuEntries[self.menuIndex])
                # buscamos el indice que coincide con el numero indicado
                for i in range(size):
                    if self.menuEntries[self.menuIndex][i][0] == c:
                        self.menuItems[self.menuIndex] = i
            #depuracion
            print ("menuIndex:%d entryName:%s entryValue:%s entryStr:%s" %(
                self.menuIndex,
                self.menuNames[self.menuIndex],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][1] ) )
            # ajustamos display con datos del menu
            self.sendMenuMessage()
            # invocamos la funcion a ejecutar en funcion de la seleccion
            self.menuFunctions[self.menuIndex]()
        # exit to normal display mode
        self.sendMenuMessage("")
