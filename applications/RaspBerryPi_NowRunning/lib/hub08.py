# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.


import atexit
import json
# import RPi.GPIO as GPIO
import GPIOEmu as GPIO

from luma.core.device import device
import luma.core.const
from luma.core.interface.serial import noop


# Hub08 pin assignments
clock = 13 # GPIO 27
latch = 15 # GPIO 22
enable = 16 # GPIO 23
data0 = 26 # GPIO 7
data1 = 24 # GPIO 8
data2 = 21 # GPIO 9
data3 = 19 # GPIO 10
red1 = 11 # GPIO 17
red2 = 12 # GPIO 18
# Green1 = 18 # GPIO 24
# Green2 = 23 # GPIO 11

class hub08(device):
    """
    Implementation for HUB08 led display driver for luma.core.device

    .. note::
        Direct use of the :func:`command` and :func:`data` methods are
        discouraged: Screen updates should be effected through the
        :func:`display` method, or preferably with the
        :class:`luma.core.render.canvas` context manager.
    """

    # variables
    state = False  # 0:inactive 1:active
    contrast0=True
    contrast1=True
    mode='1'
    width=64
    height=16
    rotate=0

    def initialize(self):
    # Setup breakout board
        GPIO.setmode(GPIO.BOARD) # number pins according board pin number ( alternative to GPIO.BCM )
        for pin in (clock,latch,enable,data0,data1,data2,data3,red1,red2):
            GPIO.setup(pin, GPIO.OUT) # set up as output

        GPIO.output(clock, True) # turn off
        GPIO.output(latch, True) # turn off
        GPIO.output(enable, True) # turn off
        GPIO.output(data0, False) # turn off
        GPIO.output(data1, False) # turn off
        GPIO.output(data2, False) # turn off
        GPIO.output(data3, False) # turn off
        GPIO.output(red1, True) # turn off
        GPIO.output(red2, True) # turn off
        self.state=False

    def __init__(self,width=64, height=16, rotate=0, mode="1"):
        super(hub08, self).__init__(const=None,serial_interface=noop)
        self.capabilities(width, height, rotate,mode="1")
        self.image = None
        self.size=(width,height)
        def shutdown_hook():  # pragma: no cover
            try:
                self.cleanup()
            except:
                pass

        atexit.register(shutdown_hook)
        self.initialize()

    # at this momment onlu 64x16, no rotate, and single color is supported
    def capabilities(self,width, height, rotate, mode='1'):
        assert(width == 64)
        assert(height == 16)
        assert(rotate == 0) # no rotation (yet)
        assert(mode == '1') # luma modes are "1", "rgb" and "rgba"
        self.width=width
        self.height=height
        self.rotate=rotate
        self.mode=mode


    def display(self,image):
        assert(image.mode == self.mode)
        assert(image.size == self.size)
        if self.state == False:
            return
        print("on display")

        image = super(hub08, self).preprocess(image)

        #  from:
        # https://github.com/Seeed-Studio/Ultrathin_LED_Matrix/blob/master/LEDMatrix.cpp
        # iterate over 16 rows
        for row in range(0,15): # 16 rows
            # send 64 bits to shift-register
            for column in range(0,63): # 64 columns
                pixel=image.getpixel((column,row))
                GPIO.output(clock,False)
                if pixel == 0: # single color
                    GPIO.output(red1,False)
                    GPIO.output(red2,False)
                else:
                    GPIO.output(red1,self.contrast0)
                    GPIO.output(red2,self.contrast1)
                GPIO.output(clock,True)

            # store sent data into current row
            GPIO.output(enable,True) # disable display
            GPIO.output(data0, (row & 0x01)!=0 )
            GPIO.output(data1, (row & 0x02)!=0 )
            GPIO.output(data2, (row & 0x04)!=0 )
            GPIO.output(data3, (row & 0x08)!=0 ) # select row
            GPIO.output(latch,False)
            GPIO.output(latch,True)
            GPIO.output(latch,False) # transfer data
            GPIO.output(enable,False) # re-enable display

    def show(self):
        """
        Sets the display mode ON, waking the device out of a prior
        low-power sleep mode.
        """
        self.state=True

    def hide(self):
        """
        Switches the display mode OFF, putting the device in low-power
        sleep mode.
        remember output enable has negative logic
        """
        GPIO.output(enable,True)
        self.state=False

    # to Verify: assume that red1 and red2 controls led intensity 00 01 10 11
    def contrast(self, level):
        """
        Switches the display contrast to the desired level, in the range
        0-255. Note that setting the level to a low (or zero) value will
        not necessarily dim the display to nearly off. In other words,
        this method is **NOT** suitable for fade-in/out animation.

        :param level: Desired contrast level in the range of 0-255.
        :type level: int
        """
        assert(0 <= level <= 255)
        if level < 64:
            self.contrast0=False
            self.contrast1=False
        elif level < 128:
            self.contrast0=True
            self.contrast1=False
        elif level < 128:
            self.contrast0=False
            self.contrast1=True
        else:
            self.contrast0=True
            self.contrast1=True

    def cleanup(self):
        """
        Attempt to switch the device off or put into low power mode (this
        helps prolong the life of the device), clear the screen and close
        resources associated with the underlying serial interface.

        If :py:attr:`persist` is True, the device will not be switched off.

        This is a managed function, which is called when the python processs
        is being shutdown, so shouldn't usually need be called directly in
        application code.
        """
        if not self.persist:
            self.hide()
            self.clear()
        self._serial_interface.cleanup()