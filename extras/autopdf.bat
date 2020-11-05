@ECHO OFF

REM EDITAR y ajustar valores correctos segun el manual
REM http://www.agilitycontest.es/downloads/ac_autopdf.pdf

REM Datos de la manga
SET PRUEBA=69
SET JORNADA=537
SET MANGA=873
SET CATEGORIAS=X
SET HOST=localhost

REM Datos del servidor web
SET SERVER=www.agilitycontest.es
SET FTP_USER=user
SET FTP_PASSWORD=password
SET FTP_PATH=/agility/resultados/OrdenSalida.pdf

REM Tiempo de refresco (segundos)
SET LOOP_INTERVAL=60

:loop

ECHO Importando PDF desde AgilityContest ...
c:\AgilityContest\xampp\apache\bin\curl.exe --insecure --silent  "https://%HOST%/agility/agility/ajax/pdf/print_ordenDeSalida.php?Operation=OrdenSalida&Prueba=%PRUEBA%&Jornada=%JORNADA%&Manga=%MANGA%&Categorias=%CATEGORIAS%" -o %TEMP%\OrdenSalida.pdf

ECHO Subiendo PDF al servidor web ...
c:\AgilityContest\xampp\apache\bin\curl.exe --silent --user %FTP_USER%:%FTP_PASSWORD% -T %TEMP%\OrdenSalida.pdf ftp://%SERVER%%FTP_PATH%

TIMEOUT /T %LOOP_INTERVAL% /NOBREAK
GOTO loop

:End
ECHO Proceso completado