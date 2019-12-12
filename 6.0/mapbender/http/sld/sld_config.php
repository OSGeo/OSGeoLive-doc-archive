<?php
# $Id: sld_config.php 6728 2010-08-10 08:31:29Z christoph $
# http://www.mapbender.org/index.php/SLD
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
 * This file contains some global configuration variables for the sld-editor
 *
 * @package sld_config
 * @author Markus Krzyzanowski
 */

include_once(dirname(__FILE__)."/classes/StyledLayerDescriptor.php");
require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
include_once(dirname(__FILE__)."/sld_parse.php");

// this should come from mapbender.conf
$SLD_MAIN = "sld_main.php?".$urlParameters;
$SLD_FUNCTION_HANDLER = "sld_function_handler.php?".$urlParameters;
$MAPBENDER_URL = "http://".$_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME']);

function char_encode($s){
	if(CHARSET == 'UTF-8'){
		$s = utf8_encode($s);
	}
	return $s;
}

if (isset($_REQUEST["sld_wms_id"]) && isset($_REQUEST["sld_gui_id"]))
{
	$_SESSION["sld_wms_id"] = $_REQUEST["sld_wms_id"];
	$_SESSION["sld_gui_id"] = $_REQUEST["sld_gui_id"];
}

if (isset($_REQUEST["sld_layer_name"]))
{
	$_SESSION["sld_layer_name"] = $_REQUEST["sld_layer_name"];
}

if (isset($_SESSION["sld_wms_id"]) && isset($_SESSION["sld_layer_name"]))
{
	$layer_name = $_SESSION["sld_layer_name"];
	$wms_id = $_SESSION["sld_wms_id"];

	//Read from DB
	require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);
	$sql = "SELECT * FROM wms WHERE wms_id = $1"; 
	$v = array($wms_id);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	
	$mapfileUrl = "";
	if ( db_fetch_row($res, 0) )
	{
		$mapfileUrl = db_result($res, 0, "wms_getmap");
		//echo $mapfileUrl;
	}
	
}

?>