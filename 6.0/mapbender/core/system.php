<?php
# $Id: system.php 8422 2012-07-06 06:27:41Z tbaschetti $
# Copyright (C) 2010 OSGeo
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

//define("SESSION_NAME", "TRUNK_SESSID");
#
# configuration file
#
require_once dirname(__FILE__)."/../conf/mapbender.conf";

#
# mapbender version
#
define("MB_VERSION_NUMBER", "2.7.3");
define("MB_VERSION_APPENDIX", "");
define("MB_RELEASE_DATE", mktime(0,0,0,07,06,2012));//h, min,sec,month,day,year

#
# constants from map.js
#
define("MB_RESOLUTION", "28.35");
define("MB_FEATURE_COUNT", "100");
define("MB_SECURITY_PROXY", "");

#
# available log levels
#
define("LOG_LEVEL_LIST", "off,error,warning,notice,all");

#
# OpenLayers path
#
define("OPENLAYERS_PATH", "../extensions/OpenLayers-2.9.1/");

#
# Module search paths
#
$pathArray = array(
	"../javascripts/",
	OPENLAYERS_PATH,
	OPENLAYERS_PATH . "lib/OpenLayers/"
);
define("MODULE_SEARCH_PATHS", implode(",", $pathArray));
unset($pathArray);

# FirePHP error log re-routing
define("LOG_PHP_WITH_FIREPHP", "off"); // "on" or "off"

define("MODULES_NOT_RELYING_ON_GLOBALS",
	"mapframe1,featureInfo1,gazetteerWFS,back,forward,zoomCoords,zoomFull,zoomIn1," .
	"zoomOut1,selArea1,pan1,copyright,dependentDiv,dragMapSize," .
	"dynamicOverview,FeatureInfoRedirect,highlightPOI,navFrame,sandclock," .
	"scaleBar,scaleSelect,setBBOX,setPOI2Scale,reload,overview,addWMS," .
	"repaint,changeEPSG,User,AdminTabs,GroupEditor,GuiEditor,UserEditor,".
	"scalebar,addWMSfromTree,mousewheelZoom,mapframe1_mousewheelZoom,doubleclickZoom," .
	"overviewToggle,resizeMapsize,coordsLookup,selArea1,loadwmc,savewmc," . 
	"resultList,mb_featureList_digitize,md_editor_container,md_editor_data," .
	"md_editor_navigation,md_editor_search_data,md_editor_result_data,metadata_create_data,".
	"metadata_create_service,metadata_create_application,mb_md_selectAction,mb_md_editMetadataByData,mb_md_path," . 
	"mb_md_editMetadataByService,mb_md_editMetadataByApplication,md_editor_xml_import," . 
	"muenster_setExtRequest,muenster_toolbar,csvUpload,md_editor_xml_import_service,toggleModule"
);


/*
 *	Function to check a path for security.
 */

define("MB_BASEDIR",realpath(dirname(__FILE__)."/../"));

if(!defined("PREPAREDSTATEMENTS")){
	define("PREPAREDSTATEMENTS", true);
}


function secure($path,$folder = "",$fileExt = null) {
	$secure = true;
	if(!defined("MB_BASEDIR")){ throw new Exception("MB_BASEDIR must be defined in core/system.php"); }
	$basedir = realpath(MB_BASEDIR."/".$folder);
	$path = realpath($path);
	// $path must be within the basedir (and optionally within the subdirectory within basedir given by the $folder parameter
	if(substr($path,0,strlen($basedir)) != $basedir){$secure = false;}

	// PATH END
	if(!empty($fileExt) AND substr($path,-strlen($fileExt)) != $fileExt){
		$secure = false;
	} 

	if($secure){
		return $path;
	} else {
		throw new Exception("This path is not allowed! '$path'");
	}
}

/*
 *	@security_patch Helper
 */
function security_patch_log($file,$line) {
    /*
	$h = fopen(dirname(__FILE__)."/../log/security_patch.log","a+");
	if($h) {
                $post_out = '
--------------------------------------------------------
$postvars = explode(",", "'.implode(",",array_keys($_POST)).'");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}
--------------------------------------------------------';
		fwrite($h,"\n".date("Y.m.d H:i")." FILE : ".$file." | LINE : ".$line." | POST : ".implode(",",array_keys($_POST))." | GET : ".implode(",",array_keys($_GET))." | FILE : ".implode(",",array_keys($_FILE))." |\n".$post_out);
		fclose($h);
	}
   */
}

//
// get real ip address, can be improved, see
// http://roshanbh.com.np/2007/12/getting-real-ip-address-in-php.html
//
function getRealIpAddr() {
	if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	{
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	{
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

//
// database wrapper
//
require_once dirname(__FILE__) . "/../lib/database-pgsql.php";

//
// establish database connection
//
$con = db_connect(DBSERVER, OWNER, PW);
db_select_db(DB, $con);



//
// Add FirePHP for debugging only, supply a global $firephp
//
if (defined("LOG_PHP_WITH_FIREPHP") && LOG_PHP_WITH_FIREPHP === "on") {
	require_once(dirname(__FILE__)."/../http/extensions/FirePHP-0.3/FirePHP.class.php");

	$firephp = FirePHP::getInstance(true);
}

//
// All data Mapbender handles internally are UTF-8
//
mb_internal_encoding("UTF-8");

//
// if magic quotes is on, automatically strip slashes
// (non-recursive due to possible security hazard)
//
if (get_magic_quotes_gpc()) {
	$in = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);

	while (list($k, $v) = each($in)) {
		foreach ($v as $key => $val) {
			if (!is_array($val)) {
				$in[$k][$key] = stripslashes($val);
				continue;
			}
			$in[]= &$in[$k][$key];
		}
	}

	unset($in);
}

//
// until we have decided how to implement a public user,
// use this constant. In Geoportal.rlp.de it was used as ANONYMOUS_USER
//
if (!defined("PUBLIC_USER")) define("PUBLIC_USER", "");

if (!defined("LOAD_JQUERY_FROM_GOOGLE")) define("LOAD_JQUERY_FROM_GOOGLE", false);

//
// class for error handling
//
if (!defined("LOG_DIR")) define("LOG_DIR", dirname(__FILE__) . "/../log/");
require_once dirname(__FILE__)."/../http/classes/class_mb_exception.php";

//
// Do not display PHP errors
//
ini_set("display_errors", "0");

//
// AJAX wrapper
//
require_once dirname(__FILE__)."/../lib/ajax.php";

// Max. size of WFS Responses in Byte
// If exceeded, an error message will be returned
// Default is 100k
#defined("WFS_RESPONSE_SIZE_LIMIT") || define("WFS_RESPONSE_SIZE_LIMIT", 1024*100);

?>
