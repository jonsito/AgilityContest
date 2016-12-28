#!/bin/sh
HTDOCS=/Applications/XAMPP/htdocs
BASEDIR=$HTDOCS/AgilityContest-master
CONFDIR=$HTDOCS/AgilityContest-master/agility/server/auth
EXTRAS=$HTDOCS/AgilityContest-master/extras

echo "Downloading code..."
curl https://codeload.github.com/jonsito/AgilityContest/zip/master -o /tmp/AgilityContest-master.zip
rm -f /tmp/registration.info
rm -f /tmp/config.ini
echo ""

echo "Unziping into $BASEDIR ... "
# make a backup copy if directory exists
if [ -f $CONFDIR ]; then
    cp $CONFDIR/registration.info /tmp
    cp $CONFDIR/config.ini /tmp
fi
# create a backup of old application
rm -rf $BASEDIR.old && mv $BASEDIR $BASEDIR.old
# and unzip files
cd $HTDOCS;
unzip -q /tmp/AgilityContest-master.zip
echo ""

echo "Adding AgilityContest apache configuration file... "
cp $EXTRAS/AgilityContest_osx.conf /Applications/XAMPP/etc/extra
sed -i -e '/.*AgilityContest.*/d' /Applications/XAMPP/etc/httpd.conf
echo 'Include \"/Applications/XAMPP/etc/extra/AgilityContest_osx.conf\"' >> /Applications/XAMPP/etc/httpd.conf
echo ""

echo "Fixing permissions... "
cd $HTDOCS
chown -R daemon AgilityContest-master
chgrp -R daemon AgilityContest-master
xattr -dr com.apple.quarantine AgilityContest-master
echo ""

echo "Creating and populating AgilityContest database"
cat <<_EOF | /Applications/XAMPP/bin/mysql -u root
DROP DATABASE IF EXISTS agility;
CREATE DATABASE agility;
USE agility;
SOURCE /Applications/XAMPP/htdocs/AgilityContest-master/extras/agility.sql;
SOURCE /Applications/XAMPP/htdocs/AgilityContest-master/extras/users.sql;
_EOF
echo ""

echo "Restoring licencse and configuration"
[ -f /tmp/registration.info ] && cp /tmp/registration.info $CONFIG/registration.info
[ -f /tmp/config.ini ] && cp /tmp/config.ini $CONFIG/config.ini
echo ""

echo "Instalacion completada"