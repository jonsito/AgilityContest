@echo off
cd /d %~dp0\..\xampp
echo AgilityContest Launch Script
echo MySQL Database is trying to start
echo Please wait  ...

echo Starting MySQL Database server...
start /B "" \xampp\mysql\bin\mysqld --defaults-file=mysql\bin\my.ini --standalone --console

echo Starting Apache Web Server....
start /B "" \xampp\apache\bin\httpd.exe

echo Opening AgilityContest console...
start /W /MAX "AgilityContest" https://localhost/agility/console

echo Please wait for navigator window to show up...

echo -------------------------------------------
echo DO NOT CLOSE THIS WINDOW UNTIL SESSION END
echo -------------------------------------------
set /p key= Press enter to finish AgilityContest session

echo Apache Web Server shutdowm ...
\xampp\apache\bin\pv -f -k httpd.exe -q
if not exist apache\logs\httpd.pid GOTO stop_mysql
del apache\logs\httpd.pid

:stop_mysql
echo MySQL DataBase shutdowm ...
\xampp\apache\bin\pv -f -k mysqld.exe -q

if not exist mysql\data\%computername%.pid GOTO finish
echo Delete %computername%.pid ...
del mysql\data\%computername%.pid

:finish
