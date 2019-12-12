<?php
# $Id:database-pgsql.php 2619 2008-07-08 15:46:11Z christoph $
# http://www.mapbender.org/index.php/database-pgsql.php
# Copyright (C) 2002 CCGIS
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2, or (at your option)
# any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

/**
 * \file
 * \brief MySQL database connection/querying layer
 * 
 * MySQL database connection/querying layer
 *
 * example:
 * \code
 * include_once(dirname(__FILE__)."/afwphp/database-mysql.php");  
 * $sys_dbhost=...                            
 * $sys_dbuser=...                            
 * $sys_dbpasswd=...                           
 * $sys_dbname=...                            
 *                                             
 * db_connect();                               
 * ...                                         
 * $rs = db_query("select * from table");      
 * while($row = db_fetch_array($rs));
 *  ...
 * \endcode
 */

/**
 * System-wide database type
 *
 * @var	constant		$sys_database_type
 */
$sys_database_type='pgsql';

/**
 *  Connect to the database
 *
 *  Notice the global vars that must be set up
 *  Notice the global vars $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname that must be set up 
 *  in other functions in this library
 */
include_once(dirname(__FILE__)."/../http/classes/class_mb_exception.php");
include_once(dirname(__FILE__)."/../http/classes/class_checkInput.php");
function db_escape_string($unescaped_string){
	return @pg_escape_string(stripslashes($unescaped_string));
}
$DB = DB;


function db_connect($DBSERVER="",$OWNER="",$PW="") {
	global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname,$db_debug;
	global $conn,$conn_update,$DB;	

	
	$db_debug=0;
	if ($DBSERVER)
		$sys_dbhost = $DBSERVER; 
	if ($OWNER)
		$sys_dbuser = $OWNER; 
	if ($PW && $PW != null)
		$sys_dbpasswd = $PW; 
	
	$sys_dbport = PORT; 	
		
	if($GLOBALS['DB'])
		$sys_dbname = $DB;
			
	$connstring = "";
	if ($sys_dbuser)		
		$connstring.=" user=$sys_dbuser";
	if ($sys_dbname)		
		$connstring.=" dbname=$sys_dbname";		
	if ($sys_dbhost)		
		$connstring.=" host=$sys_dbhost";	
	if ($sys_dbport)		
		$connstring.=" port=$sys_dbport";		
	if ($sys_dbpasswd)		
		$connstring.=" password=$sys_dbpasswd";		
	
	if ($db_debug)
		echo $connstring." ";		

	$conn = pg_connect($connstring);		

		#if(isset($sys_db_clientencoding) && $sys_db_clientencoding > "")
		#{
		#pg_set_client_encoding ( $conn, $sys_db_clientencoding);
		#}
	#return $conn;
	if ($db_debug)
		echo "conn=".$conn;
#echo $connstring;
#if(!$conn)
#{echo "FEHLER in Connection";
#pg_error($conn);}	
	
	return $conn;
}

function db_select_db($DB,$con="") {
	global $conn,$sys_dbname; 
#	$sys_dbname = DB;	
#	$_con = $con ? $con : $conn;
#	$ret = @mysql_select_db($sys_dbname,$_con);
//	echo "$ret=@mysql_select_db($sys_dbname,$_con);";
}

/**
 *  Query the database
 *
 *  @param		$qstring (string)	SQL statement
 *  @param		$limit (int)		How many rows do you want returned
 *  @param		$offset (int)		Of matching rows, return only rows starting here
 */
function db_query($qstring) {
	global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname,$db_debug,
		$conn,$conn_update,$QUERY_COUNT,$DBSERVER,$OWNER,$PW,$DB;
	$QUERY_COUNT++;
	$ret = pg_exec($qstring);
//	$e = new mb_exception("not ps:  ".$_SERVER['SCRIPT_FILENAME']." : ".$qstring);
	if(!$ret){
		$e = new mb_exception("db_query($qstring)=$ret db_error=".db_error());
	}
	return $ret;	
}
/**
 *  prepare and query the database
 *
 *  @param		$qstring (string)	SQL statement
 *  @param		$params (array params as strings)		
 *  @param		$types (array types as strings)		
 */
