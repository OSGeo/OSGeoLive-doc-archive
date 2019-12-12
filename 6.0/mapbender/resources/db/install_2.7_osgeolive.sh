#!/bin/bash
#
# create database for mapbender
#

function show_disclaimer() {
    # Disclaimer
    echo ""
    echo "The script will create the database needed for Mapbender and enter the initial data"
    echo "The script will also compile the Mapbender.mo files needed for multi language support."
    echo ""
    echo "Before running this script make sure that:"
    # echo " * set the right to execute on this install script"
    # echo " * set the right to execute on mapbender/tools/i18n_update_mo.sh"
    # echo " * write access to mapbender/log"
    # echo " * write access to mapbender/http/tmp"
    # echo " * write access to mapbender/resources/locale/ and subdirectories"
    echo " * you have credentials for a PostgreSQL user with superuser privileges"
    echo " * you know under which account the web server is running (on Debian Linux typically www-data)"
    echo " * you know which group the web server user belongs (on Debian Linux typically www-data)"
    echo ""
    echo ""
    echo "If everything is prepared you can continue."
    echo "-------"
    echo ""
    echo "You can also give all arguments on commandline using:"
    echo "$0 <HOST> <PORT> <DBNAME> <DBTEMPLATE> <DBUSER>"
    echo ""
    echo "e.g."
    echo "$0 localhost 5432 mapbender template0 postgres"
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

   #ask for database host
    echo ""
    echo "database host (Default: localhost)?"
    read DBHOST
	: ${DBHOST:="localhost"}

   #ask for database port
    echo ""
    echo "database port (Default: 5432 for postgres)?"
    read DBPORT
	: ${DBPORT:="5432"}     
        
    # db name
    until [ -n "$DBNAME" ]
    do
        echo ""
        echo "database name?"
        read DBNAME
    done    

	#ask for database template
        echo ""
        echo "database template to use (Default: template0)?"
        read DBTEMPLATE
        : ${DBTEMPLATE:="template0"}
   
    echo ""
    echo "database user?"
    read DBUSER

#using a password via commandline or as a shell variable could lead to security problems, so we don't do it
#see  http://www.postgresql.org/docs/current/static/libpq-pgpass.html for a convenient way
#    echo ""
#    echo "Password for $DBUSER (will not be shown)?"
#	stty -echo
#	read DBPASSWORD
#	stty echo

}

#set Database Configuration from commandline
function set_db_config(){
	echo ""
    echo "set variables to input:"
	DBHOST=$1
	DBPORT=$2
	DBNAME=$3
	DBTEMPLATE=$4
	DBUSER=$5
	DBPASS=$6
}


#show Database Configuration
function show_db_config(){
    echo ""
    echo "Database Configuration:"
    echo "version:"  $DBVERSION
    echo "encoding:" $DBENCODING
    if [ $DBTEMPLATE ]
    then
        echo "postgres template:" $DBTEMPLATE
    fi
    echo "db host:" $DBHOST
    echo "db port:" $DBPORT
    echo "dbname:" $DBNAME
    echo "user:"     $DBUSER
    echo ""

}



#Create Database (Postgres)
function create_pgsql_db(){
    which psql createdb > /dev/null
    if [ $? -ne 0 ]
    then
        echo "commando psql or createdb needed, but not found, exiting..."
        echo "is PostgreSQL installed?"
        echo ""
        exit 1;
    fi
    echo "Your password will be asked twice, this is normal (unless .pgpass is used)"
    echo "creating database (this might take a while)"
    createdb -U $DBUSER  -h $DBHOST -p $DBPORT -E $DBENCODING $DBNAME -T $DBTEMPLATE
    cat pgsql/pgsql_schema_2.5.sql  \
      pgsql/$DBENCODING/pgsql_data_2.5.sql \
      pgsql/pgsql_serial_set_sequences_2.5.sql \
      pgsql/$DBENCODING/update/update_2.5_to_2.5.1rc1_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.5.1rc1_to_2.5.1_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.5.1_to_2.6rc1_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.6rc1_to_2.6_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.6_to_2.6.1_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.6.1_to_2.6.2_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.6.2_to_2.7rc1_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.7rc1_to_2.7rc2_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.7.1_to_2.7.2_pgsql_$DBENCODING.sql \
      pgsql/$DBENCODING/update/update_2.7.2_to_2.7.3_pgsql_$DBENCODING.sql \
      pgsql/pgsql_serial_set_sequences_2.7.sql \
	> _install.sql
    echo "inserting data (this might take a while longer)"
    psql -U $DBUSER -h $DBHOST -p $DBPORT -f _install.sql $DBNAME > log_schema.txt 2> error.log
    rm _install.sql
    echo ""

}
function update_mapbender_conf(){
	# creating mapbender.conf
	if [ -f ../../conf/mapbender.conf ]
	then
	echo "mapbender.conf already exists, not changed. Please check manually whether it needs to be updated!"
	else

	echo "Creating mapbender.conf..."
	if [ -z $DBPASS ]
	then
		echo -n "Please enter the password for user $DBUSER: "
		stty -echo
		read password
		stty echo
		echo ""  
	else
		password=$DBPASS
	fi
	
	
	sed -e "s/%%DBSERVER%%/$DBHOST/g" -e "s/%%DBPORT%%/$DBPORT/g" -e "s/%%DBNAME%%/$DBNAME/g" -e "s/%%DBOWNER%%/$DBUSER/g"  -e "s/%%DBPASSWORD%%/$password/g" ../../conf/mapbender.conf-dist >../../conf/mapbender.conf
	fi
	echo ""
}

function set_permissions(){
	echo " "
}

function compile_po(){
    echo ""
    echo "Compiling .po files..."
    cd ../../tools/
    sh ./i18n_update_mo.sh
    cd ../resources/db/
    echo "removing permissions on /resources/locale/ and subdirectories"
    chmod -R o-rw ../locale
    echo "removing execute rights on mapbender/tools/i18n_update_mo.sh"
    chmod -R o-rx ../../tools/i18n_update_mo.sh
    echo ""
}


#magic starts here
show_disclaimer;

# we only support postgresql and utf-8
DBENCODING="UTF-8"
DBVERSION="pgsql"
if [ $# -ne 5 ] && [ $# -ne 6 ]
then
	echo "Continue? (y)es or (n)o"
	read disclaimer
	if test $disclaimer != "y"
	then
		exit 1
	fi
	echo "get_db_config - variables have to be set"	
	get_db_config;
else
	echo "set_db_config - variables where passed when the script was called"
	set_db_config $@;
fi;
show_db_config;

echo "Creating Mapbender administration database"
create_pgsql_db;

set_permissions;
compile_po;

echo ""
echo "Finished...check the log and error files to see if an error occured.";


