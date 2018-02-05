@echo off
call settings.bat
cd /d %~dp0\xampp
rem echo AgilityContest Launch Script
start "" /B mshta "javascript:var sh=new ActiveXObject( 'WScript.Shell' ); sh.Popup( 'AgilityContest is starting. Please wait', 20, 'Working...', 64 );close()"

rem notice that this may require admin privileges
rem for windows 8 and 10 disable w3svc service
rem also configure firewall to allow http https and mysql
net stop W3SVC
netsh advfirewall firewall add rule name="MySQL Server" action=allow protocol=TCP dir=in localport=3306
netsh advfirewall firewall add rule name="Apache HTTP Server" action=allow protocol=TCP dir=in localport=80
netsh advfirewall firewall add rule name="Apache HTTPs Server" action=allow protocol=TCP dir=in localport=443

rem if required prepare portable xampp to properly setup directories
if not exist ..\logs\first_install GOTO mysql_start
rem echo Configuring first boot of XAMPP
set PHP_BIN=php\php.exe
set CONFIG_PHP=install\install.php
%PHP_BIN% -n -d output_buffering=0 -q %CONFIG_PHP% usb >nul

rem start mysql database server
:mysql_start
rem echo MySQL Database is trying to start
rem echo Please wait  ....
start "" /B mysql\bin\mysqld --defaults-file=mysql\bin\my.ini --standalone --console >nul
rem timeout  5
ping -n 5 127.0.0.1 >nul

rem start apache web server
:apache_start
rem echo Starting Apache Web Server....
start "" /B apache\bin\httpd.exe >nul
rem timeout  5
ping -n 5 127.0.0.1 >nul

rem on first run create database and database users
if not exist ..\logs\first_install GOTO browser_start
rem echo Creating AgilityContest Databases. Please wait
timeout /t 5
echo DROP DATABASE IF EXISTS agility; > ..\logs\install.sql
echo CREATE DATABASE agility; >> ..\logs\install.sql
echo USE agility; >> ..\logs\install.sql
rem type ..\extras\agility.sql >> ..\logs\install.sql
type ..\extras\users.sql >> ..\logs\install.sql
mysql\bin\mysql -u root < ..\logs\install.sql
del ..\logs\install.sql
del ..\logs\first_install
rem echo Opening AgilityContest console for first time...
start /MAX "AgilityContest" https://localhost/agility/console/index.php?installdb=1
goto wait_for_end

rem normal start when database is installed
:browser_start
rem echo Opening AgilityContest console...
start /MAX "AgilityContest" https://localhost/agility/console

:wait_for_end
exit