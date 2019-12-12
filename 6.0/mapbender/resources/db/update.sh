#!/bin/bash
#
# create database for mapbender
#

function show_disclaimer() {
    # Disclaimer
    echo ""
    echo "DISCLAIMER: Mapbender update script. USE AT YOUR OWN RISK!"
    echo "The script will run the update SQLs to update you Mapbender database"
    echo ""
    echo "-------"
    echo ""
    echo "You can give all arguments on commandline:"
    echo "$0 <HOST> <PORT> <DBNAME> <DBUSER>"
    echo ""
    echo "e.g."
    echo "$0 localhost 5432 mapbender postgres"
    echo ""
    echo "------"    
    echo ""
}

function del_logfile() {
	rm log_*.txt
	rm err_*.txt

}

#get Database Configuration
function get_db_config(){

    echo ""
    echo "database host (e.g. localhost)?"
    read DBHOST
	: ${DBHOST:="localhost"}

    echo ""
    echo "database port (e.g. 5432 for postgres)?"
    read DBPORT
	: ${DBPORT:="5432"}

    # db name
    until [ -n "$DBNAME" ]
    do
        echo ""
        echo "database name?"
        read DBNAME
    done

    echo ""
    echo "database user?"
    read DBUSER

#using a password via commandline oder as shell var could lead to security problems, so we don't do it
#see  http://www.postgresql.org/docs/current/static/libpq-pgpass.html for a convenient way
#    echo ""
#    echo "Password for $DBUSER (will not be shown)?"
#	stty -echo
#	read DBPASSWORD
#	stty echo
}

#show Database Configuration
function show_db_config(){
    echo ""
    echo "Database Configuration:"
    echo "Database:"  $DBVERSION
    echo "encoding:" $DBENCODING
    echo "db host:" $DBHOST
    echo "db port:" $DBPORT
    echo "dbname:" $DBNAME
    echo "user:"     $DBUSER
}

#set Database Configuration from commandline
function set_db_config(){
	echo ""
    echo "set variables to input:"
	DBHOST=$1
	DBPORT=$2
	DBNAME=$3
	DBUSER=$4
}

#Create Database (Postgres)
function run_pgsql_update(){
    which psql  > /dev/null
    if [ $? -ne 0 ]
    then
        echo "commando 'psql' needed, but not found, exiting..."
        echo "is PostgreSQL installed?"
        echo ""
        exit 1;
    fi
	echo "update to 2.6.1"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.6_to_2.6.1_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt
	echo "update to 2.6.2"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.6.1_to_2.6.2_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt
	echo "update to 2.7rc1"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.6.2_to_2.7rc1_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt
    echo "update to 2.7rc2 to 2.7.1"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.7rc1_to_2.7rc2_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt
    echo "update to 2.7.2"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.7.1_to_2.7.2_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt
    echo "update to 2.7.3"
	psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/$DBENCODING/update/update_2.7.2_to_2.7.3_pgsql_$DBENCODING.sql $DBNAME >> log_update.txt 2>> err_update.txt    
    echo "update sequences"
    psql -U $DBUSER  -h $DBHOST -p $DBPORT -f pgsql/pgsql_serial_set_sequences_2.7.sql $DBNAME >> log_update.txt 2>> err_update.txt

}

#magic starts here
show_disclaimer;

#we only support postgres/utf8
DBENCODING="UTF-8"
DBVERSION="pgsql"

if [ $# -ne 4 ]
then
	echo "get_db_config - variables have to be set"	
	get_db_config;
else
	echo "set_db_config - variables where passed when the script was called"
	set_db_config $@;
fi;	
show_db_config;

if [ $DBVERSION = "pgsql" ]
then
    echo "Updating Postgres Database"
    run_pgsql_update;
fi

echo ""
echo "Update finished...check the log and error files to see if an error occured.";
