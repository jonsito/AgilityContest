@echo off
call settings.bat
cd /d %~dp0\xampp
echo AgilityContest Launch Script

rem notice that this may require admin privileges
rem for windows 8 and 10 disable w3svc service
rem also configure firewall to allow http https and mysql
net stop W3SVC
netsh advfirewall firewall add rule name="MySQL Server" action=allow protocol=TCP dir=in localport=3306
netsh advfirewall firewall add rule name="Apache HTTP Server" action=allow protocol=TCP dir=in localport=80
netsh advfirewall firewall add rule name="Apache HTTPs Server" action=allow protocol=TCP dir=in localport=443

rem if required prepare portable xampp to properly setup directories
if not exist ..\logs\first_install GOTO mysql_start
echo Configuring first boot of XAMPP
set PHP_BIN=php\php.exe
set CONFIG_PHP=install\install.php
%PHP_BIN% -n -d output_buffering=0 -q %CONFIG_PHP% usb

rem start mysql database server
:mysql_start
echo MySQL Database is trying to start
echo Please wait  ....
start /B "" mysql\bin\mysqld --defaults-file=mysql\bin\my.ini --standalone --console
timeout /t 5

rem start apache web server
:apache_start
echo Starting Apache Web Server....
start /B "" apache\bin\httpd.exe
timeout /t 5

rem on first run create database and database users
if not exist ..\logs\first_install GOTO browser_start
echo Creating AgilityContest Databases. Please wait
timeout /t 5
echo DROP DATABASE IF EXISTS agility; > ..\logs\install.sql
echo CREATE DATABASE agility; >> ..\logs\install.sql
echo USE agility; >> ..\logs\install.sql
rem type ..\extras\agility.sql >> ..\logs\install.sql
type ..\extras\users.sql >> ..\logs\install.sql
mysql\bin\mysql -u root < ..\logs\install.sql
del ..\logs\install.sql
del ..\logs\first_install
echo Opening AgilityContest console for first time...
start /W /MAX "AgilityContest" https://localhost/agility/console/index.php?installdb=1
goto wait_for_end

rem normal start when database is installed
:browser_start
echo Opening AgilityContest console...
start /W /MAX "AgilityContest" https://localhost/agility/console

:wait_for_end
echo Please wait for navigator window to show up...
echo -------------------------------------------
echo DO NOT CLOSE THIS WINDOW UNTIL SESSION END
echo -------------------------------------------
set /p key= Press enter to finish AgilityContest session

rem shutdown apache server
echo Apache Web Server shutdowm ...
apache\bin\pv -f -k httpd.exe -q
if not exist apache\logs\httpd.pid GOTO stop_mysql
del apache\logs\httpd.pid

rem shutdown mysql database server
:stop_mysql
echo MySQL DataBase shutdowm ...
apache\bin\pv -f -k mysqld.exe -q

if not exist mysql\data\%computername%.pid GOTO finish
echo Delete %computername%.pid ...
del mysql\data\%computername%.pid

rem That's all folks
:finish
