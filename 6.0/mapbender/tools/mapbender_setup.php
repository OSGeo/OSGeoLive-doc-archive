<?php
 # $Id: mapbender_setup.php 6864 2010-09-01 08:44:17Z tbaschetti $
 # Copyright (C) 2002 CCGIS 
 # Created on 18.05.2006/10:03:40
 #  
 # http://www.mapbender.org/index.php/Installation_en
 # Projekt: mapbender 
 # File: mapbender_setup.php
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
 
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
 
?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
	<head><meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta name="robots" content="noindex,nofollow">
	<title>Mapbender Setup-Checker</title>
</head>
<link rel="stylesheet" type="text/css" href="../css/mapbender.css">
<body>
<table  BGCOLOR="#ffffff" width="95%" height="95%" ALIGN="center" CELLSPACING="0" CELLPADDING="10" STYLE="-moz-border-radius:8px; border:2px #000000 solid;">
<tr><td VALIGN="center" STYLE="margin-bottom:0px; padding-bottom:0px;">
<H1 style="padding:0px; margin:0px; font:32px/32px bold Arial,Helvetica,sans-serif; font-stretch:extra-expanded;font-weight:bold">
<font align="left"" style="font-weight:bold" color="#000000">&nbsp;Ma</font><font color="#0000CE" style="font-weight:bold">p</font><font color="#C00000">b</font><font color="#000000" style="font-weight:bold">ender</font>
</H1>
<font color="#000000" style="font-weight:bold">Setup-Checker</font>
<br>
<HR STYLE="color:#629093; height:2px; margin:0px; padding:0px;" WIDTH="100%" NOSHADE COLOR="#808080">
</tr></tr>
<tr><td VALIGN="TOP">
	<table style="border: 2px solid rgb(128, 128, 128); -moz-border-radius-topleft: 8px; -moz-border-radius-topright: 8px; -moz-border-radius-bottomright: 8px; -moz-border-radius-bottomleft: 8px;" bgcolor=#dddddd cellspacing=0 cellpadding=0 width="95%">
	<th colspan="3" bgcolor=#F0F0F0>PHP Configurationcheck</th>
	<?php
###########################################
#PHP Configurationcheck
###########################################
#phpversion
	$check ="<tr ><td  width=\"25%\">php Version</td>";
	if (phpversion()>='5.1.0'){
		if (phpversion()<'5.2.0') $check .="<td width=\"10\"></td><td><font color=#0000FF>Version: " . phpversion() . "! You should think about upgrade to the current php version (get it <a href='http://www.php.net/downloads.php' target='_blank'>here</a>)</td></tr>";
		else $check .="<td width=\"10\">X</td><td><font color=#00D000>Version: " . phpversion() . "</td></tr>";
	}
	else $check .="<td width=\"10\"></td><td><font color=#FF0000>Version: " . phpversion() . "! Your PHP Version is very old, please upgrade to version >=5.1.0 to use full mapbender functionality and reduce problems! PHP >= 5.2 is recommended.</td></tr>";
#php-schnittstelle 
	if(php_sapi_name() == 'cgi') $check.="<tr><td >interface</td><td>X</td><td><font color=#00D000>CGI-PHP</td></tr>";
	else $check.="<tr><td >interface</td><td>X</td><td><font color=#00D000>Modul-PHP</td></tr>";
# path to php.ini
	if (!get_cfg_var('cfg_file_path')) $check .="<tr ><td>path to php.ini</td><td></td><td><font color=#FF0000>No Path to php.ini found</font></td></tr>";
	else $check .="<tr ><td>path to php.ini</td><td>X</td><td><font color=#00D000>" . get_cfg_var('cfg_file_path') . "</font></td></tr>";
# extension dir
	if (!get_cfg_var('extension_dir')||get_cfg_var('extension_dir')=='') $check .="<tr ><td>extension_dir</td><td></td><td><font color=#FF0000>no extension_dir set!</font></td></tr>";
	else $check .="<tr ><td>extension_dir</td><td>X</td><td><font color=#00D000>" . get_cfg_var('extension_dir') . "</font><font color='#0000FF'>(check the path, is it correct?)</font></td></tr>"; 
