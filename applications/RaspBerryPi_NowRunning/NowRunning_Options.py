#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.
# PYTHON_ARGCOMPLETE_OK

"""
Another vertical scrolling demo, images (used without permission)
from @pixel_dailies twitter feed.
"""

import time

class NowRunning_Options:

    def __init__(self):
        self.menuItem = 0
        self.menuNames = [ '<<<','Rng','Mng','Cat','Grd','Bri','Red','Rst']
        self.menuEntries = [
            [ [ ' ', 'Salir' ] ],
            [ [ '1','1'],['2','2'],['3','3'], ['4','4'] ],
            [ [ 'A','Agility'],['J','Jumping'],['S','Snooker'],['G','Gambler'],['K','K.O'],['T','TunnelCup'] ],
            [ [ 'L','Large'],['M','Medium'],['S','Small'],['T','Toy'] ],
            [ [ '1','Grado 1'],['2','Grado 2'],['3','Grado 3'],['P','Pre-Agility'],['J','Junior'],['S','Senior'] ],
            [ [ '1','1'],['2','2'],['3','3'], ['4','4'],['5','5'],['6','6'],['7','7'], ['8','8'],['9','9'] ],
            [ [ '1','On'],['0','Off'],['?','Info'],['>','Reiniciar'] ],
            [ [ ' ','Reboot'] ]
        ]

    def runMenu(self,dspHandler):
        # loop getting key events from input device
        # while NowRunning_Options.exitMenu == True:
        for i in range(8):
            menuItem=self.menuEntries[i]
            menuItemKey=menuItem[0][0]
            menuItemValue=menuItem[0][1]
            dspHandler.setMenuState(i,self.menuNames[i],menuItemKey,menuItemValue)
            time.sleep(5)
        # exit to normal
        dspHandler.setMenuState(-1," "," "," ")
