#!/bin/bash
#
# Script to deploy from AgilityContest git tree to install dir
# clone from http://github.com/jonsito/AgilityContest.git
BASEDIR=`dirname $0`/..
INSTDIR=${1:=/var/www/html/AgilityContest}

#for UBUNTU
#OWNER=root
#GROUP=www-data
OWNER=${USER}
GROUP=apache

# some checks
echo -n "Check..."
[ -d ${INSTDIR} ] || ( echo "${INSTDIR} does not exist. Exiting." ; exit 1 )
[ "${USER}" = "${OWNER}" ] || ( echo "Must be executed as ${OWNER}. Exiting"; exit 1 )
[ -d ${BASEDIR}/.git -a -d ${BASEDIR}/agility ] || ( echo "${BASEDIR} is not an AgilityContest git directory. Exiting"; exit 1 )
echo "Done."

# rotate last install
echo -n "Backup..."
rm -rf ${INSTDIR}.old
mv ${INSTDIR} ${INSTDIR}.old
mkdir ${INSTDIR}
echo "Done."

# copy files
echo -n "Copying..."
( cd ${BASEDIR}; tar cfBp - * ) | ( cd ${INSTDIR}; tar xfBp - )
echo "Done."

# directories to preserve ( copy from backup )
echo "Preserve directories..."
[ -d ${INSTDIR}.old/agility/images/supporters ] && \
    ( cd ${INSTDIR}.old/agility/images/supporters; tar cfBp - * ) | ( cd ${INSTDIR}/agility/images/supporters; tar xfBp - )
echo "Done."

# files to preserve (backup and restore)
echo -n "Restore config..."
[ -f ${INSTDIR}.old/agility/images/supporters/supporters.csv ] && \
    cp ${INSTDIR}.old/agility/images/supporters/supporters.csv ${INSTDIR}/agility/images/supporters
[ -f ${INSTDIR}.old/agility/server/auth/config.ini ] && \
    cp ${INSTDIR}.old/agility/server/auth/config.ini ${INSTDIR}/agility/server/auth
[ -f ${INSTDIR}.old/agility/server/auth/registration.info ] && \
    cp ${INSTDIR}.old/agility/server/auth/registration.info ${INSTDIR}/agility/server/auth
# restore restricted mode from system.ini
sed -i '/restricted/d' ${INSTDIR}/agility/server/auth/system.ini
grep 'restricted' ${INSTDIR}.old/agility/server/auth/system.ini >> ${INSTDIR}/agility/server/auth/system.ini
echo "Done."

# fix permissions
echo "Setting perms..."
find ${INSTDIR} -type d -exec chmod 775 {} \;
find ${INSTDIR} -type f -exec chmod 664 {} \;
chown -R ${OWNER}.${GROUP} ${INSTDIR}
chmod g+s ${INSTDIR}/logs ${INSTDIR}/agility/images/logos ${INSTDIR}/agility/server/auth
echo "Done."

echo "That's all folks"
exit 0
