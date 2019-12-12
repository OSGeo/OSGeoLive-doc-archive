<?php
# $Id: mb_listWMCs.php 1686 2007-09-26 09:05:01Z christoph $
# http://www.mapbender.org/index.php/mb_listWMCs.php
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
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__) . "/../classes/class_administration.php");
require_once(dirname(__FILE__) . "/../classes/class_connector.php");
require_once(dirname(__FILE__) . "/../classes/class_kml_ows.php");

$gui_id = Mapbender::session()->get("mb_user_gui");
$user_id = Mapbender::session()->get("mb_user_id");

$action = $_GET["action"];
$kmlId = $_GET["kml_id"];

$delKmlId = $_POST["del_kml_id"];
$clientFilename = $_FILES["local_kml_filename"]["tmp_name"];
$kmlUrl = $_POST["local_kml_url"];

$form_target = $self;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="cache-control" content="no-cache">
		<meta http-equiv="pragma" content="no-cache">
		<meta http-equiv="expires" content="0">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">	
		<title>Load KML</title>
	</head>
	<body>
		<form name='delete_kml' action='<?php echo $form_target; ?>' method='POST'>
			<input type='hidden' id='delete_kml' name='del_kml_id' value ='' >
		</form>

<?php
function mb_listKMLs($kmlIdArray, $form_target){
	$display = "<h2 style='font-family: Arial, Helvetica, sans-serif; color: #808080;background-color: White;'><font align='left' color='#000000'>load KML from list</font></h2>";
	$display .= "<table width='90%' style='font-family: Arial, Helvetica, sans-serif;font-size : 12px;color: #808080;' border='1' cellpadding='3' rules='rows'><tr style='background-color:#F0F0F0;' width='80px'><td ><b>KML name</b></td><td><b>last update</b></td><td colspan=5></td></tr>";

	if (count($kmlIdArray) > 0) {
		$v = array();
		$t = array();

		$kmlIdList = "";
		for ($i = 0; $i < count($kmlIdArray); $i++){
			if ($i > 0){ 
				$kmlIdList .= ",";
			}
			$kmlIdList .= "$".($i+1);
			array_push($v, $kmlIdArray[$i]);
			array_push($t, 's');
		}
		$sql_list_kmls = "SELECT DISTINCT kml_id, kml_title, kml_timestamp FROM mb_user_kml ";
		$sql_list_kmls .= "WHERE kml_id IN (" . $kmlIdList . ") ";
		$sql_list_kmls .= "ORDER BY kml_timestamp DESC";
		
		$res_list_kmls = db_prep_query($sql_list_kmls, $v, $t);
		while($row = db_fetch_array($res_list_kmls)){
			$this_id = $row["kml_id"];
			$this_title = $row["kml_title"];
			$this_timestamp = date("M d Y H:i:s", $row["kml_timestamp"]); 

			$display .= "<tr onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			$display .= "<td>".$this_title."</td>";
			$display .= "<td>".$this_timestamp. "</td>";
			$display .= "<td><a href=\"" . $form_target . "&action=load&kml_id=".$this_id."\"><img src=\"../img/button_gray/kml_load.png\" title=\"load this KML\"  border=0></a></td>";
			$display .= "<td><a href=\"" . $form_target . "&action=merge&kml_id=".$this_id."\"><img src=\"../img/button_gray/kml_merge.png\" title=\"merge KML\"  border=0></a></td>";
			$display .= "<td><a href=\"" . $form_target . "&action=append&kml_id=".$this_id."\"><img src=\"../img/button_gray/kml_append.png\" title=\"append KML\"  border=0></a></td>";
			$display .= "<td><a href='../javascripts/mod_displayKml.php?kml_id=".$this_id."' target = '_blank'><img src=\"../img/button_gray/kml_xml.png\" title=\"display KML XML\"  border=0></a></td>";
			$display .= "<td><a href=\"" . $form_target . "&action=delete&kml_id=".$this_id."\"><img src=\"../img/button_gray/del.png\" title=\"delete this KML\"  border=0></a></td>";
			$display .= "</tr>";
		}
	}
	else{
		$display .= "<tr><td>There are no KMLs availiable</td></tr>";
	}	
	$display .= "</table>";
	   
	return $display;
}

function getTarget($gui_id) {
	$sql = "SELECT e_requires, e_target FROM gui_element WHERE e_id = 'loadkml' AND fkey_gui_id = $1";
	$v = array($gui_id);
	$t = array("s");
	$res = db_prep_query($sql, $v, $t);
	$cnt = 0;
	while($row = db_fetch_array($res)){ 
		$e_target = $row["e_target"];
		$e_require = $row["e_requires"];
		$cnt++;
	}
	if ($cnt > 1) { 
		$e = new mb_exception("listKMLs: e_id 'loadkml' not unique in GUI '" . $gui_id . "'!");
	}

	$targetArray = explode(",", $e_target);
	if (in_array('mapframe1', $targetArray)) {
		return 'mapframe1';
	}
	else {
		return trim($targetArray[0]);
	}
}

function loadFile($filename) {
	$handle = fopen($filename, "r");
	$cnt = 0;
	while (!feof($handle)) {
    	$buffer .= fgets($handle, 4096);
	}
	fclose ($handle);
	return $buffer;
}

$admin = new administration();
//$kmlIdArray = $admin->getKmlByOwner($user_id);