# session.save_path
	if (!get_cfg_var('session.save_path')) $check .="<tr ><td>session.save_path</td><td></td><td><font color=#FF0000>please configure a session.save_path!</font></td></tr>";
	else $check .="<tr ><td>session.save_path</td><td>X</td><td><font color=#00D000>" . get_cfg_var('session.save_path') . " </font><font color='#0000FF'>(check out the authorisation of the dir)</font></td></tr>"; 
# memory_limit
	if (get_cfg_var('memory_limit')) $check .="<tr ><td>memory Limit</td><td>X</td><td><font color=#00D000>" . get_cfg_var('memory_limit') . "</font><font color='#0000FF'> (running in memory-trouble with printing? Perhaps raise your memory limit)</font></td></tr>";
	else $check .="<tr ><td>memory Limit</td><td></td><td><font color=#FF0000>memory_limit must be set (30M will be enough for the moment)</font></td></tr>";
# error_reporting
#Error Reporting: 6135 =>error_reporting  =  E_ALL & ~E_NOTICE (6135-8(E_NOTICE))
#Error Reporting: 1 => error_reporting  =  E_ERROR
#Error Reporting: 6143 => error_reporting  =  E_ALL
	$check .="<tr ><td>error-reporting</td>";
	 if (get_cfg_var('error_reporting')==6143||get_cfg_var('error_reporting')==8) $check .="<td></td><td><font color=#FF0000>please set error_reporting to 'E_ALL & ~E_NOTICE' or 'E_ERROR' except for debugging</td></tr>";
	elseif  (get_cfg_var('error_reporting')==6135)$check .="<td>X</td><td><font color=#00D000>ok, error_reporting = E_ALL & ~E_NOTICE</td></tr>";
	elseif  (get_cfg_var('error_reporting')==1)$check .="<td>X</td><td><font color=#00D000>ok, error_reporting = E_ERROR</td></tr>";
	else $check .="<td></td><td><font color=#0000FF>(Your error_reporting configuration is not implementet into this test yet. You shoul know what you are doing or set it to E_ALL & ~E_NOTICE)</td></tr>";
# session.save_handler
	if (!get_cfg_var('session.save_handler')||get_cfg_var('session.save_handler')!='files') $check .="<tr ><td>session.save_handler</td><td></td><td><font color=#FF0000>session.save_handler must be set to 'session.save_handler = files'!</font></td></tr>";
	else $check .="<tr ><td>session.save_handler</td><td>X</td><td><font color=#00D000>session.save_handler = " . get_cfg_var('session.save_handler') . "</font></td></tr>"; 
# file_uploads
	$check .="<tr><td>file_Uploads</td>";
	 if (get_cfg_var('file_uploads')=='1') $check .= "<td>X</td><td><font color=#00D000>On</font></td></tr>";
	 else $check .= "<td></td><td><font color=#FF0000>Off</font></td></tr>";
# allow_url_fopen
	$check .="<tr ><td>allow_url_fopen</td>"; 
	if (get_cfg_var('allow_url_fopen')=='1') $check .= "<td>X</td><td><font color=#00D000>On</font></td></tr>";
	else $check .= "<td></td><td><font color=#FF0000>Off =>allow_url_fopen must be on read <a href='http://www.mapbender.org/index.php/Allow_url_fopen' target=_blank>this</a></font></td></tr>";
# short_open_tag 
	$check .="<tr ><td>short_open_tag</td>"; 
	if (get_cfg_var('short_open_tag')!='1') $check .= "<td>X</td><td><font color=#00D000>Off</font></td></tr>";
	else $check .= "<td></td><td><font color=#FF0000>On => Displaying XML files will not work properly</font></td></tr>";
# json
	$check .="<tr ><td>JSON support</td>"; 
	if (Mapbender_JSON::usesNative()) $check .= "<td>X</td><td><font color=#00D000>Native PHP</font></td></tr>";
	else $check .= "<td></td><td><font color=#FF0000>PEAR library, think about uprading to PHP >=5.2 </font><font color=#0000FF>(the library is error prone with huge data sets; some things like WMC load/save might not work properly)</font></td></tr>";
	echo $check;
#################################################
#PHP Extensioncheck
#################################################
	?>
	<th colspan="3" bgcolor=#F0F0F0>PHP Extensioncheck</th>
	<?php
#PGSQL
	if(!extension_loaded('pgsql')) $check="<tr><td>PostgreSQL check</td><td></td><td><font color=#FF0000>PostgreSQL not installed (You have to include pgsql-extension if you want to use Postgres as MB-Database!)</font></td></tr>";
	else $check="<tr><td>PostgreSQL check</td><td>X</td><td><font color=#00D000>PostgreSQL installed</font></td></tr>";