function db_prep_query($qstring, $params, $types){
	$orig_qstring = $qstring;
	$ci = new checkInput($qstring,$params,$types);
	$params = $ci->v; 
	if(PREPAREDSTATEMENTS == false){
		for ($i=0; $i<count($params); $i++){
			$needle = "$".strval($i+1);
			$tmp = '';
			if($params[$i] !== NULL){
				if($types[$i] == 's'){ $tmp .= "'"; }
				$tmp .= $params[$i];
				if($types[$i] == 's'){ $tmp .= "'"; }
			}
			else{
				$tmp .= "NULL";
			}
			$posa = mb_strpos($qstring, $needle);
			if(!$posa) { 
 				$e = new mb_exception("Error while preparing statement in ".$_SERVER['SCRIPT_FILENAME']. ": Sql :". $orig_qstring .",Error: parameter '$needle' not found ");
 			}
			$posb = mb_strlen($needle);
			$qstring = mb_substr($qstring,0,$posa).$tmp.mb_substr($qstring,($posa + $posb));	
		}
		$r = db_query($qstring);
		if(!$r){
			$e = new mb_exception("Error while executing sql statement in ".$_SERVER['SCRIPT_FILENAME'].": Sql: ".$qstring.", Error: ".db_error());
		}
	}
	else{
		$result = pg_prepare("", $qstring);
		if(!$result){
			$e = new mb_exception("Error while preparing statement in ".$_SERVER['SCRIPT_FILENAME'].": Sql: ".$qstring.", Error: ".db_error());
		}
		$r = pg_execute("", $params);
		if(!$r){
			$e = new mb_exception("Error while executing prepared statement in ".$_SERVER['SCRIPT_FILENAME'].": Sql: ".$qstring.", Error: ".db_error());
		}
	}	
	return $r;
}
/**
 *	Begin a transaction
 *
 *	Begin a transaction for databases that support them
 *	may cause unexpected behavior in databases that don't
 */
function db_begin() {
	return db_query("BEGIN WORK");
}

/**
 * Commit a transaction
 *
 * Commit a transaction for databases that support them
 * may cause unexpected behavior in databases that don't
 */
function db_commit() {
	return db_query("COMMIT");
}

/**
 * Roll back a transaction
 *
 * Rollback a transaction for databases that support them
 * may cause unexpected behavior in databases that don't
 */
function db_rollback() {
	$str = db_error();
	db_query("ROLLBACK");
	die('sql error: ' . $str . " ROLLBACK performed....");
}

/**
 * Returns the number of rows in this result set
 *
 *  @param		$qhandle (string)	Query result set handle
 */
function db_numrows($qhandle) {
	// return only if qhandle exists, otherwise 0
	if ($qhandle) {
		return @pg_numrows($qhandle);
	} else {
		return 0;
	}
}
/**
 * Returns the number of rows in this result set
 *
 *  @param		$qhandle (string)	Query result set handle
 *	php > 4.2
 */ 
function db_num_rows($qhandle) {
	// return only if qhandle exists, otherwise 0
	if ($qhandle) {
		return @pg_num_rows($qhandle);
	} else {
		return 0;
	}
}

/**
 *  Frees a database result properly 
 *
 *  @param	$qhandle (string)	Query result set handle
 */
function db_free_result($qhandle) {
	return @pg_freeresult($qhandle);
}

/**
 *  Reset a result set.
 *
 *  Reset is useful for db_fetch_array sometimes you need to start over
 *
 *  @param		$qhandle (string)	Query result set handle
 *  @param		$row (int)		Row number
 */
function db_reset_result($qhandle,$row=0) {
#dummy
	return 0;#mysql_data_seek($qhandle,$row);
	
}

/**
 *  Returns a field from a result set
 *
 *  @param		$qhandle (string)	Query result set handle
 *  @param		$row (int)		Row number
 *  @param		$field (string)	Field name
 */
function db_result($qhandle,$row,$field) {
	return @pg_result($qhandle,$row,$field);
}

/**
 *  Returns the number of fields in this result set
 *
 *  @param		$lhandle (string)	Query result set handle
 */
function db_numfields($lhandle) {
	return @pg_numfields($lhandle);
}

/**
 *  Returns the number of fields in this result set
 *
 *  @param		$lhandle (string)	Query result set handle
 *	php >4.2
 */
function db_num_fields($lhandle) {
	return @pg_num_fields($lhandle);
}

