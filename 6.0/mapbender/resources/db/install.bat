@echo off
REM Script to install Mapbender  database
REM
setlocal
REM Delete old Logfiles
del log_*.txt
del err_*.txt
:PREP
echo.
echo ==============================================================================
REM Disclaimer
echo.
echo DISCLAIMER: Mapbender database setup script. USE AT YOUR OWN RISK!
echo This script will create a database for you and will import the Mapbender data to the new database
echo It will possibly also compile the Mapbender.mo files
echo.
echo You need:
echo - a running postgresql 8.x database 
echo - database user with write access 
echo.
echo If everything is prepared you can continue.
echo You can give nearly all arguments on commandline:
echo "%0 <HOST> <PORT> <DBNAME> <DBTEMPLATE> <DBUSER>"
echo.
echo e.g.
echo %0 localhost 5432 mapbender template_postgis postgres

echo.
echo ==============================================================================
echo.
REM 5 Params expected
if not %5x==x goto ARGSSUPPLIED

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
echo "The following database template should be postgis-enabled!"
set /p DBTEMPLATE="database template to use (just hit return for default 'template_postgis'):"
echo.

echo.
set /p DBUSER="Database user:"
echo.

rem Password
rem echo.
echo Database Password will be asked many times during install, unless pgpass.conf is used...

IF %DBTEMPLATE%x==x set DBTEMPLATE=template_postgis
IF %DBHOST%x==x set DBHOST=localhost
IF %DBPORT%x==x set DBPORT=5432
goto CHOICES

:ARGSSUPPLIED
set DBHOST=%1
set DBPORT=%2
set DBNAME=%3
set DBTEMPLATE=%4
set DBUSER=%5
echo.

:CHOICES
REM dbtype and encoding are fixed
set USEDBTYPE=PostgreSQL
set USEDBENC=UTF-8
echo.
echo Your Choices:
echo Databasetype: %USEDBTYPE%
echo Encoding: %USEDBENC%
echo Database Host: %DBHOST%
echo Database Port: %DBPORT%
echo Database Name: %DBNAME%
echo Database Template: %DBTEMPLATE%
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
rem echo on


:INSTPOSTGRESQL
REM do these exist?
psql --version 2> nul 1> nul
if NOT %ERRORLEVEL% == 0 goto PGNOTFOUND

echo creating db
createdb -U %DBUSER% -E %USEDBENC% -h %DBHOST% -p %DBPORT% -T %DBTEMPLATE% %DBNAME% "Mapbender Database Version 2.6"
echo creating schema
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/pgsql_schema_2.5.sql %DBNAME% 1>log_schema.txt 2> err_schema.txt
echo importing data
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/pgsql_data_2.5.sql %DBNAME% 1>log_data.txt 2> err_data.txt
echo setting sequences
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/pgsql_serial_set_sequences_2.5.sql %DBNAME% 1>log_sequences.txt 2> err_sequences.txt

echo performing updates
echo   update to 2.5.1rc1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.5_to_2.5.1rc1_pgsql_%USEDBENC%.sql %DBNAME% 1>log_update.txt 2> err_update.txt
echo   update to 2.5.1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.5.1rc1_to_2.5.1_pgsql_%USEDBENC%.sql %DBNAME% 1>>log_update.txt 2>> err_update.txt
echo   update to 2.6rc1
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.5.1_to_2.6rc1_pgsql_%USEDBENC%.sql %DBNAME% 1>>log_update.txt 2>> err_update.txt
echo   update to 2.6
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/%USEDBENC%/update/update_2.6rc1_to_2.6_pgsql_%USEDBENC%.sql %DBNAME% 1>>log_update.txt 2>> err_update.txt
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
psql -U %DBUSER% -h %DBHOST% -p %DBPORT% -f pgsql/pgsql_serial_set_sequences_2.7.sql %DBNAME% 1>> log_sequences.txt 2>> err_sequences.txt

:POFILES
rem install mapbender.conf
if not exist ..\..\conf\mapbender.conf copy ..\..\conf\mapbender.conf-dist ..\..\conf\mapbender.conf
echo please check and edit your mapbender.conf.

rem update .po files
echo ""
msgfmt --version 2> nul 1> nul
if NOT %ERRORLEVEL% == 0 goto MSGFMTNOTFOUND
echo "Compiling .po files..."
pushd ..\..\tools\
findstr /v "^# ^chmod" i18n_update_mo.sh > i18n_update_mo.bat
call i18n_update_mo.bat
del i18n_update_mo.bat
popd
goto END:

:MSGFMTNOTFOUND
echo Sorry, msgfmt not found, must be in PATH-Variable, won't compile translations...
echo Have a look at http://www.mapbender.org/Gettext#Utility_programms for msgfmt.
goto END:

:PGNOTFOUND
echo Sorry, psql not found, must be in PATH-Variable, exiting...
goto END

REM End, keep Terminal session open
:END
endlocal
echo Finished...check the log files to see if an error occured.
echo.
pause 
