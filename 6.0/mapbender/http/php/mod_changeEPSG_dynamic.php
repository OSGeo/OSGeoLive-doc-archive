<?php
# $Id: mod_changeEPSG_dynamic.php 7115 2010-11-11 12:33:30Z apour $
# http://www.mapbender.org/index.php/mod_changeEPSG_dynamic.php
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

require_once(dirname(__FILE__)."/mb_validateSession.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="author-mail" content="info@ccgis.de">
<meta name="author" content="U. Rothstein, T. Wirkus">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>changeEPSG_dynamic</title>
<style type="text/css">
<!--

.epsg{
	width:100px;
	border: solid;
}
-->
</style>
<?php
$gui_id = Mapbender::session()->get("mb_user_gui");
$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db(DB,$con);
$sql = "SELECT e_target FROM gui_element WHERE e_id = 'changeEPSG' AND fkey_gui_id = $1";
$v = array($gui_id);
$t = array('s');
$res = db_prep_query($sql,$v,$t);
$cnt_gui_wms = 0;
$cnt_epsg_wms = 0;
$cnt_layer_wms = 0;

/*get allocated wms from allocated gui  ***********************************************************/
$sql_gui_wms = "SELECT fkey_wms_id FROM gui_wms WHERE fkey_gui_id = $1 ORDER BY fkey_wms_id";
$v = array($gui_id);
$t = array('s');
$res_gui_wms = db_prep_query($sql_gui_wms,$v,$t);
while(db_fetch_row($res_gui_wms)){
	$fkey_gui_id[$cnt_gui_wms] = db_result($res_gui_wms,$cnt_gui_wms,"fkey_gui_id");
	$fkey_wms_id_1[$cnt_gui_wms] = db_result($res_gui_wms,$cnt_gui_wms,"fkey_wms_id");
	$cnt_gui_wms++;
}					 
/*get allocated wms from allocated gui  ***********************************************************/
/*get allocated layer_id from allocated gui  ******************************************************/
$v = array();
$t = array();
$sql_layer_wms = "SELECT  layer_id FROM layer WHERE fkey_wms_id IN (";
for($i=0; $i<count($fkey_wms_id_1); $i++){
	if($i>0){ $sql_layer_wms .= ",";}
	$sql_layer_wms .= "$".($i+1);
	array_push($v,$fkey_wms_id_1[$i]);
	array_push($t,'i');
}
$sql_layer_wms.= ") ORDER BY layer_id";

$res_layer_wms = db_prep_query($sql_layer_wms,$v,$t);
while($row = db_fetch_array($res_layer_wms)){
	$layer_id[$cnt_layer_wms] = $row["layer_id"];
	$fkey_wms_id[$cnt_layer_wms] = $row["fkey_wms_id"];
	$cnt_layer_wms++;
}
/*get allocated wms from allocated gui  ***********************************************************/

/*get allocated epsg-code from allocated wms  *****************************************************/

$v = array();
$t = array();
$sql_epsg_wms = "SELECT DISTINCT wms_srs FROM wms_srs WHERE fkey_wms_id IN (";
for($i=0; $i<count($fkey_wms_id_1); $i++){
	if($i>0){ $sql_epsg_wms .= ",";}
	$sql_epsg_wms .= "$".($i+1);
	array_push($v,$fkey_wms_id_1[$i]);
	array_push($t,'i');
}
$sql_epsg_wms.= ") ORDER BY wms_srs";

$res_epsg_wms = db_prep_query($sql_epsg_wms,$v,$t);
while($row = db_fetch_array($res_epsg_wms)){
	$fkey_wms_id_2[$cnt_epsg_wms] = $row["fkey_wms_id"];
	$epsg_code[$cnt_epsg_wms] = $row["wms_srs"];
	$cnt_epsg_wms++;  //possible error because increment before echo statement
	echo"$epsg_code[$cnt_epsg_wms]";
}		

echo "<script type='text/javascript'>";
echo "var myTarget = '".db_result($res,0,"e_target")."';";
echo "</script>";
# transform coordinates
if(isset($_REQUEST["srs"])){
	require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
	$con = pg_connect ($con_string) or die ("Error while connecting database DBname");
	
	$arraymapObj = mb_split("###", $_REQUEST["srs"]);
	
	echo "<script type='text/javascript'>";
	echo "var newExtent = new Array();";
	
	for($i=0; $i < count($arraymapObj); $i++){
		$temp = mb_split(",",$arraymapObj[$i]);
		/*
		 * @security_patch sqli done
		 */
		$sqlMinx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($temp[2])." ".pg_escape_string($temp[3]).")',".pg_escape_string(preg_replace("/EPSG:/","",$temp[1]))."),".pg_escape_string(preg_replace("/EPSG:/","",$_REQUEST["newSRS"])).")) as minx";
		$resMinx = @pg_query($con,$sqlMinx);
		$minx = pg_result($resMinx,0,"minx");
		
		$sqlMiny = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($temp[2])." ".pg_escape_string($temp[3]).")',".pg_escape_string(preg_replace("/EPSG:/","",$temp[1]))."),".pg_escape_string(preg_replace("/EPSG:/","",$_REQUEST["newSRS"])).")) as miny";
		$resMiny = @pg_query($con,$sqlMiny);
		$miny = pg_result($resMiny,0,"miny");
		
		$sqlMaxx = "SELECT X(transform(GeometryFromText('POINT(".pg_escape_string($temp[4])." ".pg_escape_string($temp[5]).")',".pg_escape_string(preg_replace("/EPSG:/","",$temp[1]))."),".pg_escape_string(preg_replace("/EPSG:/","",$_REQUEST["newSRS"])).")) as maxx";
		$resMaxx = @pg_query($con,$sqlMaxx);
		$maxx = pg_result($resMaxx,0,"maxx");
		
		$sqlMaxy = "SELECT Y(transform(GeometryFromText('POINT(".pg_escape_string($temp[4])." ".pg_escape_string($temp[5]).")',".pg_escape_string(preg_replace("/EPSG:/","",$temp[1]))."),".pg_escape_string(preg_replace("/EPSG:/","",$_REQUEST["newSRS"])).")) as maxy";
		$resMaxy = @pg_query($con,$sqlMaxy);		 
		$maxy = pg_result($resMaxy,0,"maxy");
	
		$extenty = $maxy - $miny;
		$extentx = $maxx - $minx;
		$relation_px_x = $temp[6] / $temp[7];
		$relation_px_y = $temp[7] / $temp[6];
		$relation_bbox_x = $extentx / $extenty;
		
		if($relation_bbox_x <= $relation_px_x){
			$centerx = $minx + ($extentx/2);
			$minx = $centerx - $relation_px_x * $extenty / 2;
			$maxx = $centerx + $relation_px_x * $extenty / 2;
		}
		if($relation_bbox_x > $relation_px_x){
			$centery = $miny + ($extenty/2);
			$miny = $centery - $relation_px_y * $extentx / 2;
			$maxy = $centery + $relation_px_y * $extentx / 2;
		}
		echo "newExtent[".$i."] = '".$temp[0].",".$_REQUEST["newSRS"].",".$minx.",".$miny.",".$maxx.",".$maxy."';";
		
	}
	echo "</script>";
}
else{
	echo "<script type='text/javascript'>var newExtent = false;</script>";
}

?>
<script type='text/javascript'>
<!--
if(newExtent == false){
 parent.mb_registerSubFunctions("window.frames['changeEPSG_dynamic'].mod_changeEPSG_setBox()");
}
function mod_changeEPSG_init(){
//frameName, EPSG, minx, miny, maxx, maxy, width, height
	var exists = false;
	if(newExtent){
		for(var i=0; i<newExtent.length; i++){
			var temp = newExtent[i].split(",");
			if(temp[0] == myTarget){
				for(var ii=0; ii<parent.mb_MapHistoryObj[temp[0]].length; ii++){
					if(parent.mb_MapHistoryObj[temp[0]][ii].epsg == temp[1]){
						exists = ii;
						var goback = true;
					}
				}
				var ind = parent.getMapObjIndexByName(temp[0]);
				if(goback){
					parent.mb_mapObj[ind].epsg = temp[1];
					parent.mb_mapObj[ind].extent = parent.mb_MapHistoryObj[temp[0]][exists].extent;
					parent.setMapRequest(temp[0]);
				}
				else{
					parent.mb_mapObj[ind].epsg = temp[1];
					parent.mb_mapObj[ind].extent = new parent.Mapbender.Extent(
						parseFloat(temp[2]),
						parseFloat(temp[3]),
						parseFloat(temp[4]),
						parseFloat(temp[5])
					);
					parent.setMapRequest(temp[0]);
				}
			}
			if(temp[0] != myTarget){
				var ind = parent.getMapObjIndexByName(temp[0]);
				parent.mb_mapObj[ind].epsg = temp[1];
				parent.mb_mapObj[ind].extent = new parent.Mapbender.Extent(
					parseFloat(temp[2]),
					parseFloat(temp[3]),
					parseFloat(temp[4]),
					parseFloat(temp[5])
				);
				parent.setMapRequest(temp[0]);
			}
		}
	}
}
function mod_changeEPSG_setBox(){
	var myEPSG = parent.mb_mapObj[0].epsg;
	for(var i=0; i<document.forms[0].epsg.length; i++){
		if(document.forms[0].epsg.options[i].value == myEPSG){
			document.forms[0].epsg.selectedIndex = i;
			isEPSG = true;
		}
	}
}

function mod_changeEPSG(){
	str_srs = "";
	for(var i=0; i<parent.mb_mapObj.length; i++){
	if(i>0){str_srs += "###";}
		str_srs += parent.mb_mapObj[i].frameName + "," + parent.mb_mapObj[i].epsg + "," + parent.mb_mapObj[i].extent.toString() + ","+parent.mb_mapObj[i].width+","+parent.mb_mapObj[i].height;
	}
	document.forms[0].srs.value = str_srs;
	var ind = document.forms[0].epsg.selectedIndex;
	document.forms[0].newSRS.value = document.forms[0].epsg.options[ind].value;
	document.forms[0].submit();
}
// -->
</script>
</head>
<body leftmargin="1" topmargin="1" onload="mod_changeEPSG_init()" bgcolor="#0066cc">

<?php
/*insert EPSG into selectbox************************************************************************************/
echo "<form action='" . $self ."' method='post'>";
echo "<select  class='epsg' name='epsg' onChange='mod_changeEPSG()'>";
for($i=0; $i<$cnt_epsg_wms; $i++){
	echo "<option value='" . $epsg_code[$i] . "' ";
	if($epsg && $epsg == $epsg_code[$i]){
	}
	echo ">" . $epsg_code[$i]  . "</option>";
}
echo "</select>";
/*insert EPSG in selectbox************************************************************************************/
echo"<input type='hidden' name='srs' value=''>";
echo"<input type='hidden' name='newSRS' value=''>";
echo"</form>"; 
?>
</body>
</html>