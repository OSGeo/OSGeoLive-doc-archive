<?php
# $Id: mod_addWMSfromfilteredList.php 830 2006-11-20 13:39:10Z christoph $
# http://www.mapbender.org/index.php/mod_addWMSfromfilteredList.php
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
require_once(dirname(__FILE__)."/../classes/class_wms.php"); 
require_once(dirname(__FILE__)."/../classes/class_administration.php"); 
require_once(dirname(__FILE__)."/../classes/class_json.php");


$userId = Mapbender::session()->get("mb_user_id");
$command = $_GET["command"];
$guiId = $_GET["guiId"];
$groupId = $_GET["groupId"];

$admin = new administration();
$guiIdArray = $admin->getGuisByPermission($userId, false);

$resultObj = array();

if ($command == "getGroups") {
	$resultObj["group"] = array();
	
	$sql = "SELECT mb_group_id, mb_group_name, gettext($1, mb_group_description) as mb_group_description ";
	$sql .= "FROM mb_group, mb_user_mb_group ";
	$sql .= "WHERE fkey_mb_group_id = mb_group_id AND fkey_mb_user_id = $2 ";
	$sql .= "ORDER BY mb_group_name";	
	$v = array(Mapbender::session()->get("mb_lang"), $userId);
	$t = array("s", "i");
	$res = db_prep_query($sql, $v, $t);
			
	while($row = db_fetch_array($res)){
		$resultArray = array("description" => $row["mb_group_description"], "name" => $row["mb_group_name"], "id" => $row["mb_group_id"]);
		array_push($resultObj["group"], $resultArray);
	}
}
else if ($command == "getGUIs") {

	$resultObj["gui"] = array();
	
	$sql = "SELECT gui_id, gui_name, gettext($1, gui_description) as gui_description FROM gui WHERE gui_id IN (";
	$v = array(Mapbender::session()->get("mb_lang"));

	$t = array("s");
	
	for ($i = 0; $i < count($guiIdArray); $i++) {
		if ($i > 0) { 
			$sql .= ",";
		}
		$sql .= "$" . strval($i + 2);
		array_push($v, $guiIdArray[$i]);
		array_push($t, "s");
	}
	$sql .= ") ORDER BY gui_name";
	
	$res = db_prep_query($sql,$v,$t);
	
	while ($row = db_fetch_array($res)) {
		$resultArray = array("description" => $row["gui_description"], "name" => $row["gui_name"], "id" => $row["gui_id"]);
		array_push($resultObj["gui"], $resultArray);
	}
}
else if ($command == "getAllWMS") {
	$resultObj["wms"] = array();
					 
	$sql = "SELECT DISTINCT wms.wms_id, wms.wms_title, gettext($1, wms.wms_abstract) as wms_abstract, wms.wms_getcapabilities, wms.wms_version ";
	$sql .= "FROM wms, gui_wms ";
	$sql .= "WHERE wms.wms_id = gui_wms.fkey_wms_id AND gui_wms.fkey_gui_id IN (";
	$v = array(Mapbender::session()->get("mb_lang"));
	$t = array("s");
	
	for ($i = 0; $i < count($guiIdArray); $i++) {
		if ($i > 0) {
			$sql .= ",";
		}
		$sql .= "$" . strval($i + 2);
		array_push($v, $guiIdArray[$i]);
		array_push($t, "s");
	}
	$sql .= ") ORDER BY wms_title";
	
	$res = db_prep_query($sql,$v,$t);
	
	while ($row = db_fetch_array($res)) {
		$owsproxy = $admin->getWMSOWSstring($row["wms_id"]);
		if ($owsproxy && $owsproxy != "") {
			$owsproxyUrl = OWSPROXY."/".session_id()."/".$owsproxy."?";
			$wmsUrl = $owsproxyUrl;
		}
		else {
			$wmsUrl = $row["wms_getcapabilities"];
		}
		
		$resultArray = array("id" => $row["wms_id"], "title" => $row["wms_title"], "abstract" => $row["wms_abstract"], "getCapabilitiesUrl" => $wmsUrl, "version" => $row["wms_version"]);
		array_push($resultObj["wms"], $resultArray);
	}							 
}
else if ($command == "getWMSByGroup") {
	$resultObj["wms"] = array();
	
	$sql = "SELECT DISTINCT wms_id, wms_title, gettext($1, wms_abstract) as wms_abstract, wms_getcapabilities, wms_version ";
	$sql .= "FROM wms, gui_wms, gui_mb_group ";
	$sql .= "WHERE wms.wms_id = gui_wms.fkey_wms_id AND gui_wms.fkey_gui_id = gui_mb_group.fkey_gui_id AND gui_mb_group.fkey_mb_group_id = $2";
	$v = array(Mapbender::session()->get("mb_lang"), $groupId);
	$t = array("s", "i");
	$res = db_prep_query($sql, $v, $t);
	
	while ($row = db_fetch_array($res)) {
		$owsproxy = $admin->getWMSOWSstring($row["wms_id"]);
		if ($owsproxy && $owsproxy != "") {
			$owsproxyUrl = OWSPROXY."/".session_id()."/".$owsproxy."?";
			$wmsUrl = $owsproxyUrl;
		}
		else {
			$wmsUrl = $row["wms_getcapabilities"];
		}
		
		$resultArray = array("id" => $row["wms_id"], "title" => $row["wms_title"], "abstract" => $row["wms_abstract"], "getCapabilitiesUrl" => $wmsUrl, "version" => $row["wms_version"]);
		array_push($resultObj["wms"], $resultArray);
	}		
}
else if ($command == "getWMSByGUI") {
	$resultObj["wms"] = array();

	$sql = "SELECT DISTINCT wms_id, wms_title, gettext($1, wms_abstract) as wms_abstract, wms_getcapabilities, wms_version ";
	$sql .= "FROM wms, gui_wms WHERE wms.wms_id = gui_wms.fkey_wms_id AND fkey_gui_id = $2";
	$v = array(Mapbender::session()->get("mb_lang"), $guiId);
	$t = array("s", "s");
	$res = db_prep_query($sql, $v, $t);
	
	while ($row = db_fetch_array($res)) {
		$owsproxy = $admin->getWMSOWSstring($row["wms_id"]);
		if ($owsproxy && $owsproxy != "") {
			$owsproxyUrl = OWSPROXY."/".session_id()."/".$owsproxy."?";
			$wmsUrl = $owsproxyUrl;
		}
		else {
			$wmsUrl = $row["wms_getcapabilities"];
		}
		
		$resultArray = array(
			"id" => $row["wms_id"], 
			"title" => $row["wms_title"], 
			"abstract" => $row["wms_abstract"], 
			"getCapabilitiesUrl" => $wmsUrl, 
			"version" => $row["wms_version"]
		);
		array_push($resultObj["wms"], $resultArray);
	}		
}

$json = new Mapbender_JSON();
$output = $json->save_encode($resultObj);
echo $output;
?>
