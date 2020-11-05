@ECHO OFF

REM EDITAR y ajustar valores correctos segun el manual
REM http://www.agilitycontest.es/downloads/ac_autopdf.pdf
SET PRUEBA=69
SET JORNADA=537
SET MANGA=873
SET CATEGORIAS=X
SET HOST=localhost

SET SERVER=www.agilitycontest.es
SET FTP_USER=user
SET FTP_PASSWORD=password
SET FTP_PATH=/agility/resultados/OrdenSalida.pdf

:loop
c:\AgilityContest\xampp\apache\bin\curl.exe --insecure --silent  "https://%HOST%/agility/agility/ajax/pdf/print_ordenDeSalida.php?Operation=OrdenSalida&Prueba=%PRUEBA%&Jornada=%JORNADA%&Manga=%MANGA%&Categorias=%CATEGORIAS%" -o c:\Windows\Temp\OrdenSalida.pdf
c:\AgilityContest\xampp\apache\bin\curl.exe --insecure --silent --user %FTP_USER%:%FTP_PASSWORD -T c:\Windows\Temp\OrdenSalida.pdf ftp://%SERVER%/%FTP_PATH
TIMEOUT /T 60 /NOBREAK
GOTO loop

:End
ECHO Proceso completado