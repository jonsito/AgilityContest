# -*- coding: utf-8 -*-
# Copyright (c) 2017-18 Richard Hull and contributors
# See LICENSE.rst for details.


import atexit
import json
import sys
import time
import spidev
import threading

import RPi.GPIO as GPIO
# import GPIOEmu as GPIO

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

# table to linearize bright values for setting pwm duty cycle
# from https://forum.arduino.cc/index.php?topic=96839.0
brightness_table= [
    0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,
    0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  0,  1,  1,  1,  1,
    1,  1,  1,  1,  1,  1,  1,  1,  1,  2,  2,  2,  2,  2,  2,  2,
    2,  3,  3,  3,  3,  3,  3,  3,  4,  4,  4,  4,  4,  5,  5,  5,
    5,  6,  6,  6,  6,  7,  7,  7,  7,  8,  8,  8,  9,  9,  9, 10,
    10, 10, 11, 11, 11, 12, 12, 13, 13, 13, 14, 14, 15, 15, 16, 16,
    17, 17, 18, 18, 19, 19, 20, 20, 21, 21, 22, 22, 23, 24, 24, 25,
    25, 26, 27, 27, 28, 29, 29, 30, 31, 32, 32, 33, 34, 35, 35, 36,
    37, 38, 39, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 50,
    51, 52, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 66, 67, 68,
    69, 70, 72, 73, 74, 75, 77, 78, 79, 81, 82, 83, 85, 86, 87, 89,
    90, 92, 93, 95, 96, 98, 99,101,102,104,105,107,109,110,112,114,
    115,117,119,120,122,124,126,127,129,131,133,135,137,138,140,142,
    144,146,148,150,152,154,156,158,160,162,164,167,169,171,173,175,
    177,180,182,184,186,189,191,193,196,198,200,203,205,208,210,213,
    215,218,220,223,225,228,231,233,236,239,241,244,247,249,252,255
]

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
	state = False # true: refresh active, false: do not refresh
	brightness = 0  # 0: full brigthness .. 100: turn off
	refresh_period = 0.0005 # 0.5 mseg
	mode='1' # 1-color only
	width=64
	height=16
	rotate=0
	display_data = [ #eigh bytes (64 pixels) on each row
		[255,0,0,0,0,0,0,0], # row 0
		[0,255,0,0,0,0,0,0], # row 1
		[0,0,255,0,0,0,0,0], # row 2
		[0,0,0,255,0,0,0,0], # row 3
		[0,0,0,0,255,0,0,0], # row 4
		[0,0,0,0,0,255,0,0], # row 5
		[0,0,0,0,0,0,255,0], # row 6
		[0,0,0,0,0,0,0,255], # row 7
		[0,0,0,0,0,0,0,255], # row 8
		[0,0,0,0,0,0,255,0], # row 9
		[0,0,0,0,0,255,0,0], # row 10
		[0,0,0,0,255,0,0,0], # row 11
		[0,0,0,255,0,0,0,0], # row 12
		[0,0,255,0,0,0,0,0], # row 13
		[0,255,0,0,0,0,0,0], # row 14
		[255,0,0,0,0,0,0,0]  # row 15
		]
	cur_row = 0 # row being serialized

	def refresh(self):
		while self.refresh_period >0: # loop until end of thread signaled
			if self.state == True:
				row=self.display_data[self.cur_row]
				self.enabled.ChangeDutyCycle(100) # turn off display ( set enable gpio high )

				for byte in row:
					for bit in range(8):
						val = (byte & (1<<bit)) != 0
						GPIO.output(spi_dout,val)
						GPIO.output(spi_clock,False)
						GPIO.output(spi_clock,True)

				#transfer row data using SPI
				# self.spi.writebytes(row)
				# store sent data into current row
				GPIO.output(addr0, (self.cur_row & 0x01)!=0 )
				GPIO.output(addr1, (self.cur_row & 0x02)!=0 )
				GPIO.output(addr2, (self.cur_row & 0x04)!=0 )
				GPIO.output(addr3, (self.cur_row & 0x08)!=0 ) # select row
				GPIO.output(latch,False)
				#time.sleep(.001)
				GPIO.output(latch,True)
				self.enabled.ChangeDutyCycle(self.brightness) # re-enable display
				# and finally increase cursor
				self.cur_row = (self.cur_row + 1 ) & 0x0F
			time.sleep(self.refresh_period)

	def initialize(self):
		self.state = False
		# Setup breakout board
		GPIO.setmode(GPIO.BOARD) # number pins according board pin number ( alternative to GPIO.BCM )

		# gpios related to transfer data
		# this code should rewritten to use SPI, but in gpio mode works fine
		GPIO.setup(spi_clock,GPIO.OUT)
		GPIO.setup(spi_dout,GPIO.OUT)
		GPIO.setup(spi_din,GPIO.IN)
		GPIO.setup(spi_cs,GPIO.OUT)
		GPIO.output(spi_clock, True) # default high
		GPIO.output(spi_dout, True) # default high
		GPIO.output(spi_cs, False) # default low (enabled)

		#self.spi = spidev.SpiDev()
		#self.spi.open(0, 0)
		#self.spi.no_cs = True
		#self.spi.threewire = False
		#self.spi.bits_per_word = 8
		#self.spi.mode = 0b00
		#self.spi.max_speed_hz = 100000

		# use enable pin as PWM, to allow set brightness
		# remember enable pin is active low
		GPIO.setup(enable,GPIO.OUT)
		self.enabled = GPIO.PWM(enable, 100000) # set 100KHz as frecuency
		self.enabled.start(50) # default is 50% duty cycle

		#GPIOs
		GPIO.setup(latch,GPIO.OUT)
		GPIO.setup(addr0,GPIO.OUT)
		GPIO.setup(addr1,GPIO.OUT)
		GPIO.setup(addr2,GPIO.OUT)
		GPIO.setup(addr3,GPIO.OUT)
		GPIO.output(latch, True) # turn off (negative logic)
		GPIO.output(addr0, False) # default 0
		GPIO.output(addr1, False) # default 0
		GPIO.output(addr2, False) # default 0
		GPIO.output(addr3, False) # default 0

		# GPS module ( RX-TX)

	def __init__(self,width=64, height=16, rotate=0, mode="1"):
		super(hub08, self).__init__(const=None,serial_interface=noop)
		self.capabilities(width, height, rotate,mode="1")
		self.image = None
		self.size=(width,height)
		self.refresh_period=0.0005 # 0.5 msecs between consecutive rows refresh
		self.initialize()
		def shutdown_hook():  # pragma: no cover
			try:
				GPIO.cleanup()
				self.cleanup()
			except:
				pass
		atexit.register(shutdown_hook)

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
		# create a bitmap from provided pixelmap, to be sent to display by mean of refresh trhead
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
		for row in range(16): # 16 rows
			# send 64 bits to shift-register
			for column in range(64): # 64 columns
				byte = int(column/8) # 8 bits per byte
				mask  = 0x01 << int(column&0x07)
				pixel= pixels[64*row + column]
				# 0 is black 255:white... but matrix reverse colors
				if pixel == 0:
					cur = self.display_data[row][byte] | mask
				else:
					cur = self.display_data[row][byte] & ~mask
				self.display_data[row][byte] = cur

	def show(self):
		"""
		Sets the display mode ON, waking the device out of a prior
		low-power sleep mode.
		"""
		self.enabled.ChangeDutyCycle(self.brightness)
		self.state=True

	def hide(self):
		"""
		Switches the display mode OFF, putting the device in low-power
		sleep mode.
		led matrix "enable" pin has negative logic so set pwm to 100% makes display turn off
		"""
		self.enabled.ChangeDutyCycle(100)
		self.state=False

	def contrast(self, level):
		"""
		Switches the display contrast to the desired level, in the range
		0-255. Note that setting the level to a low (or zero) value will
		not necessarily dim the display to nearly off. In other words,
		this method is **NOT** suitable for fade-in/out animation.

		hub08 display has no way to set up bright. The only way to handle this
		is by mean of using enable pin as PWM, and changeing duty cycle

		:param level: Desired contrast level in the range of 0-255.
		:type level: int
		"""
		assert(0 <= level <= 255)
		lvl = brightness_table[255-level] # human vision is nonlinear: use lookup table
		self.brightness = int( (lvl*100)/255)

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
			self.period=-1
		self._serial_interface.cleanup()
