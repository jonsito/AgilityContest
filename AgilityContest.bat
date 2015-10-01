@echo off
call settings.bat
cd /d %~dp0\xampp
echo AgilityContest Launch Script

if not exist ..\logs\first_install GOTO apache_start
echo Configuring first boot of XAMPP
set PHP_BIN=php\php.exe
set CONFIG_PHP=install\install.php
%PHP_BIN% -n -d output_buffering=0 -q %CONFIG_PHP% usb

:apache_start
echo Starting Apache Web Server....
start /B "" apache\bin\httpd.exe
timeout /t 5

:mysql_start
echo MySQL Database is trying to start
echo Please wait  ....
start /B "" mysql\bin\mysqld --defaults-file=mysql\bin\my.ini --standalone --console
timeout /t 5

if not exist ..\logs\first_install GOTO browser_start
echo Creating AgilityContest Databases
echo DROP DATABASE IF EXISTS agility; > ..\logs\install.sql
echo CREATE DATABASE agility; >> ..\logs\install.sql
echo USE agility; >> ..\logs\install.sql
type ..\extras\agility.sql >> ..\logs\install.sql
type ..\extras\users.sql >> ..\logs\install.sql
mysql\bin\mysql -u root < ..\logs\install.sql
del ..\logs\install.sql
del ..\logs\first_install

:browser_start
echo Opening AgilityContest console...
start /W /MAX "AgilityContest" https://localhost/agility/console

echo Please wait for navigator window to show up...

echo -------------------------------------------
echo DO NOT CLOSE THIS WINDOW UNTIL SESSION END
echo -------------------------------------------
set /p key= Press enter to finish AgilityContest session

echo Apache Web Server shutdowm ...
apache\bin\pv -f -k httpd.exe -q
if not exist apache\logs\httpd.pid GOTO stop_mysql
del apache\logs\httpd.pid

:stop_mysql
echo MySQL DataBase shutdowm ...
apache\bin\pv -f -k mysqld.exe -q

if not exist mysql\data\%computername%.pid GOTO finish
echo Delete %computername%.pid ...
del mysql\data\%computername%.pid

:finish