#GD
	if(extension_loaded('gd')) $check.="<tr ><td>GD2 check</td><td>X</td><td><font color=#00D000>GD installed</font></td></tr>";
	else $check.="<tr ><td>GD2 check</td><td></td><td><font color=#FF0000>GD not installed (no printing possible)</font></td></tr>";

#mbstring
	if(extension_loaded('mbstring')) $check.="<tr ><td>mbstring check</td><td>X</td><td><font color=#00D000>mbstring installed</font></td></tr>";
	else $check.="<tr ><td>mbstring check</td><td></td><td><font color=#FF0000>PHP extension mbstring is not installed</font></td></tr>";

#gettext
	if(extension_loaded('gettext')) $check.="<tr ><td>gettext check</td><td>X</td><td><font color=#00D000>gettext installed</font></td></tr>";
	else $check.="<tr ><td>gettext check</td><td></td><td><font color=#FF0000>PHP extension gettext is not installed</font></td></tr>";
#imagick
	if(extension_loaded('imagick')) $check.="<tr ><td>imagick check</td><td>X</td><td><font color=#00D000>imagick installed</font></td></tr>";
	else $check.="<tr ><td>imagick check</td><td></td><td><font color=#FF0000>PHP extension imagick is not installed</font></td></tr>";

echo $check;	    
####################################
# Database check
####################################
	?>
	</table>
	<br><br>
	<table style="border: 2px solid rgb(128, 128, 128); -moz-border-radius-topleft: 8px; -moz-border-radius-topright: 8px; -moz-border-radius-bottomright: 8px; -moz-border-radius-bottomleft: 8px;" bgcolor=#dddddd cellspacing=0 cellpadding=0 width="95%">
	<th colspan="3" bgcolor=#F0F0F0>Database check</th>
	<?php
	$con = @db_connect(DBSERVER,OWNER,PW);
	if (SYS_DBTYPE=="mysql"){
		$check = "<tr ><td width=\"25%\">Administration Database</td><td>X</td><td><font color=#FF0000>MySQL (no longer supported!)</td><tr>";
		$check .= "<tr ><td>Connect to Database</td>";
		if($con) $check .="<td width=\"10\">X</td><td><font color=#00D000>connected</font></td></tr>";
		else $check .="<td width=\"10\"></td><td><font color=#FF0000>not connected</font></td></tr>";
		$check .="<tr><td colspan=3><b>PostGIS function check</b></td></tr>";
		$con_string = "host= " . GEOS_DBSERVER . " port=" . GEOS_PORT . " dbname=" . GEOS_DB . " user=" . GEOS_OWNER . " password=" .GEOS_PW;
		if (pg_connect($con_string)){
			pg_connect($con_string);
			$con_postgis = pg_connect($con_string);
			$sql = "Select postgis_full_version();";
			$res = pg_query($con_postgis,$sql);
			if(!$res) $check .="<tr width=\"20%\><td>PostGIS support</td><td></td><td><font color=#FF0000>no PostGIS function available</td></tr>";
			else{
				$cnt=0;
				while(pg_fetch_row($res)){
					$check .="<tr><td>PostGIS support</td><td>X</td><td><font color=#00D000>PostGIS function available</td></tr>";
					$check .="<tr><td>Version</td><td>X</td><td><font color=#00D000>" . pg_fetch_result($res,$cnt,0). "</td></tr>";
		  		  	$cnt++;
		  		}
				if ($cnt==0) $check .="<tr><td>PostGIS support</td><td></td><td><font color=#FF0000>no PostGIS function available</td></tr>";
			}
		}
		else $check .="<tr><td>Postgis support</td><td></td><td><font color=#FF0000>no PostGIS function available</font></td></tr>";
		echo $check;
	}
	else{
		$check = "<tr><td width=\"25%\">Administration Database</td><td>X</td><td><font color=#00D000>PostgreSQL</td></tr>";
		$check .= "<tr><td>Connect to Database</td>";
		if($con) $check .="<td width=\"10\">X</td><td><font color=#00D000>connected</font></td></tr>";
		else $check .="<td width=\"10\"></td><td><font color=#FF0000>not connected</font></td></tr>";
# md5 support	
		$sql = "Select md5('root');";
		$res = pg_query($sql);
		if(!$res) $check .="<tr><td>MD5 support</td><td></td><td><font color=#FF0000>no md5 support</td></tr>";
		else{
			$row = db_fetch_array($res);
			if ($row) $check .="<tr><td>MD5 support</td><td>X</td><td><font color=#00D000>md5 supported</td></tr>";
			else $check .="<tr><td>MD5 support</td><td></td><td><font color=#FF0000>no md5 support</td></tr>";
		}
		echo $check;	
######################################
# PostGIS check
######################################	
	?>
	<th colspan="3" bgcolor=#F0F0F0>PostGIS check</th>
	<?php
		$check ="";
		if ($con){
			$sql = "select postgis_full_version();";
			if (pg_query($con,$sql))$res = pg_query($con,$sql);
			else echo "<tr><td><font>pg_query($con,$sql)";
			if(!$res) $check .="<tr><td width=\"25%\">PostGIS support</td><td width=\"10\"></td><td><font color=#FF0000>no PostGIS function available</td></tr>";
			else{
				$cnt=0;
				while(pg_fetch_row($res)){
					$check .="<tr><td width=\"25%\">PostGIS support</td><td width=\"10\">X</td><td><font color=#00D000>PostGIS function available</td></tr>";
					$check .="<tr><td>Version</td><td width=\"10\">X</td><td><font color=#00D000>" . pg_fetch_result($res,$cnt,0). "</td></tr>";
		  		  	$cnt++; 	
		  		}
				if ($cnt==0) $check .="<tr><td width=\"25%\">PostGIS support</td><td width=\"10\"></td><td><font color=#FF0000>no PostGIS function available</td></tr>";
			}
		}
		else $check .="<tr><td width=\"25%\">Postgis support</td><td width=\"10\"></td><td><font color=#FF0000>no PostGIS function available</font></td></tr>";
		echo $check;
	} 
