@echo off
REM Script to update an existing Mapbender database
REM
setlocal
REM Delete old Logfiles
del log_update*.txt
del err_update*.txt
:PREP
echo.
echo ==============================================================================
REM Disclaimer
echo.
echo DISCLAIMER: Mapbender database update script. USE AT YOUR OWN RISK!
echo The script will run the update SQLs to update you Mapbender database
echo.
echo.
echo If everything is prepared you can continue.
echo "You can give nearly all arguments on commandline:"
echo "%0 <HOST> <PORT> <DBNAME> <DBUSER>"
echo ""
echo "e.g."
echo "%0 localhost 5432 mapbender postgres"

echo.
echo ==============================================================================
echo.
REM 4 Params expected
if not %4x==x goto ARGSSUPPLIED

echo Continue?
set /p PREPARED="(y)es or (n)o:"
if %PREPARED%x==x goto :PREP

if %PREPARED%==y goto :PREP_OK
if %PREPARED%==n goto :End
goto :PREP
:PREP_OK

rem Database
:DB_TYPE

echo.
set /p DBHOST="Database host (just hit return for default 'localhost'):"
echo.

echo.
set /p DBPORT="Database port (just hit return for default '5432'):"
echo.

echo.
set /p DBNAME="Database name:"
echo.

echo.
set /p DBUSER="Database user:"
echo.

rem Password
rem echo.
echo Database Password will be asked many times during update, unless pgpass.conf is used...

IF %DBHOST%x==x set DBHOST=localhost
IF %DBPORT%x==x set DBPORT=5432
goto CHOICES

:ARGSSUPPLIED
set DBHOST=%1
set DBPORT=%2
set DBNAME=%3
set DBUSER=%4

echo.
set USEDBTYPE=PostgreSQL
REM Encoding
set USEDBENC=UTF-8


:CHOICES
echo.
echo Your Choices:
echo Database Host: %DBHOST%
echo Database Port: %DBPORT%
echo Database Name: %DBNAME%
echo Database User: %DBUSER%
echo.
echo Looks ok, start install?
echo.
rem delete ARG#5
shift
set /p START_INSTALL="(y)es or (n)o? "
if %START_INSTALL%x==x goto CHOICES
if %START_INSTALL%==y goto START_INSTALL
goto PREP
:START_INSTALL
 echo on


:INSTPOSTGRESQL
REM do these exist?
psql --version 2> nul 1> nul
if NOT %ERRORLEVEL% == 0 goto PGNOTFOUND
set USEDBENC=UTF-8

echo performing updates
echo   update to 2.6.1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.6_to_2.6.1_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update to 2.6.2
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.6.1_to_2.6.2_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update to 2.7rc1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.6.2_to_2.7rc1_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update to 2.7rc2 to 2.7.1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.7rc1_to_2.7rc2_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update to 2.7.2
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.7.1_to_2.7.2_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update to 2.7.3
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.7.2_to_2.7.3_pgsql_%USEDBENC%.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt
echo   update sequences
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/pgsql_serial_set_sequences_2.7.sql %DBNAME% 1>> log_update.txt 2>> err_update.txt


goto END:

:PGNOTFOUND
echo Sorry, psql not found, must be in PATH-Variable, exiting...
goto END

:END
endlocal
echo Finished...check the log files to see if an error occured.
echo.
 
