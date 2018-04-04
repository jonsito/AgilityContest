#!/bin/bash
#
# Script to deploy from AgilityContest git tree to install dir
# clone from http://github.com/jonsito/AgilityContest.git
BASEDIR=`dirname $0`/..
INSTDIR=${1:-/var/www/html/AgilityContest}
WEBDIR=${INSTDIR}/..
SYSTEMINI="agility/server/auth/system.ini"

case `grep -e '^ID=' /etc/os-release` in
    'ID=ubuntu' )
        # for UBUNTU
        OWNER=root
        GROUP=www-data
        ;;
    'ID=fedora' )
        # for Fedora/RedHat (sudo to proper user before running)
        OWNER=jantonio
        GROUP=apache
        ;;
esac

# some checks
echo -n "Checking ..."
# check for installer to have needed permissions
if [ "${USER}" != "${OWNER}" ]; then
    echo "Must be executed as ${OWNER}. Exiting"
    exit 1
fi
# make sure that directory to clone from is valid
if [ ! -d ${BASEDIR}/.git -o ! -d ${BASEDIR}/agility ]; then
    echo "${BASEDIR} is not an AgilityContest git directory. Exiting";
    exit 2
fi
# check for destination directory
if [ ! -d ${INSTDIR} ]; then
    echo -n "Directory ${INSTDIR} does not exist. Create? Y/[n]: "
    read a
    case "$a" in
    [YySs]* )
        mkdir -p ${INSTDIR}
        ;;
    * )
        echo "AgilityContest installation aborted"
        exit 3
        ;;
    esac
fi
echo "Done."

# rotate last install
echo -n "Backup..."
rm -rf ${INSTDIR}.old
[ -d ${INSTDIR} ] && mv ${INSTDIR} ${INSTDIR}.old
mkdir ${INSTDIR}
echo "Done."

# copy files
echo -n "Copying files..."
( cd ${BASEDIR}; tar cfBp - * ) | ( cd ${INSTDIR}; tar xfBp - )
echo "Done."

# directories to preserve ( copy from backup )
echo "Preserve directories..."
[ -d ${INSTDIR}.old/agility/images/supporters ] && \
    ( cd ${INSTDIR}.old/agility/images/supporters; tar cfBp - * ) | ( cd ${INSTDIR}/agility/images/supporters; tar xfBp - )
echo "Done."

# files to preserve (backup and restore)
echo -n "Restore config..."
mkdir -p ${INSTDIR}/logs/updateRequests
[ -f ${INSTDIR}.old/agility/images/supporters/supporters.csv ] && \
    cp ${INSTDIR}.old/agility/images/supporters/supporters.csv ${INSTDIR}/agility/images/supporters
[ -f ${INSTDIR}.old/agility/server/auth/config.ini ] && \
    cp ${INSTDIR}.old/agility/server/auth/config.ini ${INSTDIR}/agility/server/auth
[ -f ${INSTDIR}.old/agility/server/auth/registration.info ] && \
    cp ${INSTDIR}.old/agility/server/auth/registration.info ${INSTDIR}/agility/server/auth
[ -d ${INSTDIR}.old/logs/updateRequests ] && \
    mv ${INSTDIR}.old/logs/updateRequests ${INSTDIR}/logs
chmod -R g+w ${INSTDIR}/logs/updateRequests

# restore system.ini and update version and revision info
if [ -f ${INSTDIR}.old/${SYSTEMINI} ]; then
    #copia el viejo system.ini, manteniendo el backup en ${instdir}.old
    cp ${INSTDIR}.old/${SYSTEMINI} ${INSTDIR}/${SYSTEMINI}.old
    # reemplaza nueva version_name en el viejo system.ini
	sed -i '/version_name/d' ${INSTDIR}/${SYSTEMINI}.old
	grep 'version_name' ${INSTDIR}/${SYSTEMINI} >> ${INSTDIR}/${SYSTEMINI}.old
	# reemplaza nueva version_date en el viejo system.ini
	sed -i '/version_date/d' ${INSTDIR}/${SYSTEMINI}.old
	grep 'version_date' ${INSTDIR}/${SYSTEMINI} >> ${INSTDIR}/${SYSTEMINI}.old
	# convierte el viejo system.ini en el nuevo
	mv ${INSTDIR}/${SYSTEMINI}.old ${INSTDIR}/${SYSTEMINI}
fi
echo "Done."

# fix permissions
echo "Setting perms..."
find ${INSTDIR} -type d -exec chmod 775 {} \;
find ${INSTDIR} -type f -exec chmod 664 {} \;
sudo chown -R ${OWNER}:${GROUP} ${INSTDIR}
sudo chmod g+s ${INSTDIR}/logs ${INSTDIR}/agility/images/logos ${INSTDIR}/agility/server/auth

#finally move web contents to their proper location
echo -n "Install web files? Y\[n]: "
read a
case "$a" in
    [YySs]* )
        echo "Installing web files...."
        (cd ${INSTDIR}/web; tar cfBp - *) | (cd ${WEBDIR}; tar xfBp -)
        echo "Done."
    ;;
    * )
        echo "Skip install web files...."
    ;;
esac

echo "That's all folks"
exit 0