#################################
# Mapbender configuration check
#################################
	?>
	</table>
	<br><br>
	<table style="border: 2px solid rgb(128, 128, 128); -moz-border-radius-topleft: 8px; -moz-border-radius-topright: 8px; -moz-border-radius-bottomright: 8px; -moz-border-radius-bottomleft: 8px;" bgcolor=#dddddd cellspacing=0 cellpadding=0 width="95%">
	<th colspan="4" bgcolor=#F0F0F0>Mapbender Configuration Check</th>
	<?php 
# SYS_DBTYPE
	if ((SYS_DBTYPE == 'mysql' || SYS_DBTYPE == 'pgsql') && defined('SYS_DBTYPE')) $check ="<tr><td>Administration Database</td><td >X</td><td><font color=#00D000>" . SYS_DBTYPE . "</font></td></tr>";
	else $check ="<tr><td width=\"25%\">Administration Database</td><td width=\"10\"></td><td><font color=#FF0000>SYS_DBTYPE is not defined for mysql or pgsql</font></td></tr>";
# DBSERVER
	if (DBSERVER !="<HOST>" && DBSERVER != "" && defined('DBSERVER')) $check .="<tr><td>DB-Server</td><td>X</td><td><font color=#00D000>" . DBSERVER . "</font><font color='#0000FF'> (is this your DB-Server)</font></td></tr>";
	else $check .="<tr><td>DB-Server</td><td></td><td><font color=#FF0000>DBSERVER is not defined</font></td></tr>";
# Mapbender-DB
	if (DB !="<database>" && DB != "" && defined('DB')) $check .="<tr><td>Mapbender-DB</td><td>X</td><td><font color=#00D000>" . DB . "</font><font color='#0000FF'> (is this your Mapbender-DB)</font></td></tr>";
	else $check .="<tr><td>Mapbender-DB</td><td></td><td><font color=#FF0000>DB is not defined</font></td></tr>";
# DB Owner
	if (OWNER !="<owner>" && OWNER != "" && defined('OWNER')) $check .="<tr><td>DB-Owner</td><td>X</td><td><font color=#00D000>" . OWNER . "</font><font color='#0000FF'> (is this your DB-Owner)</font></td></tr>";
	else $check .="<tr><td>DB-Owner</td><td></td><td><font color=#FF0000>OWNER is not defined</font></td></tr>";