/**
 *  Returns the number of rows changed in the last query
 *
 *  @param		$lhandle	(string) Query result set handle
 *  @param		$fnumber (int)	Column number
 */
function db_fieldname($lhandle,$fnumber) {
	   return @pg_fieldname($lhandle,$fnumber);
}

/**
 *  Returns the number of rows changed in the last query
 *
 *  @param		$qhandle (string)	Query result set handle
 */
function db_affected_rows($qhandle) {
	
	return @pg_cmdtuples($qhandle);
}

/**
 *  Fetch an array
 *
 *  Returns an associative array from 
 *  the current row of this database result
 *  Use db_reset_result to seek a particular row
 *
 *  @param		$qhandle (string)	Query result set handle
 */
function db_fetch_array($qhandle) {
	return @pg_fetch_array($qhandle);
}
/**                                                       
 * fetch a row into an associative array 
 * 
 *  @param		$qhandle (string)	Query result set handle
 *  @param		$fnumber (int)	Column number
 */
function db_fetch_assoc($qhandle) {
	return @pg_fetch_assoc($qhandle);

}
function db_fetch_all($qhandle){
		return @pg_fetch_all($qhandle);
}
/**                                                       
 * fetch a row into an array 
 * 
 *  @param		$qhandle (string)	Query result set handle
 *  @param		$fnumber (int)	Column number
 */
function db_fetch_row($qhandle,$fnumber=0) {
	  return pg_fetch_row($qhandle);
}

/**
 *  Returns the last primary key from an insert
 *
 *  @param		$qhandle (string)	Query result set handle
 *  @param		$table_name (string)	Is the name of the table you inserted into
 *  @param		$pkey_field_name (string)	Is the field name of the primary key
 */
function db_insertid($qhandle="",$table_name="",$pkey_field_name="") {
	$res=db_query("SELECT max($pkey_field_name) AS id FROM $table_name");
    if ($res && db_numrows($res) > 0) {
        return @db_result($res,0,'id');
    } else {
        return 0;
    }
}




function db_insert_id($qhandle="",$table_name="",$pkey_field_name="") {
		global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname,$db_debug,
		$conn,$conn_update,$QUERY_COUNT,$DBSERVER,$OWNER,$PW,$DB;
	/*	
	$oid =pg_last_oid($qhandle);
	echo $oid;
	
	$res=db_query("SELECT ".$pkey_field_name." FROM ".$table_name." WHERE oid =".$oid );
    if ($res && db_numrows($res) > 0) {
        return @db_result($res,0,0);
    } else {
        return 0;
    }*/
    $res=db_query("SELECT max($pkey_field_name) AS id FROM $table_name");
    if ($res && db_numrows($res) > 0) {
        return db_result($res,0,'id');
    } else {
        return 0;
    }
}

function db_last_oid()
      {
      	global $sys_dbhost,$sys_dbuser,$sys_dbpasswd,$sys_dbname,$db_debug,
		$conn,$conn_update,$QUERY_COUNT;
        global $DBSERVER,$OWNER,$PW,$DB	;
             return pg_getlastoid($conn);
      }


/**
 * Returns the last error from the database
 */
function db_error() {
	return @pg_last_error();
}

/**
 * Get the flags associated with the specified field in a result 
 *
 *  @param		$lhandle	(string) Query result set handle
 *  @param		$fnumber (int)	Column number
 *
 * 					Examples: "not_null", "primary_key", "unique_key", "multiple_key",					 
 *                    "blob", "unsigned", "zerofill","binary", "enum",                  
 *                    "auto_increment", "timestamp"                                     
 */

function db_field_flags($lhandle,$fnumber) {
	   print "db_field_flags()	isn't implemented";
	   
}

/**                                                       
 * Get the type of the specified field  
 *
 *  @param		$lhandle	(string) Query result set handle
 *  @param		$fnumber (int)	Column number
 */                                                       
                                                          
function db_field_type($lhandle,$fnumber) {               
	   return @pg_fieldtype($lhandle,$fnumber);         
}                                                         

/**                                                       
 * Get the length of the specified field                                                            
 *
 *  @param		$lhandle	(string) Query result set handle
 *  @param		$fnumber (int)	Column number
 */                                                       
                                                          
function db_field_len($lhandle,$fnumber) {               
	   return @pg_fieldlen($lhandle,$fnumber);         
} 

?>