// kml is being deleted
if (!empty($delKmlId)) {
	$result = $admin->deleteKml($delKmlId, $user_id);
	if (!$result) {
		echo "<script language='javascript'>";
		echo "alert('KML could not be deleted!');";
		echo "</script>";
	}
}
// kml is being loaded from file
elseif ($clientFilename) {
	$serverFilename = "../tmp/kml" . time() . ".xml";
	copy($clientFilename, $serverFilename);
	
	$kmlDoc = loadFile($serverFilename);
	$kmlObj = new KML();
	if ($kmlObj->parseKml($kmlDoc)) {
		$geoJSON = $kmlObj->toGeoJSON();
		setGeoJson($geoJSON);
	}
	else {
		echo "<script language='javascript'>";
		echo "alert('KML load failed. See the error log for details.');";
		echo "</script>";
	}
}
// load KML from URL
elseif ($kmlUrl) {
	$connector = new connector($kmlUrl);
	$kmlDoc = $connector->file;
	$kmlObj = new KML();
	if ($kmlObj->parseKml($kmlDoc)) {
		$geoJSON = $kmlObj->toGeoJSON();
		setGeoJson($geoJSON);
	}
	else {
		echo "<script language='javascript'>";
		echo "alert('KML load failed. See the error log for details.');";
		echo "</script>";
	}
}

function setGeoJson ($geoJSON) {
	echo "<script language='javascript'>";
	if ($geoJSON) {
		echo "var geoJSON = " . $geoJSON . ";"; 
		echo "window.opener.kmlHasLoaded.trigger(geoJSON);";
		echo "alert('KML loaded succesfully.');";
	}
	else {
		echo "alert('Loading KML failed.');";
	}
	echo "window.close();";
	echo "</script>";
}

// load a KML from file
?>
<h2 style='font-family: Arial, Helvetica, sans-serif; color: #808080;background-color: White;'><font align='left' color='#000000'>load KML from file</font></h2>
<form enctype="multipart/form-data" action="<?php echo $form_target;?>" method=POST target="_self"> 
<input type='file' name='local_kml_filename'>
<input id='kml_load_from_file' type='submit' value='load'>
</form>

<?php
// load a KML from URL
?>
<h2 style='font-family: Arial, Helvetica, sans-serif; color: #808080;background-color: White;'><font align='left' color='#000000'>load KML from URL</font></h2>
<form action="<?php echo $form_target;?>" method=POST target="_self"> 
<input type='text' name='local_kml_url' value='http://code.google.com/apis/kml/documentation/KML_Samples.kml' maxlength=512 size=50>

<br/><br/>Choose URL below or enter above
<select name='local_kml_url_select' onChange='local_kml_url.value=this.value'>
<option value='http://code.google.com/apis/kml/documentation/KML_Samples.kml'>Google KML Sample</option>
<option value='http://geo.openplans.org:8080/geoserver/wms?bbox=-128,23,-64,51&Format=application/vnd.google-earth.kml+xml&request=GetMap&&width=600&height=317&srs=EPSG:4326&sld=http://artois.openplans.org/slds/population.sld'>TOPP OWS-5 Demo Service KML 2.2 (states US)</option>
<option value='http://geo.openplans.org:8080/geoserver/wms?bbox=-128,23,-64,51&Format=application/kml+xml&request=GetMap&&width=600&height=317&srs=EPSG:4326&sld=http://artois.openplans.org/slds/population.sld&layers=topp:states&format_options=extendedData:true;style:false'>TOPP OWS-5 Demo Service KML OWS-5 (states US)</option>
<option value='http://wight.demos.galdosinc.com/fps/wms/http?request=GetMap&version=1.1.1&layers=DepthArea&styles=DepthArea&service=WMS&srs=urn:ogc:def:crs:ogc:1.3:CRS84&BBOX=-1.62,50.55,-1.02,50.9&format=kml&WIDTH=500&HEIGHT=500'>Galdos OWS-5 Demo Service</option>
<option value='http://geo.openplans.org:8080/geoserver/wms?bbox=-180,-90,180,90&Format=application/kml+xml&request=GetMap&&width=600&height=317&srs=EPSG:4326&sld=http://artois.openplans.org/slds/population.sld&layers=topp:tasmania_roads,topp:tasmania_state_boundaries&format_options=extendedData:true;style:false'>TOPP OWS-5 Demo Service KML OWS-5 (Tasmania)</option>
</select>
<input id='kml_load_from_url' type='submit' value='load'>
</form>
<?php
/*
// load a KML from list
echo mb_listKMLs($kmlIdArray, $form_target);
		
if ($kmlId && in_array($kmlId, $kmlIdArray)){
	if ($action == "delete") {
		echo "<script language='javascript'>";
		echo "value = confirm('Do you really want to delete this document?');";
		echo "if (value == true) {";
		echo "document.delete_kml.del_kml_id.value = '" . $kmlId . "';";
		echo "document.delete_kml.submit();";
		echo "}";
		echo "</script>";
	}
	else if ($action == "append" || $action == "merge" || $action == "load") {
		$mytarget = getTarget($gui_id);

		$kml = new kml();
		$kml->createObjFromKML_id($kmlId);
		$js = $kml->createJsObjFromKML("window.opener.", $mytarget, $action);

		echo "<script language='javascript'>";
		echo $js;
		if ($kml->getTitle()) {
			$title = "'" . $kml->getTitle() . "' ";
		}
		echo "alert(\"KML " . $title . ": " . $action . " successful.\");\n";
		echo "window.close();";
		echo "</script>";
	}
}
*/
?>
</body>
</html>
