<?php
# $Id: mod_wfsGazetteerEditor_server.php 1376 2007-11-09 14:34:37Z baudson $
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
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
require_once(dirname(__FILE__)."/../classes/class_gml2.php");
require_once(dirname(__FILE__)."/../extensions/JSON.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_wfs_conf.php");
require_once(dirname(__FILE__)."/../classes/class_connector.php");

$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db($DB,$con);

$command = $_REQUEST["command"];
$wfsFeatureTypeName = $_REQUEST["wfsFeatureTypeName"];
#$wfsDescribeFeatureType = $_REQUEST["wfsDescribeFeatureType"];
$wfsGetFeatureAttr = $_REQUEST["wfsGetFeatureAttr"];
$wfsGetFeature = $_REQUEST["wfsGetFeature"];
$wfsFilter = $_REQUEST["wfsFilter"];
$exportToShape = $_REQUEST["exportToShape"] == "true" ? true : false;

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
				$e = new mb_exception("mod_wfsGazetteerEditor_server: checkAccessConstraint: invalid value of variable containing user information!");
			}
		}
		else {
			$e = new mb_exception("mod_wfsGazetteerEditor_server: checkAccessConstraint: var name is not valid! (" . $auth_varname . ")");
		}
	}
	return $filter;
}


if ($command == "getWfsConf") {
	
	$wfsConfIdString = $_GET["wfsConfIdString"];
	
	if ($wfsConfIdString != "") {
		//array_keys(array_flip()) produces an array with unique entries
		$wfsConfIdArray = array_keys(array_flip(mb_split(",", $wfsConfIdString)));
	}
	else {
		echo "{}";
		die();
	}
	
	$obj = new WfsConf();
	$obj->load($wfsConfIdArray);
	$json = new Services_JSON();
	$output = $json->encode($obj->confArray);
	echo $output;
}
else if ($command == "getSearchResults") {
	$wfs_conf_id = $_REQUEST["wfs_conf_id"];
	$backlink = $_REQUEST["backlink"];
	$frame = $_REQUEST["frame"];
	$filter = $_REQUEST["filter"];
	$url = $_REQUEST["url"];

	/* wfs_conf */
	$sql = "SELECT * FROM wfs_conf JOIN wfs ON wfs_conf.fkey_wfs_id = wfs.wfs_id ";
	$sql .= "WHERE wfs_conf.wfs_conf_id = $1";
	$v = array($wfs_conf_id);
	$t = array('i');
	
	$res = db_prep_query($sql,$v,$t);
	if ($row = db_fetch_array($res)) {
		$g_res_style  = $row["g_res_style"];
	}
	else {
		die("wfs_conf " . $wfs_conf_id . "data not available");
	}
	
	/* wfs_conf_element */
	$sql = "SELECT * FROM wfs_conf_element ";
	$sql .= "JOIN wfs_element ON wfs_conf_element.f_id = wfs_element.element_id ";
	$sql .= "WHERE wfs_conf_element.fkey_wfs_conf_id = $1 ";
	$sql .= "AND wfs_conf_element.f_show = 1 ORDER BY wfs_conf_element.f_respos;";
	$v = array($wfs_conf_id);
	$t = array('i');
	
	#echo $sql;
	$res = db_prep_query($sql,$v,$t);
	$col = array();
	while ($row = db_fetch_array($res)) {
		array_push($col, $row["element_name"]);
	}
	if (count($col) == 0) {
		die("wfs_conf_element data not available");
	}
	
	// append authorisation condition to filter
	$filter = checkAccessConstraint($filter, $wfs_conf_id);
	
	$admin = new administration();
	
	$req = urldecode($url).urlencode($admin->char_decode(stripslashes($filter)));
	$mygml = new gml2();
	$mygml->parseFile($req);

	if (!empty($exportToShape)) {
		$filenamePrefix = md5(microtime());
		$mygml->toShape($filenamePrefix);
		header("Content-Type: application/json; charset=utf-8");
		echo '{"filename": "' . $filenamePrefix . '.zip"}';
		
	}
	else {
		header("Content-Type: application/json; charset=utf-8");
		echo $mygml->toGeoJSON();
	}
}
else if($command == "getFeature"){

	$admin = new administration();
	$wfsGetFeature=$admin->checkURL($wfsGetFeature);

	$url = $wfsGetFeature."REQUEST=getFeature&VERSION=1.0.0&SERVICE=WFS&MAXFEATURES=20".
		   "&typename=".$wfsFeatureTypeName; //."&propertyname=".$wfsGetFeatureAttr;
		   //"&filter=<ogc:Filter><ogc:Not><ogc:PropertyIsNull><ogc:PropertyName>".$wfsGetFeatureAttr."</ogc:PropertyName></ogc:PropertyIsNull></ogc:Not></ogc:Filter>";
	
	#echo $url;
	$e = new mb_exception("URL: " . $url);
	$x = new connector($url);
	$data = $x->file;
	//$data = file_get_contents($url);
	$parser = xml_parser_create();
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1);
	xml_parser_set_option($parser,XML_OPTION_TARGET_ENCODING,"UTF-8");
	xml_parse_into_struct($parser,$data,$values,$tags);
	
	$code = xml_get_error_code($parser);
	xml_parser_free($parser);
	
	if ($code) {
		$line = xml_get_current_line_number($parser); 
		$mb_exception = new mb_exception(xml_error_string($code) .  " in line " . $line);
	}
	
	
	function sepNameSpace($s){
		$c = mb_strpos($s,":"); 
		if($c>0){
			return mb_substr($s,$c+1);
		}
		else{
			return $s;
		}		
	}
	
	$featureNameToUpper = mb_strtoupper($wfsGetFeatureAttr);
	$featureValuesArray = array();
	foreach ($values as $element) {
		if(mb_strtoupper($element[tag]) == $featureNameToUpper || sepNameSpace(mb_strtoupper($element[tag])) == $featureNameToUpper){
			array_push($featureValuesArray, $element[value]);
		}
	}
	
//	$featureValues = join("|", $featureValuesArray);	
	$json = new Services_JSON();
	echo $json->encode($featureValuesArray);	
	
// the way below only works with propertyname in REQUEST-Url	
//	$sxe = new SimpleXMLElement($data);
//	
//	list($ns, $ns_url) = explode(":", $wfsFeatureTypeName);
//	
//	$sxe->registerXPathNamespace($ns, 'http://namespace_url_default');
//	
//	$m = array();
//	$m['columnValues'] = $sxe->xpath('//'.$ns.':'.$wfsGetFeatureAttr);
//	 
//	$columnValues = array();
//	$i=0;
//	foreach ($m['columnValues'] as $columnValue) {
//		
//		$domNode = dom_import_simplexml($columnValue);    
//	    $columnValues[$i]=$domNode->nodeValue;
//	    
//	    $i++;
//	    
//	    $json = new Services_Json();
//		$jsonEncodedValue = $json->encode($columnValues);	
//	}
	
//	echo $jsonEncodedValue;
}
else {
	echo "please enter a valid command.";
}
?>