# PREPAREDSTATEMENTS
	if (defined('PREPAREDSTATEMENTS')){
		if (PREPAREDSTATEMENTS == true){
			if (phpversion()<'5.1.0') $check.="<tr><td width=\"25%\">PREPAREDSTATEMENTS</td><td width=\"10\"></td><td><font color=\"#ff0000\">PREPAREDSTATEMENTS =set to 'true' and php version " . phpversion() . " is incompatible<br>set PREPAREDSTATEMENTS to false or update php to >=5.1</td></tr>";
			else $check .="<tr><td width=\"25%\">PREPAREDSTATEMENTS</td><td width=\"10\">X</td><td><font color=#00D000>set to 'true' and php " . phpversion() . " should work</td></tr>";
		}
		else{
			if (phpversion()<'5.1.0') $check .="<tr><td width=\"25%\">PREPAREDSTATEMENTS-<br>compatibility</td><td width=\"10\">X</td><td><font color=#00D000>set to 'false' and php " . phpversion() . " should work </font><font color='#0000FF'> (but think about upgrading to php 5.1)</td></tr>";
			else $check .="<tr><td width=\"25%\">PREPAREDSTATEMENTS-<br>compatibility</td><td width=\"10\">X</td><td><font color=#00D000>set to 'false' and php " . phpversion() . " should work <font color=#0000FF>(but you can set PREPAREDSTATEMENTS to 'true')</font></td></tr>";
		}
	}
    else $check .="<tr><td width=\"25%\">PREPAREDSTATEMENTS-<br>compatibility</td><td width=\"10\"></td><td><font color=#FF0000>PREPAREDSTATEMENTS is not defined</td></tr>";
# CHARSET
	if (CHARSET != "" && defined('CHARSET')) $check .="<tr><td>CHARSET</td><td>X</td><td><font color=#00D000>" . CHARSET . "</font><font color='#0000FF'></font></td></tr>";
	else $check .="<tr><td>CHARSET</td><td></td><td><font color=#FF0000>CHARSET is not defined</font></td></tr>";
# TMPDIR
	if (TMPDIR != "" && defined('TMPDIR')) $check .="<tr><td>TMPDIR</td><td>X</td><td><font color=#00D000>" . TMPDIR . "</font><font color='#0000FF'></font></td></tr>";
	else $check .="<tr><td>TMPDIR</td><td></td><td><font color=#FF0000>TMPDIR is not defined</font></td></tr>";
# OWSPROXY
	if (OWSPROXY != "" && defined('OWSPROXY')) $check .="<tr><td>OWSPROXY</td><td>X</td><td><font color=#00D000>" . OWSPROXY . "</font><font color=#0000FF> (Is this the right URL to your OWSPROXY?)</font></td></tr>";
	else $check .="<tr><td>OWSPROXY</td><td></td><td><font color=#FF0000>OWSPROXY not defined</font><font color=#0000FF>(if you want to camouflage your WMS, you should think about OWSPROXY!)</font></td></tr>";
#AUTO_UPDATE
	if (AUTO_UPDATE != "" && defined('AUTO_UPDATE')){
		if (AUTO_UPDATE == '1'){ 
			$check .="<tr><td>AUTO_UPDATE</td><td>X</td><td>set to 1: will update all out-of-date WMS automatically<td></tr>";
			if (!TIME_LIMIT || TIME_LIMIT == "")$check .="<tr><td>TIME_LIMIT</td><td></td><td><font color=#FF0000>you should define a TIME_LIMIT for the AUTO_UPDATE funtionallity</font><td></tr>";
		}
		elseif (AUTO_UPDATE == '0') $check .="<tr><td>AUTO_UPDATE</td><td>X</td><td><font color=#00D000>set to 0:</font> <font color=#0000FF>(see the result of the test and update WMS manually)</font></td></tr>";
		else $check .="<tr><td>AUTO_UPDATE</td><td></td><td><font color=#FF0000>set to " . AUTO_UPDATE . ": this configuration value is not supported(as yet!)</td></tr>";
	} 			    	
	else $check .="<tr><td>AUTO_UPDATE</td><td></td><td><font color=#FF0000>AUTO_UPDATE not defined </font><font color=#0000FF>(for the wms monitoring 	functionality you have to define this constant)</font></td></tr>";
# ERROR LOGGING
	$testLog = new mb_exception("This is a test run by the Mapbender setup script.");
	if ($testLog->result) {
		$check .="<tr><td>ERROR LOGGING</td><td>X</td><td><font color=#00D000>" . $testLog->message . "</font></td></tr>";		
	}
	else {
		$check .="<tr><td>ERROR LOGGING</td><td></td><td><font color=#FF0000>" . $testLog->message . "</font></td></tr>";		
	}
