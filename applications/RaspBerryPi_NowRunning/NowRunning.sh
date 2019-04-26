#!/usr/bin/env bash
# change to virtual terminal 12 to enable local keypad
# vt12 is declared at enpista.service
chvt 12
# force num-lock on
setleds -D +num
# execute app
echo "Starting service..."
cd /home/pi/AgilityContest/NowRunning
/usr/bin/python3 /home/pi/AgilityContest/NowRunning/NRMain.py
# evaluate exit code to decide what to do
retcode=$?
echo "Service exit with status: $retcode"
case $retcode in
    0 ) # stop
        echo "Stopping service... "
        systemctl stop enpista
        ;;
    1 ) # restart
        echo "Service finished. Restarting..."
        exit 0
        ;;
    2 ) # shutdown
        echo "Shutting down Raspberry Pi..."
        /sbin/shutdown -h now
        ;;
esac