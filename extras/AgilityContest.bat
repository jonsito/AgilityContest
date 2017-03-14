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
echo Installing MySQL as service ...
mysql\bin\mysqld.exe --defaults-file=mysql\bin\my.ini --install "MySQL for AgilityContest"
echo Starting MySQL database server ....
net start "MySQL for AgilityContest"

rem start apache web server
:apache_start
echo Installing Apache as service
apache\bin\httpd.exe -k install -n "Apache for AgilityContest"
echo Starting Apache web server ...
net start "Apache for AgilityContest"

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
start /MAX "AgilityContest" https://localhost/agility/console/index.php?installdb=1
goto finish

rem normal start when database is installed
:browser_start
echo Opening AgilityContest console...
start /MAX "AgilityContest" https://localhost/agility/console

rem as started as service no need to manually stop servers. let the system do the job
rem That's all folks
:finish
