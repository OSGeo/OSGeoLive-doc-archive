<?php
# $Id: mod_wfs_gazetteer_server.php 1190 2007-10-18 10:38:38Z baudson $
# http://www.mapbender.org/index.php/Administration
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_json.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");
require_once(dirname(__FILE__)."/../classes/class_universal_wfs_factory.php");
require_once(dirname(__FILE__)."/../classes/class_universal_gml_factory.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_configuration.php");

$user = new User(Mapbender::session()->get("mb_user_id"));
$command = $_REQUEST["command"];

/**
 * checks if a variable name is valid.
 * Currently a valid name would be sth. like Mapbender::session()->get("mb_user_id")
 * TODO: this function is also in mod_wfs_result!! Maybe merge someday.
 */
function isValidVarName ($varname) {
	if (preg_match("/[\$]{1}_[a-z]+\[\"[a-z_]+\"\]/i", $varname) != 0) {
		return true;
	}
	return false;
}

/**
 * If access to the WFS conf is restricted, modify the filter.
 * TODO: this function is also in mod_wfs_result!! Maybe merge someday.
 */
 function checkAccessConstraint($filter, $wfs_conf_id) {
	/* wfs_conf_element */
	$sql = "SELECT * FROM wfs_conf_element ";
	$sql .= "JOIN wfs_element ON wfs_conf_element.f_id = wfs_element.element_id ";
	$sql .= "WHERE wfs_conf_element.fkey_wfs_conf_id = $1 ";
	$sql .= "ORDER BY wfs_conf_element.f_respos";
			
	$v = array($wfs_conf_id);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	while($row = db_fetch_array($res)){

		if (!empty($row["f_auth_varname"])) {
			$auth_varname = $row["f_auth_varname"];
			$element_name = $row["element_name"];
		}
	}
	if (!empty($auth_varname)) {

		if (isValidVarName($auth_varname)) {
			$user = eval("return " . $auth_varname . ";");
			if ($user) {
				$pattern = "(<ogc:Filter[^>]*>)(.*)(</ogc:Filter>)";
				$replacement = "\\1<And>\\2<ogc:PropertyIsEqualTo><ogc:PropertyName>" . $element_name . "</ogc:PropertyName><ogc:Literal>" . $user . "</ogc:Literal></ogc:PropertyIsEqualTo></And>\\3"; 
				$filter = mb_eregi_replace($pattern, $replacement, $filter);
			}
			else {
				$e = new mb_exception("mod_wfs_gazetteer_server: checkAccessConstraint: invalid value of variable containing user information!");
			}
		}
		else {
			$e = new mb_exception("mod_wfs_gazetteer_server: checkAccessConstraint: var name is not valid! (" . $auth_varname . ")");
		}
	}
	return $filter;
}


if ($command == "getWfsConf") {
	
	$wfsConfIdString = $_GET["wfsConfIdString"];
	
	if ($wfsConfIdString != "") {
		//array_keys(array_flip()) produces an array with unique entries
		$wfsConfIdArray = array_keys(array_flip(mb_split(",", $wfsConfIdString)));
		$availableWfsConfIds = $user->getWfsConfByPermission();
		
		$wfsConfIdArray = array_intersect($wfsConfIdArray, $availableWfsConfIds);
		if (count($wfsConfIdArray) === 0) {
			echo "no wfs conf available.";
			die();
		}
	}
	else {
		echo "please specify wfs conf id.";
		die();
	}
	
	$obj = new WfsConf();
	$obj->load($wfsConfIdArray);
	$json = new Mapbender_JSON();
	$output = $json->encode($obj->confArray);
	echo $output;
}
elseif ($command == "getWfsConfsForThisApplication") {
	
	// get all WFS conf IDs for this application
	$availableWfsConfIds = $user->getWfsConfByPermission(Mapbender::session()->get("mb_user_gui"));
	
	$obj = new WfsConf();
	$obj->load($availableWfsConfIds);
	$json = new Mapbender_JSON();
	$output = $json->encode($obj->confArray);
	echo $output;
}
else if ($command == "getSearchResults") {
	$wfs_conf_id = $_REQUEST["wfs_conf_id"];
	$backlink = $_REQUEST["backlink"];
	$frame = $_REQUEST["frame"];
	$filter = $_REQUEST["filter"];
	$url = $_REQUEST["url"];
	$typename = $_REQUEST["typename"];
	$destSrs = $_REQUEST["destSrs"];

	$wfsConf = WfsConfiguration::createFromDb($wfs_conf_id);
	if (is_null($wfsConf)) {
		sendErrorMessage("Invalid WFS conf: " . $wfs_conf_id);
	}
	
	// append authorisation condition to filter
	$filter = checkAccessConstraint($filter, $wfs_conf_id);
	
	$admin = new administration();
	
	$filter = administration::convertIncomingString($filter);

	$wfsId = $wfsConf->wfsId;

	$myWfsFactory = new UniversalWfsFactory();
	$myWfs = $myWfsFactory->createFromDb($wfsId);
	$data = $myWfs->getFeature($typename, $filter,$destSrs);
	if ($data === null) die('{}');
	
	if (defined("WFS_RESPONSE_SIZE_LIMIT") && WFS_RESPONSE_SIZE_LIMIT < strlen($data)) {
		die("Too many results, please restrict your search.");
	}
//	$geomColumn = WfsConf::getGeomColumnNameByConfId($wfs_conf_id);

	$gmlFactory = new UniversalGmlFactory();
	$myGml = $gmlFactory->createFromXml($data, $wfsConf);
	if (!is_null($myGml)) {
		$geoJson = $myGml->toGeoJSON();
	}
	else {
		$geoJson = "{}";
	}
	
	header("Content-type:application/x-json; charset=utf-8");
	echo $geoJson;
}
else {
	echo "please enter a valid command.";
}
?>
