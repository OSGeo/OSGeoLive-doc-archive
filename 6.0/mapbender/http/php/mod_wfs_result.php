<?php
# $Id: mod_wfs_result.php 7765 2011-04-12 12:30:43Z verenadiewald $
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
require_once(dirname(__FILE__) . "/../classes/class_stripRequest.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_configuration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs.php");
require_once(dirname(__FILE__)."/../classes/class_universal_wfs_factory.php");
require_once(dirname(__FILE__)."/../classes/class_universal_gml_factory.php");

$filter = $_REQUEST["filter"];
$db_wfs_conf_id = $_REQUEST["db_wfs_conf_id"];
$typename = $_REQUEST["typename"];
$destSrs = $_REQUEST["destSrs"];

/**
 * checks if a variable name is valid.
 * Currently a valid name would be sth. like Mapbender::session()->get("mb_user_id")
 * TODO: this function is also in mod_wfs_result!! Maybe merge someday.
 */
function isValidVarName ($varname) {
	if (preg_match("/[\$]{1}_[a-z]+\[\"[a-z_]+\"\]/i", $varname) !== 0) {
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
	$sql = "SELECT f.featuretype_name AS name FROM " . 
		"wfs_featuretype AS f, wfs_conf AS c " . 
		"WHERE c.wfs_conf_id = $1 AND " . 
		"c.fkey_featuretype_id = f.featuretype_id";
	$v = array($wfs_conf_id);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	$row = db_fetch_array($res);
	if ($row) {
		$ns = substr($row["name"], 0, strpos($row["name"], ":")) . ":";
	}
	else {
		$ns = "";
	}
	
	unset($sql);
	unset($v);
	unset($t);
	unset($res);
			
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
	$e = new mb_exception($auth_varname . " " . $element_name);
	if (!empty($auth_varname)) {

		if (isValidVarName($auth_varname)) {
			$user = eval("return " . $auth_varname . ";");
			$pattern = "(<ogc:Filter[^>]*>)(.*)(</ogc:Filter>)";
			$replacement = "\\1<And>\\2<ogc:PropertyIsEqualTo><ogc:PropertyName>" . 
				$ns . $element_name . "</ogc:PropertyName><ogc:Literal>" . $user . 
				"</ogc:Literal></ogc:PropertyIsEqualTo></And>\\3"; 
			$filter = eregi_replace($pattern, $replacement, $filter);
		}
	}
	return $filter;
}

$filter = checkAccessConstraint($filter, $db_wfs_conf_id);

$sql = "SELECT fkey_wfs_id FROM wfs_conf WHERE wfs_conf_id = $1";
$v = array($db_wfs_conf_id);
$t = array('i');
$res = db_prep_query($sql, $v, $t);
$row = db_fetch_array($res);
$wfsId = $row["fkey_wfs_id"];


$myWfsFactory = new UniversalWfsFactory();
$myWfs = $myWfsFactory->createFromDb($wfsId);
$data = $myWfs->getFeature($typename, $filter,$destSrs);

if ($data === null) die('{}');

$myWfsConf = WfsConfiguration::createFromDb($db_wfs_conf_id);
if (is_null($myWfsConf)) {
	die("{}");
}

$gmlFactory = new UniversalGmlFactory();
$myGml = $gmlFactory->createFromXml($data, $myWfsConf);

$geoJson = $myGml->toGeoJSON();

header('Content-type: text/html');
echo $geoJson;
?> 