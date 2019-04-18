# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.


import atexit
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
        super(hub08, self).__init__(serial_interface=noop())
        self.capabilities(width, height, rotate, mode)
        self.image = None
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
        if self.state == False:
            return

        for row in range(0,15): # 16 rows
            for column in range(0,63): # 64 columns
                pixel=image.getPixel((column,row))
                GPIO.output(clock,False)
                if pixel == 0:
                    GPIO.output(red1,False)
                    GPIO.output(red2,False)
                else:
                    GPIO.output(red1,self.contrast0)
                    GPIO.output(red2,self.contrast1)
                GPIO.output(clock,True)
#  from:
# https://github.com/Seeed-Studio/Ultrathin_LED_Matrix/blob/master/LEDMatrix.cpp
#
#   this routine send a row to the display on each interaction
#
#    void LEDMatrix::scan()
#    {
#        static uint8_t row = 0;  // from 0 to 15
#
#        if (!state) {
#            return;
#        }
#
#        uint8_t *head = displaybuf + row * (width / 8);
#        for (uint8_t line = 0; line < (height / 16); line++) {
#            uint8_t *ptr = head;
#            head += width * 2;              // width * 16 / 8
#
#            for (uint8_t byte = 0; byte < (width / 8); byte++) {
#                uint8_t pixels = *ptr;
#                ptr++;
#                pixels = pixels ^ mask;     // reverse: mask = 0xff, normal: mask =0x00
#                for (uint8_t bit = 0; bit < 8; bit++) {
#                    digitalWrite(clk, LOW);
#                    digitalWrite(r1, pixels & (0x80 >> bit));
#                    digitalWrite(clk, HIGH);
#                }
#            }
#        }
#
#        // disable display
#        digitalWrite(enable, HIGH);
#
#        // select row
#        digitalWrite(data0, (row & 0x01));
#        digitalWrite(data1, (row & 0x02));
#        digitalWrite(data2, (row & 0x04));
#        digitalWrite(data3, (row & 0x08));
#
#        // latch data
#        digitalWrite(latch, LOW);
#        digitalWrite(latch, HIGH);
#        digitalWrite(latch, LOW);
#
#        // enable display
#        digitalWrite(enable, LOW);
#
#        // increase row count
#        row = (row + 1) & 0x0F;
#    }

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
        GPIO.output(output_enable,True)
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