#!/usr/bin/env bash
# script to install and configure AgilityContest NowRunning tool
INSTALL_DIR=/home/pi/AgilityContest/nowRunning

if [ "$USER" != "root" ]; then
    echo "This script must be run as user root"
    exit 1
fi

# Copy files to destination directory
if [ "$CWD" != "$INSTALL_DIR" ]; then
    mkdir -p ${INSTALL_DIR}
    cp *py NowRunning.sh ${INSTALL_DIR}
fi
chmod +r ${INSTALL_DIR}/*.py
chmod +x ${INSTALL_DIR}/NowRunning.sh

# install pip3 required modules
pip3 install luma.emulator luma.led_matrix
pip3 install py-getch netifaces

#
# configure Raspberry
#

# enable ssh
raspi-config nonint do_ssh 0
# Cannot allow auto login nor graphics mode, cause
# connection with NowRunning Virtual Terminal will be lost
/usr/bin/raspi-config nonint do_boot_behaviour B1
# enable SPI
raspi-config nonint do_spi 0
# enable I2C
raspi-config nonint do_i2c 0

# install systemd service
cp enpista.service /lib/systemd/system/enpista.service
systemctl daemon-reload
systemctl enable enpista

#
# Ask user to start app
while true; do
    read -p "Start AgilityContest NowRunning App?" yn
    case $yn in
        [Yy]* ) systemctl start enpista; break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
done