#!/bin/bash
# build.sh
# script to build a win32 AgilityContest installable distribution
#
# Copyright 2015 by Juan Antonio Martínez <juansgaviota@gmail.com>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

BASE_DIR=/home/jantonio/work/agility/phpstorm/AgilityContest
EXE_DIR=/home/jantonio/work/agility/phpstorm/AgilityContest/build/launcher
BUILD_DIR=/home/jantonio/work/agility/build
EXTRA_DIR=/home/jantonio/work/agility/extra-pkgs
CONF_DIR=${BASE_DIR}/extras
NSIS=${BASE_DIR}/build/AgilityContest.nsi
XAMPP=xampp-portable-win32-7.2.6-0-VC15.zip
DROPBOX=${HOME}/Dropbox/Public/AgilityContest

# make sure that build dir exists and is clean
mkdir -p ${BUILD_DIR}
rm -rf ${BUILD_DIR}/*

#retrieve xampp from server if not exists
if [ ! -f ${EXTRA_DIR}/${XAMPP} ]; then
    echo "Download xampp from server ..."
    (cd ${EXTRA_DIR}; wget --no-check-certificate http://sourceforge.net/projects/xampp/files/XAMPP%20Windows/7.2.6/${XAMPP} )
    if [ $? -ne 0 ]; then
        echo "Cannot download xampp. Aborting"
        exit 1
    fi
fi

# compile AgilityContest.exe
echo "Compiling launcher..."
( cd ${EXE_DIR}; make clean; make install; make clean )

# unzip xampp to build directory
echo "Extracting xampp ... "
( cd ${BUILD_DIR}; unzip -q ${EXTRA_DIR}/${XAMPP} )

# personalize xampp files
# notice that relocation will be done at nsi install time with "setup_xampp.bat"
echo "Setting up apache.conf ..."
cp ${BUILD_DIR}/xampp/apache/conf/httpd.conf ${BUILD_DIR}/xampp/apache/conf/httpd.conf.orig
cat <<__EOF >>${BUILD_DIR}/xampp/apache/conf/httpd.conf
<IfModule mpm_winnt_module>
    ThreadStackSize 8388608
</IfModule>
Include "conf/extra/AgilityContest_apache2.conf"
__EOF
unix2dos ${BUILD_DIR}/xampp/apache/conf/httpd.conf

# create certificates
echo "Creating new Certificate ..."
/bin/bash ${CONF_DIR}/create_certificate.command /tmp/ssl_crt >/dev/null 2>&1
cp /tmp/ssl_crt/server.csr ${BUILD_DIR}/xampp/apache/conf/ssl.csr/server.csr
cp /tmp/ssl_crt/server.crt ${BUILD_DIR}/xampp/apache/conf/ssl.crt/server.crt
cp /tmp/ssl_crt/server.key ${BUILD_DIR}/xampp/apache/conf/ssl.key/server.key
rm -rf /tmp/ssl_crt

# add AC config file and remove "/" to use relative paths
echo "Adding AgilityContest config file ..."
cp ${CONF_DIR}/AgilityContest_apache2.conf ${BUILD_DIR}/xampp/apache/conf/extra
sed -i -e "s|__HTTP_BASEDIR__|C:|g" \
    -e "s|__AC_BASENAME__|AgilityContest|g" \
    -e "s|__AC_WEBNAME__|agility|g" \
    ${BUILD_DIR}/xampp/apache/conf/extra/AgilityContest_apache2.conf

# enable OpenSSL and Locale support into php
echo "Setting up php/php.ini ..."
cp ${BUILD_DIR}/xampp/php/php.ini  ${BUILD_DIR}/xampp/php/php.ini.orig
sed -i "s/;extension=php_openssl.dll/extension=php_openssl.dll/g" ${BUILD_DIR}/xampp/php/php.ini
sed -i "s/;extension=php_intl.dll/extension=php_intl.dll/g" ${BUILD_DIR}/xampp/php/php.ini

# fix options for mysql
# notice that in 5.6.20 cannot simply add options at the end, so must provide our own
# personalized copy of my.ini
echo "Setting up mysql/my.ini ..."
cp ${BASE_DIR}/build/ac_my.ini  ${BUILD_DIR}/xampp/mysql/my.ini
unix2dos ${BUILD_DIR}/xampp/mysql/my.ini

# ok. time to add AgilityContest files
echo "Copying AgilityContest files ..."
(cd ${BASE_DIR}; tar cfBp - .htaccess index.html agility applications extras logs config AgilityContest.exe COPYING README.md Contributors) |\
    ( cd ${BUILD_DIR}; tar xfBp - )
# set first install mark and properly edit .htaccess
touch ${BUILD_DIR}/logs/first_install
sed -i -e "s|__HTTP_BASEDIR__|C:/|g" \
    -e "s|__AC_BASENAME__|AgilityContest|g" \
    -e "s|__AC_WEBNAME__|agility|g" \
    ${BUILD_DIR}/.htaccess

# create directory for docs (some day...)
mkdir -p ${BUILD_DIR}/docs
if [ -d ${DROPBOX} ]; then
    echo "Adding a bit of documentation ..."
    for i in ac_despliegue.pdf ReferenciasPegatinas.txt AgilityContest-1000x800.png Tarifas_2017.pdf ac_obs_livestreaming.pdf; do
    cp ${DROPBOX}/${i} ${BUILD_DIR}/docs
    done
    cp ${BASE_DIR}/README* ${BUILD_DIR}/docs
fi

# invoke makensis
echo "Prepare and execute makensis..."
VERSION=`grep version_name ${BUILD_DIR}/config/system.ini | sed -e 's/^.*= "/"/g'`
DATE=`grep version_date ${BUILD_DIR}/config/system.ini | sed -e 's/^.*= "/"/g'`
sed -e "s/__VERSION__/${VERSION}/g" -e "s/__TIMESTAMP__/${DATE}/g" ${NSIS} > ${BUILD_DIR}/AgilityContest.nsi
cp ${BASE_DIR}/build/{installer.bmp,License.txt,wellcome.bmp} ${BUILD_DIR}
(cd ${BUILD_DIR}; makensis AgilityContest.nsi )

# prepare dmg image for MAC-OSX
echo "Creating disk image for Mac-OSX"
#strip quotes in version
VERSION=`echo ${VERSION} |sed -e 's/"//g'`
DATE=`echo ${DATE} |sed -e 's/"//g'`
mkdir -p ${BUILD_DIR}/AgilityContest-master
cd ${BUILD_DIR}
# add build installer and certificate script
cp extras/{osx_install.command,create_certificate.command} .
chmod +x *.command
# add .dmg background image
mkdir -p .background
cp agility/images/AgilityContest.png .background
cp -r COPYING License.txt agility config logs applications extras docs AgilityContest-master
# restore original .htaccess
cp ${BASE_DIR}/.htaccess AgilityContest-master
# do not include build and web dir in destination zipfile
zip -q -r AgilityContest-master.zip AgilityContest-master/{agility,applications,extras,logs,config,COPYING,index.html,.htaccess}
FILES="osx_install.command create_certificate.command COPYING License.txt AgilityContest-master.zip"
mkisofs -quiet -A AgilityContest \
    -P jonsito@gmail.com \
    -V ${VERSION}_${DATE} \
    -J -r -o AgilityContest-${VERSION}-${DATE}.dmg \
    -graft-points /.background/=.background \
    ${FILES}

# prepare zip file
mv AgilityContest-master.zip AgilityContest-${VERSION}-${DATE}.zip

# create md5 sum file and html page

rm -f AgilityContest-${VERSION}-${DATE}.md5sums
zsum=`md5sum AgilityContest-${VERSION}-${DATE}.zip`
esum=`md5sum AgilityContest-${VERSION}-${DATE}.exe`
dsum=`md5sum AgilityContest-${VERSION}-${DATE}.dmg`
echo ${zsum} >> AgilityContest-${VERSION}-${DATE}.md5sums
echo ${esum} >> AgilityContest-${VERSION}-${DATE}.md5sums
echo ${dsum} >> AgilityContest-${VERSION}-${DATE}.md5sums
cp ${BASE_DIR}/applications/Eval_md5sum.html AgilityContest-${VERSION}-${DATE}_md5check.html
sed -i "s/__VERSION__/${VERSION}-${DATE}/g" AgilityContest-${VERSION}-${DATE}_md5check.html
sed -i "s/__ZIPFILE__/${zsum}/g" AgilityContest-${VERSION}-${DATE}_md5check.html
sed -i "s/__WINFILE__/${esum}/g" AgilityContest-${VERSION}-${DATE}_md5check.html
sed -i "s/__MACFILE__/${dsum}/g" AgilityContest-${VERSION}-${DATE}_md5check.html

# move generated files to dropbox
#mv AgilityContest-${VERSION}-${DATE}.* ${DROPBOX}

# cleanups
rm -rf AgilityContest AgilityContest.zip *.command .background
echo "That's all folks!"
