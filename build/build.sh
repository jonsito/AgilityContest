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
BUILD_DIR=/home/jantonio/work/agility/build
EXTRA_DIR=/home/jantonio/work/agility/extra-pkgs
CONF_DIR=${BASE_DIR}/extras
NSIS=${BASE_DIR}/build/AgilityContest.nsi
XAMPP=xampp-portable-win32-5.6.12-0-VC11.zip
DROPBOX=${HOME}/Dropbox/Public/AgilityContest

# make sure that build dir exists and is clean
mkdir -p ${BUILD_DIR}
rm -rf ${BUILD_DIR}/*

#retrieve xampp from server if not exists
if [ ! -f ${EXTRA_DIR}/${XAMPP} ]; then
    echo "Download xampp from server ..."
    (cd ${EXTRA_DIR}; wget http://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.6.12/xampp-portable-win32-5.6.12-0-VC11.zip )
    if [ $? -ne 0 ]; then
        echo "Cannot download xampp. Aborting"
        exit 1
    fi
fi

# unzip xampp to build directory
echo "Extracting xampp ... "
( cd ${BUILD_DIR}; unzip ${EXTRA_DIR}/${XAMPP} )

# personalize xampp files
# notice that relocation will be done at nsi install time with "setup_xampp.bat"
echo "Setting up apache.conf ..."
cp ${BUILD_DIR}/xampp/apache/conf/httpd.conf ${BUILD_DIR}/xampp/apache/conf/httpd.conf.orig
cat <<__EOF >>${BUILD_DIR}/xampp/apache/conf/httpd.conf
<IfModule mpm_winnt_module>
    ThreadStackSize 8388608
</IfModule>
Include "conf/extra/AgilityContest_xampp.conf"
__EOF
unix2dos ${BUILD_DIR}/xampp/apache/conf/httpd.conf

# add AC config file and remove "/" to use relative paths
echo "Adding AgilityContest config file ..."
cp ${CONF_DIR}/AgilityContest_xampp.conf ${BUILD_DIR}/xampp/apache/conf/extra
sed -i "s:/AgilityContest-master:/AgilityContest:g" ${BUILD_DIR}/xampp/apache/conf/extra/AgilityContest_xampp.conf

# enable OpenSSL support into php
echo "Setting up php/php.ini ..."
cp ${BUILD_DIR}/xampp/php/php.ini  ${BUILD_DIR}/xampp/php/php.ini.orig
sed -i "s/;extension=php_openssl.dll/extension=php_openssl.dll/g" ${BUILD_DIR}/xampp/php/php.ini

# fix options for mysql
echo "Setting up mysql/my.ini ..."
cp ${BUILD_DIR}/xampp/mysql/my-default.ini  ${BUILD_DIR}/xampp/mysql/my.ini
# default my.ini is empty and only has section [mysqld]. So it's safe to append at eof
cat <<__EOF >>${BUILD_DIR}/xampp/mysql/my.ini
lower_case_table_names = 1
key_buffer_size = 16M
explicit_defaults_for_timestamp = 1
__EOF
unix2dos ${BUILD_DIR}/xampp/mysql/my.ini

# ok. time to add AgilityContest files
echo "Copying AgilityContest files ..."
(cd ${BASE_DIR}; tar cfBp - agility extras logs AgilityContest.bat settings_*.bat COPYING README.md ) |\
    ( cd ${BUILD_DIR}; tar xfBp - )
touch ${BUILD_DIR}/logs/first_install
unix2dos ${BUILD_DIR}/AgilityContest.bat
unix2dos ${BUILD_DIR}/settings_es.bat
unix2dos ${BUILD_DIR}/settings_en.bat

# create directory for docs (some day...)
mkdir -p ${BUILD_DIR}/docs
if [ -d ${DROPBOX} ]; then
    echo "Adding a bit of documentation ..."
    cp ${DROPBOX}/{AgilityContest_despliegue.pdf,ReferenciasPegatinas.txt,AgilityContest-1000x800.png,Tarifas_2015.pdf,AgilityContest_doc.zip} ${BUILD_DIR}/docs
    cp ${BASE_DIR}/README* ${BUILD_DIR}/docs
    (cd ${BUILD_DIR}/docs; unzip AgilityContest_doc.zip)
fi

# and finally invoke makensis
echo "Prepare and execute makensis..."
VERSION=`grep version_name ${BUILD_DIR}/agility/server/auth/system.ini | sed -e 's/^.*= "/"/g'`
DATE=`grep version_date ${BUILD_DIR}/agility/server/auth/system.ini | sed -e 's/^.*= "/"/g'`
sed -e "s/__VERSION__/${VERSION}/g" -e "s/__TIMESTAMP__/${DATE}/g" ${NSIS} > ${BUILD_DIR}/AgilityContest.nsi
cp ${BASE_DIR}/build/{installer.bmp,License.txt,wellcome.bmp} ${BUILD_DIR}
(cd ${BUILD_DIR}; makensis AgilityContest.nsi )

echo "That's all folks!"
