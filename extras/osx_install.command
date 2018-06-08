#!/bin/sh
BASE=`dirname $0`
HTCONF=/Applications/XAMPP/etc
HTDOCS=/Applications/XAMPP/htdocs
BASEDIR=$HTDOCS/AgilityContest
CONFDIR_37=$HTDOCS/AgilityContest/server/auth
CONFDIR=$HTDOCS/AgilityContest/config
EXTRAS=$HTDOCS/AgilityContest/extras

# request root permissions before continue install
if [ "$USER" != "root" ]; then
    echo "Password required to continue as user root"
    sudo $0
    exit 1
fi

if [ ! -d /Applications/XAMPP ]; then
    echo "XAMPP for Mac-OSX is not installed"
    echo "Download and install it from https://www.apachefriends.org/xampp-files/5.6.28/xampp-osx-5.6.28-1-installer.dmg"
    exit 1
fi

echo "Prepare ..."
# download from github or (prefered) use zip from .dmg diskfile
# curl https://codeload.github.com/jonsito/AgilityContest/zip/master -o /tmp/AgilityContest-master.zip
cp $BASE/AgilityContest-master.zip /tmp/AgilityContest-master.zip
rm -f /tmp/registration.info
rm -f /tmp/config.ini
echo ""

echo "Unziping into $BASEDIR ... "

# hack to preserve files data from versions <= 3.7
mkdir -p $CONFDIR
[ -f $CONFDIR_37/registration.info -a ! -f $CONFDIR/registration.info ] && \
    cp -f $CONFDIR_37/registration.info $CONFDIR/registration.info
[ -f $CONFDIR_37/config.ini -a ! -f $CONFDIR/config.ini ] && \
    cp -f $CONFDIR_37/config.ini $CONFDIR/config.ini
[ -f $CONFDIR_37/system.ini -a ! -f $CONFDIR/system.ini ] && \
    cp -f $CONFDIR_37/system.ini $CONFDIR/system.ini

# make a backup copy of configuration and license
[ -f $CONFDIR/registration.info ] && cp $CONFDIR/registration.info /tmp
[ -f $CONFDIR/config.ini ] && cp $CONFDIR/config.ini /tmp
[ -f $CONFDIR/system.ini ] && cp $CONFDIR/system.ini /tmp

# create a backup of old application
rm -rf $BASEDIR.old
[ -d $BASEDIR ] && mv $BASEDIR $BASEDIR.old

# and unzip files
cd $HTDOCS;
unzip -q /tmp/AgilityContest-master.zip
mv ${HTDOCS}/AgilityContest-master ${HTDOCS}/AgilityContest
echo ""

echo "Configuring MySQL... "
sed -i -e '/.*lower_case_table_names.*/d' /Applications/XAMPP/xamppfiles/etc/my.cnf
sed -i -e '/\[mysqld\]/a lower_case_table_names = 1' /Applications/XAMPP/xamppfiles/etc/my.cnf
echo ""

# add and edit AgilityContest_apache2.conf
echo "Adding AgilityContest apache configuration file... "
cp $EXTRAS/AgilityContest_apache2.conf ${HTCONF}/extra
sed -i -e "s|__HTTP_BASEDIR__|${HTDOCS}|g" \
    -e "s|__AC_BASENAME__|AgilityContest|g" \
    -e "s|__AC_WEBNAME__|agility|g" \
    ${HTCONF}/extra/AgilityContest_apache2.conf

# edit ${HTDOCS}/AgilityContest/.htaccess
sed -i -e "s:|__HTTP_BASEDIR__|${HTDOCS}|g" \
    -e "s|__AC_BASENAME__|AgilityContest|g" \
    -e "s|__AC_WEBNAME__|agility|g" \
    ${HTDOCS}/AgilityContest/.htaccess

# Tell httpd.conf to include AgilityContest config file
sed -i -e '/.*AgilityContest.*/d' ${HTCONF}/httpd.conf
echo 'Include "/Applications/XAMPP/etc/extra/AgilityContest_apache2.conf"' >> ${HTCONF}/httpd.conf
echo ""

# create and install certificates
if [ -f ${BASE}/create_certificate.sh ]; then
    echo -n "Create and Install X509 SSL Certificate S/[n]?"
    read a
    case $a in
        [SsYy]* )
            # create certificate and key
            bash ${BASE}/create_certificate.sh /tmp/ssl
            # backup and copy certificate
            mv -f ${HTCONF}/ssl.crt/server.crt ${HTCONF}/ssl.crt/server.crt.orig
            cp /tmp/ssl/server.crt ${HTCONF}/ssl.crt/server.crt
            # backup and copy key file
            mv -f ${HTCONF}/ssl.key/server.key ${HTCONF}/ssl.key/server.key.orig
            cp /tmp/ssl/server.key ${HTCONF}/ssl.key/server.key
            # cleanup
            rm -rf /tmp/ssl
        ;;
    esac
else
    echo "Certificate creation script not found. Using default certificates"
fi

echo "Fixing permissions... "
cd $HTDOCS
chown -R daemon AgilityContest
chgrp -R daemon AgilityContest
xattr -dr com.apple.quarantine AgilityContest
echo ""

echo "Starting MySQL database server"
/Applications/XAMPP/bin/mysql.server restart
echo ""

echo "Creating and populating AgilityContest database"
cat <<_EOF | /Applications/XAMPP/bin/mysql -u root
DROP DATABASE IF EXISTS agility;
CREATE DATABASE agility;
USE agility;
SOURCE /Applications/XAMPP/htdocs/AgilityContest/extras/agility.sql;
SOURCE /Applications/XAMPP/htdocs/AgilityContest/extras/users.sql;
_EOF
echo ""

echo "Restoreing license and configuration data"
[ -f /tmp/registration.info ] && cp -f /tmp/registration.info $CONFIG/registration.info
[ -f /tmp/config.ini ] && cp -f /tmp/config.ini $CONFIG/config.ini
[ -f /tmp/system.ini ] && cp -f /tmp/system.ini $CONFIG/system.ini
echo ""

echo "Instalation completed"
echo "Start AgilityContest Y/[n]?"
read a
case $a in
    [SsYy]* )
        /Applications/XAMPP/xamppfiles/xampp restart
        open -a Safari https://localhost/agility/console
    ;;
esac
