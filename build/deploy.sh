#!/bin/bash
#
# Script to deploy from AgilityContest git tree to install dir
# clone from http://github.com/jonsito/AgilityContest.git
BASEDIR=`dirname $0`/..
INSTDIR=${1:-/var/www/html/AgilityContest}
WEBNAME=${2:-agility}
WEBDIR=`dirname ${INSTDIR}`
BASENAME=`basename ${INSTDIR}`
SYSTEMINI="config/system.ini"
HTACCESS=${INSTDIR}/.htaccess
HTTPD_CONF="AgilityContest_${WEBNAME}.conf"

case `grep -e '^ID=' /etc/os-release` in
    'ID=ubuntu' )
        # for UBUNTU
        OWNER=root
        GROUP=www-data
        CONF=/etc/apache2/conf-available/${HTTPD_CONF}
        ;;
    'ID=fedora' )
        # for Fedora/RedHat (sudo to proper user before running)
        OWNER=jantonio
        GROUP=apache
        CONF=/etc/httpd/conf.d/${HTTPD_CONF}
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

# check for existing httpd.conf
if [ -f ${CONF} ]; then
    read -p  "${WEBNAME} is an already defined httpd.conf alias. Overwrite? Y/[n]: " a
    case "$a" in
    [YySs]* )
        mkdir -p ${INSTDIR}
        ;;
    * )
        echo "AgilityContest installation aborted: ${CONF} already exists"
        exit 3
        ;;
    esac
    exit 2
fi

# check for destination directory
if [ ! -d ${INSTDIR} ]; then
    read -p "Directory ${INSTDIR} does not exist. Create? Y/[n]: " a
    case "$a" in
    [YySs]* )
        mkdir -p ${INSTDIR}
        ;;
    * )
        echo "AgilityContest installation aborted: user requested do not create ${INSTDIR}"
        exit 4
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
( cd ${BASEDIR}; tar cfBp - * .htaccess ) | ( cd ${INSTDIR}; tar xfBp - )
echo "Done."

# personalize apache related files ( httpd.conf , .htaccess )
echo -n "Personalize apache files... "
sed -i -e "s:__HTTP_BASEDIR__:${WEBDIR}:g" \
    -e "s:__AC_BASENAME__:${BASENAME}:g" \
    -e "s:__AC_WEBNAME__:${WEBNAME}:g" \
    ${HTACCESS}
case `grep -e '^ID=' /etc/os-release` in
    'ID=ubuntu' )
        cp -f ${BASEDIR}/extras/AgilityContest_apache2.conf ${CONF}
        sed -i -e "s:__HTTP_BASEDIR__:${WEBDIR}:g" \
            -e "s:__AC_BASENAME__:${BASENAME}:g" \
            -e "s:__AC_WEBNAME__:${WEBNAME}:g" \
            ${CONF}
        a2enconf AgilityContest_apache2
        service apache2 reload
        ;;
    'ID=fedora' )
        cp -f ${BASEDIR}/extras/AgilityContest_apache2.conf ${CONF}
        sed -i -e "s:__HTTP_BASEDIR__:${WEBDIR}:g" \
            -e "s:__AC_BASENAME__:${BASENAME}:g" \
            -e "s:__AC_WEBNAME__:${WEBNAME}:g" \
            ${CONF}
        systemctl restart httpd
        ;;
esac
echo "Done"

# directories to preserve ( copy from backup )
echo "Preserve directories..."
[ -d ${INSTDIR}.old/agility/images/supporters ] && \
    ( cd ${INSTDIR}.old/agility/images/supporters; tar cfBp - * ) | ( cd ${INSTDIR}/agility/images/supporters; tar xfBp - )
echo "Done."

# files to preserve (backup and restore)
echo -n "Restore config..."
mkdir -p ${INSTDIR}/logs/updateRequests
mkdir -p ${INSTDIR}.old/config
mkdir -p ${INSTDIR}/config
[ -f ${INSTDIR}.old/agility/images/supporters/supporters.csv ] && \
    cp ${INSTDIR}.old/agility/images/supporters/supporters.csv ${INSTDIR}/agility/images/supporters

# backward compatibility with pre-3.8 versions
[ -f ${INSTDIR}.old/agility/server/auth/config.ini -a ! -f ${INSTDIR}.old/config/config.ini ] && \
    cp ${INSTDIR}.old/agility/server/auth/config.ini ${INSTDIR}/config
[ -f ${INSTDIR}.old/agility/server/auth/registration.info -a ! -f ${INSTDIR}.old/config/registration.info ] && \
    cp ${INSTDIR}.old/agility/server/auth/registration.info ${INSTDIR}/config

# new location for configuration files.
[ -f ${INSTDIR}.old/config/config.ini ] && \
    cp ${INSTDIR}.old/config/config.ini ${INSTDIR}/config
[ -f ${INSTDIR}.old/config/registration.info ] && \
    cp ${INSTDIR}.old/config/registration.info ${INSTDIR}/config

# pending update requests
[ -d ${INSTDIR}.old/logs/updateRequests ] && \
    mv ${INSTDIR}.old/logs/updateRequests ${INSTDIR}/logs
chmod -R g+w ${INSTDIR}/logs/updateRequests

# patch to handle 3.7 to 3.8 config files location
[ -f ${INSTDIR}.old/agility/server/auth/system.ini -a ! -f ${INSTDIR}.old/${SYSTEMINI} ] && \
    cp -f ${INSTDIR}.old/agility/server/auth/system.ini ${INSTDIR}.old/${SYSTEMINI}

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
sudo chmod g+s ${INSTDIR}/logs ${INSTDIR}/agility/images/logos ${INSTDIR}/config

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
