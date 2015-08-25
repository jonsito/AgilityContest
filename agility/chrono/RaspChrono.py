#!/usr/bin/env python
#Imports for Pins,input
http://raspberrypi.stackexchange.com/questions/19668/from-python-script-to-kernel-module
import RPi.GPIO as GPIO
import uinput
from time import sleep

#Setup
key_events=( uinput.KEY_ESC, )
device=uinput.Device(key_events)
GPIO.setmode(GPIO.BCM)
GPIO.setup(17, GPIO.IN, pull_up_down=GPIO.PUD_UP)

#MAIN
while True:
   GPIO.wait_for_edge(17,GPIO.FALLING)
   device.emit(uinput.KEY_ESC,1)
   sleep(2)
   device.emit(uinput.KEY_ESC,0)

####################################################################
#!/usr/bin/python3
#gpiokeys.pi
import time
import RPi.GPIO as GPIO
import uinput

#HARDWARE SETUP
# P1
# 2 [==G=====<=V==]26
# 1 [===2=2>^=====]25
B_DOWN  = 22 #V
B_LEFT  = 18 #<
B_UP    = 15 #^
B_RIGHT = 13 #>
B_1     = 11 #1
B_2     = 7  #2

DEBUG=True
BTN = [B_UP,B_DOWN,B_LEFT,B_RIGHT,B_1,B_2]
MSG = ["UP","DOWN","LEFT","RIGHT","1","2"]

#Setup the DPad module pins and pull-ups
def dpad_setup():
	# set up the wiring
	GPIO.setmode(GPIO.BOARD)
	# set BTN ports as INPUTS
	for val in BTN:
		# Set up GPIO input with pull up control
		# ( pull_up_down can be: PUD_OFF -def-, PUD_UP or PUD_DOWN )
		GPIO.setup(val, GPIO.IN, pull_up_down=GPIO.PUD_UP)

def main():
	# Setup uinput
	events = ( uinput.KEY_UP,uinput.KEY_DOWN,uinput.KEY_LEFT,uinput.KEY_RIGHT,uinput.KEY_ENTER,uinput.KEY_ENTER )
	device = uinput.Device(events)
	time.sleep(2) # seconds
	dpad_setup()
	print("DPad Ready!")
	btn_state = [False,False,False,False,False,False]
	key_state = [False,False,False,False,False,False]
	while True:
		# Catch all the buttons pressed before pressing related keys
		for idx, val in enumerate(BTN):
			if GPIO.input(val) == False:
				btn_state[idx]=True
			else:
				btn_state[idx]=False

		# Perform the button presses/releases,( but only change state once )
		for idx, val in enumerate(btn_state):
			if val == True and key_state[idx] == False:
				if DEBUG: print (str(val) + ":" + MSG[idx])
				device.emit(events[idx],1) # press
				key_state[idx]=True
			elif val == False and key_state[idx] == True:
				if DEBUG: print (str(val) + ":!" + MSG[idx])
				device.emit(events[idx],0) # release
				key_state[idx]=False

		time.sleep(.1)

try:
	main()
finally:
	GPIO.cleanup()
# End

#################################################################
https://www.raspberrypi.org/forums/viewtopic.php?t=43216&p=346413
    #!/usr/bin/env python


    ### IMPORTS ###
    import os                           # Allows us to run console commands and programs
    from time import sleep                        # Allows us to call the sleep function to slow down our loop
    import datetime                           # Allows us to look at the time
    import RPi.GPIO as GPIO                        # Allows us to call our GPIO pins and names it just GPIO
    GPIO.setmode(GPIO.BCM)                        # Set's GPIO pins to BCM GPIO numbering

    b1count = 1
    b2count = 1
    b3count = 1

    ### GPIO SETUP ###
    GPIO.setup(23, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)            # Set input pin to use the internal pull down resistor
    GPIO.setup(24, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)            # Set input pin to use the internal pull down resistor
    GPIO.setup(25, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)            # Set input pin to use the internal pull down resistor


    ### BUTTON PRESS FUNCTIONS ###
    def Input_1(channel):
        os.system('xte "key q"')                     # Xte converting button Presses into Keystrokes.
        global b1count                        # Imports global variable set above
        now = datetime.datetime.now()                  # What time is it now?
        print ' - Button 1 - %r - %r' % (b1count, now.strftime("%H:%M:%S.%f"))   # Prints which button was pressed, how many times, and timestamps it
        b1count = b1count+1                        # Increments button count


    def Input_2(channel):
        os.system('xte "key h"')                     # Xte converting button Presses into Keystrokes.
        global b2count                        # Imports global variable set above
        now = datetime.datetime.now()                  # What time is it now?
        print ' - Button 2 - %r - %r' % (b2count, now.strftime("%H:%M:%S.%f"))   # Prints which button was pressed, how many times, and timestamps it
        b2count = b2count+1                        # Increments button count

    def Input_3(channel):
        os.system('xte "key v"')                     # Xte converting button Presses into Keystrokes.
        global b3count                        # Imports global variable set above
        now = datetime.datetime.now()                  # What time is it now?
        print ' - Button 3 - %r - %r' % (b3count, now.strftime("%H:%M:%S.%f"))   # Prints which button was pressed, how many times, and timestamps it
        b3count = b3count+1                        # Increments button count


    ### BUTTON PRESS DETECTION ###
    ### Does a Callback to the appropriate Input function.  Also debounces to prevent clicking the button multiple times a second.
    GPIO.add_event_detect(23, GPIO.FALLING, callback=Input_1, bouncetime=1000)   # Waiting for Button 1 to be pressed.
    GPIO.add_event_detect(24, GPIO.FALLING, callback=Input_2, bouncetime=1000)   # Waiting for Button 2 to be pressed.
    GPIO.add_event_detect(25, GPIO.FALLING, callback=Input_3, bouncetime=1000)   # Waiting for Button 3 to be pressed.


    ### FEH -D - Slideshow -Z - Zooms -Y Hides Pointer -F Fullscreen -r loads all images in the following directory###
    os.system('feh -D 0.25 -Z -Y -F -r /home/pi/Desktop/Pics/')         # Runs Feh Image Viewer with some command line arguments.


    ### Starts a neverending loop otherwise the script will just quit.
    while True:
        print "Waiting for input."                     # Insert Random Loop Junk
        sleep(60);                           # Sleeps for a minute to save CPU cycles.  Any interrupt will break this.

    GPIO.cleanup()