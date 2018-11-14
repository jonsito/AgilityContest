#!/usr/bin/env python

#
# Utility classes to get a single character from standard input
import getch

class NowRunning_Options:

    def exitMenu(self): # menu index 0
        # PENDING: confirm exit menu
        if self.menuItems[0]==1:
            self.menuItems[0]=0 # to avoid auto-exit on next "enter menu"
            self.endLoop=True

    def setRing(self): # menu index 1
        ring= int(self.menuEntries[1][self.menuItems[1]][0])
        self.dspHandler.setRing(ring)
        return

    def setRoundInfo(self): # menu index 2,3 y 4
        round=self.menuEntries[2][self.menuItems[2]][1]
        cat=self.menuEntries[3][self.menuItems[3]][1]
        grad=self.menuEntries[4][self.menuItems[4]][1]
        str= "%s %s - %s" %(round,cat,grad)
        self.dspHandler.setRoundInfo(str)
        return

    def setBrillo(self): # menu index 5
        return

    def setupEthernet(self): # menu index 6
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
        self.menuFunctions = [ self.exitMenu,self.setRing,self.setRoundInfo,self.setRoundInfo,self.setRoundInfo,self.setBrillo,self.setupEthernet,self.restartApp]
        self.menuEntries = [
            [ [ ' ', 'Ignorar' ], [ '*', 'Salir' ] ],
            [ [ '1','Ring 1'],['2','Ring 2'],['3','Ring 3'], ['4','Ring 4'] ],
            [ [ 'A','Agility'],['J','Jumping'],['S','Snooker'],['G','Gambler'],['K','K.O'],['T','TunnelCup'],[' ','Special' ] ],
            [ [ 'L','Large'],['M','Medium'],['S','Small'],['T','Toy'] ],
            [ [ '1','Grado 1'],['2','Grado 2'],['3','Grado 3'],['P','Pre-Agility'],['J','Junior'],['S','Senior'],['O','Open'] ],
            [ [ '1','1'],['2','2'],['3','3'], ['4','4'],['5','5'],['6','6'],['7','7'], ['8','8'],['9','9'] ],
            [ [ '1','On'],['0','Off'],['?','Info'],['>','Reiniciar'] ],
            [ [ ' ','Ignorar'], [ '*', 'Reiniciar' ] ]
        ]

    def runMenu(self,dspHandler):
        import time
        self.endLoop = False
        self.dspHandler = dspHandler

        dspHandler.setMenuState(
                self.menuIndex,
                self.menuNames[self.menuIndex],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][1] )
        while self.endLoop == False:
            c= getch.getch()
            print ("getch() returns: "+c)
            if c=='+' : # next entry inside item
                size= len(self.menuEntries[self.menuIndex])
                self.menuItems[self.menuIndex] = ( 1 + self.menuItems[self.menuIndex] ) % size
            if c=='-' : # previous entry inside item
                size= len(self.menuEntries[self.menuIndex])
                self.menuItems[self.menuIndex] = self.menuItems[self.menuIndex] - 1
                if self.menuItems[self.menuIndex] < 0:
                    self.menuItems[self.menuIndex] = size - 1
            if c=='\n': # nex menu entry
                size = len(self.menuItems)
                self.menuIndex = (1+self.menuIndex) % size
            if c in ['1','2','3','4','5','6','7','8','9']: # numbers 1..9
                size= len(self.menuEntries[self.menuIndex])
                # buscamos el indice que coincide con el numero indicado
                for i in range(size):
                    if self.menuEntries[self.menuIndex][i][0] == c:
                        self.menuItems[self.menuIndex] = i

            print ("menuIndex:%d entryName:%s entryValue:%s entryStr:%s" %(
                self.menuIndex,
                self.menuNames[self.menuIndex],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][1] ) )
            # ajustamos display
            dspHandler.setMenuState(
                self.menuIndex,
                self.menuNames[self.menuIndex],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][0],
                self.menuEntries[self.menuIndex][self.menuItems[self.menuIndex]][1] )
            # invocamos la funcion a ejecutar
            self.menuFunctions[self.menuIndex]()
        # exit to normal
        dspHandler.setMenuState(-1," "," "," ")
