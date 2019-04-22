# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.


import atexit
import json
import sys
import time
import spidev

# import RPi.GPIO as GPIO
import GPIOEmu as GPIO

from luma.core.device import device
import luma.core.const
from luma.core.interface.serial import noop

# Hub08 pin assignments
spi_clock = 23 # GPIO 11 / SPI0_CLK / HUB08_CLOCK
spi_dout = 19 # GPIO 10 / SPI0_DATA_OUT / HUB08_RED1
spi_din = 21 # GPIO 09 / SPI0_DATA_IN --- NOT USED
spi_cs = 24 # GPIO 08 / SPI0_CE_0 --- NOT USED

latch = 22 # GPIO 25
enable = 18 # GPIO 24
addr0 = 16 # GPIO 23
addr1 = 11 # GPIO 17
addr2 = 13 # GPIO 27
addr3 = 15 # GPIO 22

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
    refresh_period = 20 # mseg
    mode='1' # 1-color only
    width=64
    height=16
    rotate=0
    display_data = [ #eigh bytes (64 pixels) on each row
        [0,0,0,0,0,0,0,0], # row 0
        [0,0,0,0,0,0,0,0], # row 1
        [0,0,0,0,0,0,0,0], # row 2
        [0,0,0,0,0,0,0,0], # row 3
        [0,0,0,0,0,0,0,0], # row 4
        [0,0,0,0,0,0,0,0], # row 5
        [0,0,0,0,0,0,0,0], # row 6
        [0,0,0,0,0,0,0,0], # row 7
        [0,0,0,0,0,0,0,0], # row 8
        [0,0,0,0,0,0,0,0], # row 9
        [0,0,0,0,0,0,0,0], # row 10
        [0,0,0,0,0,0,0,0], # row 11
        [0,0,0,0,0,0,0,0], # row 12
        [0,0,0,0,0,0,0,0], # row 13
        [0,0,0,0,0,0,0,0], # row 14
        [0,0,0,0,0,0,0,0]  # row 15
        ]
    cur_row = 0 # row being serialized

    def refresh():
        if self.state == True:
            # disable display before latch data
            GPIO.output(enable,True)
            #transfer row data.
            spi.xfer(self.display_data[self.cur_row])
            # store sent data into current row
            GPIO.output(addr0, (self.cur_row & 0x01)!=0 )
            GPIO.output(addr1, (self.cur_row & 0x02)!=0 )
            GPIO.output(addr2, (self.cur_row & 0x04)!=0 )
            GPIO.output(addr3, (self.cur_row & 0x08)!=0 ) # select row
            GPIO.output(latch,False)
            GPIO.output(latch,True)
            GPIO.output(enable,False) # re-enable display
            # and finally increase cursor
            self.cur_row = (self.cur_row + 1 ) % 16
        time.sleep(refresh_period)

    def initialize(self):
        # Setup breakout board
        GPIO.setmode(GPIO.BOARD) # number pins according board pin number ( alternative to GPIO.BCM )

        #SPI
        GPIO.setup(spi_clock,GPIO.OUT)
        GPIO.setup(spi_dout,GPIO.OUT)
        GPIO.setup(spi_din,GPIO.IN)
        GPIO.setup(spi_cs,GPIO.OUT)
        GPIO.output(spi_clock, True) # default high
        GPIO.output(spi_dout, True) # default high
        GPIO.output(spi_cs, False) # default low (enabled)

        # spi = spidev.SpiDev()
        # spi.open(0, 0)
        # spi.max_speed_hz = 7629

        #GPIOs
        GPIO.setup(latch,GPIO.OUT)
        GPIO.setup(enable,GPIO.OUT)
        GPIO.setup(addr0,GPIO.OUT)
        GPIO.setup(addr1,GPIO.OUT)
        GPIO.setup(addr2,GPIO.OUT)
        GPIO.setup(addr3,GPIO.OUT)
        GPIO.output(enable, False) # turn on (negative logic)
        GPIO.output(latch, True) # turn off (negative logic)
        GPIO.output(addr0, False) # default 0
        GPIO.output(addr1, False) # default 0
        GPIO.output(addr2, False) # default 0
        GPIO.output(addr3, False) # default 0

        # GPS module ( RX-TX)

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
        im = super(hub08, self).preprocess(image)
        pixels=list(im.getdata())
        width, height = im.size
        # print([pixels[i * width:(i + 1) * width] for i in range(height)])
        #  from:
        # https://github.com/Seeed-Studio/Ultrathin_LED_Matrix/blob/master/LEDMatrix.cpp
        # iterate over 16 rows
        for row in range(0,15): # 16 rows
            # send 64 bits to shift-register
            for column in range(0,63): # 64 columns
                byte = int(column/8) # 8 bits per byte
                mask  = 0x01 << int(column%8)
                pixel= pixels[ column+ 16*row]
                if pixel != 0:
                    cur = self.display_data[row][byte] | mask
                else:
                    cur = self.display_data[row][byte] & ~mask
                self.display_data[row][byte] = cur

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

    def contrast(self, level):
        """
        Switches the display contrast to the desired level, in the range
        0-255. Note that setting the level to a low (or zero) value will
        not necessarily dim the display to nearly off. In other words,
        this method is **NOT** suitable for fade-in/out animation.

        hub08 display has no way to set up bright. The only way is varying
        refresh period.

        :param level: Desired contrast level in the range of 0-255.
        :type level: int
        """
        assert(0 <= level <= 255)
        self.period= level # from 1 hz to 255 hz

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