#LOG_LEVEL (off,error,warning,all)
	if (LOG_LEVEL !="" && defined('LOG_LEVEL')){
		if (LOG_LEVEL =='off') $check .="<tr><td>LOG_LEVEL</td><td>X</td><td>switched off: <font color=#FF0000>-no Mapbender-errors logging</font><td></tr>"; 
		elseif (LOG_LEVEL =='error') $check .="<tr><td>LOG_LEVEL</td><td>X</td><td><font color=#00D000>set to 'error': </font><font color=#0000FF>-Mapbender-errors will be logged</font><td></tr>";
		elseif (LOG_LEVEL =='warning') $check .="<tr><td>LOG_LEVEL</td><td>X</td><td><font color=#00D000>set to 'warning: </font><font color=#0000FF>- Mapbender-errors and -warnings will be logged</font><td></tr>";
		elseif (LOG_LEVEL =='notice') $check .="<tr><td>LOG_LEVEL</td><td>X</td><td><font color=#00D000>set to 'notice': </font><font color=#0000FF>-really every little notice will be logged!!</font><td></tr>";
		elseif (LOG_LEVEL =='all') $check .="<tr><td>LOG_LEVEL</td><td>X</td><td><font color=#00D000>set to 'all': </font><font color=#0000FF>-really every little notice will be logged!!</font><td></tr>";
		else $check .="<tr><td>LOG_LEVEL</td><td></td><td><font color=#FF0000>set to " . LOG_LEVEL . ": this configuration value is not supported (as yet!)</font></td></tr>";		
	}
# PORTAL
	if (defined('PORTAL')){
		if (PORTAL == true) $check .="<tr><td width=\"25%\">PORTAL</td><td width=\"10\">X</td><td><font color=#00D000>true</font><font color='#0000FF'> (Users can create theirs own accounts)</font></td></tr>";
		else $check .="<tr><td width=\"25%\">PORTAL</td><td width=\"10\">X</td><td><font color=#00D000>false<font color=#0000FF> (Users can't create their own accounts at the moment)</font></td></tr>";
	}
    else $check .="<tr><td width=\"25%\">PORTAL</td><td width=\"10\"></td><td><font color=#FF0000>PORTAL is not defined<font color=#0000FF>(Maybe an old configuration file?)</font></td></tr>";
# MAXLOGIN
	if (MAXLOGIN != "" && defined('MAXLOGIN')) $check .="<tr><td>MAXLOGIN</td><td>X</td><td><font color=#00D000>" . MAXLOGIN . "</font><font color='#0000FF'></font></td></tr>";
	else $check .="<tr><td>MAXLOGIN</td><td></td><td><font color=#0000FF>MAXLOGIN is not defined</font></td></tr>";		    	
# LOGIN
	if (defined('LOGIN')) $check .= "<tr height=10/><tr><td>Login-Path</td><td colspan=2><a href='" . LOGIN . "' target='_blank'>" . LOGIN . "</a><br><font color=#0000FF> (If this link doesn't work, check your url to 'Login' in your mapbender.conf<br>Perhaps an alias in your httpd.conf will solve the problem, too)</td>";
	else $check .= "<tr height=10/><tr><td>Login-Path</td><td colspan=2><font color=#FF0000>LOGIN is not defined</font></td>";
	echo $check;	
	echo "<tr height=10/><tr bgcolor=#F0F0F0><td colspan=4>Legend:<br><font color=#FF0000>red = maybe your Mapbender will run into trouble</font><br><font color=#0000FF>blue = just a tip</font><br><font color=#00D000>green = seems to be alright</font></td></tr>";
	echo "</table>";
	echo "<tr><td colspan=3 align=right>for further informations visit <a href=\"http://www.mapbender.org/index.php/Installation_en\" target=\"_blank\"><font align=\"left\" style=\"font-weight:bold\" color=\"#000000\">&nbsp;Ma</font><font color=\"#0000CE\" style=\"font-weight:bold\">p</font><font color=\"#C00000\" style=\"font-weight:bold\">b</font><font color=\"#000000\" style=\"font-weight:bold\">ender</font> installation instructions</a></td></tr>";
	?>
	</td></tr>
</table>
</body> 
</html>
