<?php
# $Id: mod_print1.php 4274 2009-07-01 15:05:08Z christoph $
# http://www.mapbender.org/index.php/mod_print1.php
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
include(dirname(__FILE__)."/../../conf/print.conf");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Print</title>
</head>
<style type="text/css">
<!--

	select{
		width:180px;
	}
	.textField{
		width: 180px;
	}
	body{
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
-->
</style>
<?php
/******Name of this module**************/
$mb_module_id = "print1";
/**********************************/

$sql = "SELECT * from gui_element WHERE fkey_gui_id = '".Mapbender::session()->get("mb_user_gui")."' AND e_id = $1";
$v = array($mb_module_id);
$t = array('s');
$res = db_prep_query($sql, $v, $t);

echo "<script type='text/javascript'>";
#echo "var mb_user_resolution = ".Mapbender::session()->get("mb_user_resolution")." / 2.54;";
echo "var deformation = ".$deformation.";";
#echo "var DPC = 28.35 * deformation;";
echo "var DPC = 28.35;";
echo "var a4_width = " . $a4_width . " * DPC;";
echo "var a4_height = " . $a4_height . " * DPC;";
echo "var a3_width = " . $a3_width . " * DPC;";
echo "var a3_height = " . $a3_height . " * DPC;";


echo "var border_Portrait_a4_left = " . $border_Portrait_a4_left . " * DPC;";
echo "var border_Portrait_a4_top = " . $border_Portrait_a4_top . " * DPC;";
echo "var border_Portrait_a4_right = " . $border_Portrait_a4_right . " * DPC;";
echo "var border_Portrait_a4_bottom = " . $border_Portrait_a4_bottom . " * DPC;";
echo "var header_Portrait_a4_height = " . $header_Portrait_a4_height . " * DPC;";

echo "var border_Landscape_a4_left = " . $border_Landscape_a4_left . " * DPC;";
echo "var border_Landscape_a4_top = " . $border_Landscape_a4_top . " * DPC;";
echo "var border_Landscape_a4_right = " . $border_Landscape_a4_right . " * DPC;";
echo "var border_Landscape_a4_bottom = " . $border_Landscape_a4_bottom . " * DPC;";
echo "var header_Landscape_a4_height = " . $header_Landscape_a4_height . " * DPC;";

echo "var border_Portrait_a3_left = " . $border_Portrait_a3_left . " * DPC;";
echo "var border_Portrait_a3_top = " . $border_Portrait_a3_top . " * DPC;";
echo "var border_Portrait_a3_right = " . $border_Portrait_a3_right . " * DPC;";
echo "var border_Portrait_a3_bottom = " . $border_Portrait_a3_bottom . " * DPC;";
echo "var header_Portrait_a3_height = " . $header_Portrait_a3_height . " * DPC;";

echo "var border_Landscape_a3_left = " . $border_Landscape_a3_left . " * DPC;";
echo "var border_Landscape_a3_top = " . $border_Landscape_a3_top . " * DPC;";
echo "var border_Landscape_a3_right = " . $border_Landscape_a3_right . " * DPC;";
echo "var border_Landscape_a3_bottom = " . $border_Landscape_a3_bottom . " * DPC;";
echo "var header_Landscape_a3_height = " . $header_Landscape_a3_height . " * DPC;";

echo "var mod_print1_target = '".db_result($res,0,"e_target")."';";
echo "</script>";
?>
<script type="text/javascript">
<!--
/******PARAMS********************/
var mb_resolution = null;
var ind_size = null;
var ind_format = null;

/**END*PARAMS********************/

function validate(){
	//window.opener.mb_resolution = DPC;
	ind_size = document.form1.size.selectedIndex;
	ind_format = document.form1.format.selectedIndex;

	if(ind_size != 0 && ind_format != 0){
		var ind = window.opener.getMapObjIndexByName(mod_print1_target);
		var coord = window.opener.mb_mapObj[ind].extent.toString().split(",");
		var centerX = parseInt(coord[0]) + (parseInt(coord[2]) - parseInt(coord[0]))/2
		var centerY = parseInt(coord[1]) + (parseInt(coord[3]) - parseInt(coord[1]))/2
		if(document.form1.size.options[ind_size].value == "A4" && document.form1.format.options[ind_format].value == "portrait"){
			document.form1.page_width.value = a4_width;
			document.form1.page_height.value = a4_height;
			document.form1.printOffset_left.value = border_Portrait_a4_left;
			document.form1.printOffset_top.value = border_Portrait_a4_top;
			document.form1.map_width.value = Math.round(a4_width - border_Portrait_a4_left - border_Portrait_a4_right); 
			document.form1.map_height.value = Math.round(a4_height - border_Portrait_a4_top - border_Portrait_a4_bottom - header_Portrait_a4_height); 
			document.form1.header_height.value = header_Portrait_a4_height;
		}
		if(document.form1.size.options[ind_size].value == "A4" && document.form1.format.options[ind_format].value == "landscape"){
			document.form1.page_width.value = a4_height;
			document.form1.page_height.value = a4_width;
			document.form1.printOffset_left.value = border_Landscape_a4_left;
			document.form1.printOffset_top.value = border_Landscape_a4_top;
			document.form1.map_width.value = Math.round(a4_height - border_Landscape_a4_left - border_Landscape_a4_right); 
			document.form1.map_height.value = Math.round(a4_width - border_Landscape_a4_top - border_Landscape_a4_bottom - header_Landscape_a4_height); 
			document.form1.header_height.value = header_Landscape_a4_height;
		}
		if(document.form1.size.options[ind_size].value == "A3" && document.form1.format.options[ind_format].value == "portrait"){
			document.form1.page_width.value = a3_width;
			document.form1.page_height.value = a3_height;
			document.form1.printOffset_left.value = border_Portrait_a3_left;
			document.form1.printOffset_top.value = border_Portrait_a3_top;
			document.form1.map_width.value = Math.round(a3_width - border_Portrait_a3_left - border_Portrait_a3_right); 
			document.form1.map_height.value = Math.round(a3_height - border_Portrait_a3_top - border_Portrait_a3_bottom - header_Portrait_a3_height); 
			document.form1.header_height.value = header_Portrait_a3_height;
		}
		if(document.form1.size.options[ind_size].value == "A3" && document.form1.format.options[ind_format].value == "landscape"){
			document.form1.page_width.value = a3_height;
			document.form1.page_height.value = a3_width;
			document.form1.printOffset_left.value = border_Landscape_a3_left;
			document.form1.printOffset_top.value = border_Landscape_a3_top;
			document.form1.map_width.value = Math.round(a3_height - border_Landscape_a3_left - border_Landscape_a3_right); 
			document.form1.map_height.value = Math.round(a3_width - border_Landscape_a3_top - border_Landscape_a3_bottom - header_Landscape_a3_height); 
			document.form1.header_height.value = header_Landscape_a3_height;
		}            
		var pos = window.opener.makeClickPos2RealWorldPos(mod_print1_target, document.form1.map_width.value , document.form1.map_height.value );
		window.opener.mb_mapObj[ind].width = document.form1.map_width.value;
		window.opener.mb_mapObj[ind].height = document.form1.map_height.value;
		window.opener.document.getElementById(mod_print1_target).style.width = document.form1.map_width.value;
		window.opener.document.getElementById(mod_print1_target).style.height = document.form1.map_height.value;
		window.opener.window.frames[mod_print1_target].document.getElementById("maps").style.width = document.form1.map_width.value;
		window.opener.window.frames[mod_print1_target].document.getElementById("maps").style.height = document.form1.map_height.value;
      
		window.opener.mb_mapObj[ind].extent = new window.opener.Mapbender.Extent(
			coord[0],
			pos[1],
			pos[0],
			coord[3]
		);
		window.opener.setMapRequest(mod_print1_target);
	}
}
function refreshParams(){
	var ind = window.opener.getMapObjIndexByName(mod_print1_target);
	document.form1.map_url.value = "";
	var cnt_urls = 0;
	for(var i=0; i<window.opener.mb_mapObj[ind].wms.length; i++){
		if(window.opener.mb_mapObj[ind].wms[i].mapURL != false){
			if(cnt_urls > 0){
				document.form1.map_url.value += "###";
			}
			//hack for relativ WMS_ONLINERESOURCE
			if(window.opener.mb_mapObj[ind].wms[i].mapURL.charAt(0) == '/' && window.opener.mb_mapObj[ind].wms[i].mapURL.charAt(1) == 'c'){
				document.form1.map_url.value += 'http://localhost' + window.opener.mb_mapObj[ind].wms[i].mapURL;
			}
			else{
				document.form1.map_url.value += window.opener.mb_mapObj[ind].wms[i].mapURL;
			}
			cnt_urls++;
		}
	}
	document.form1.map_extent.value = window.opener.mb_mapObj[ind].extent.toString();
	document.form1.map_scale.value = window.opener.mb_getScale(mod_print1_target);
}
function printMap(){
	if(ind_size > 0 && ind_format >0){
		refreshParams();
		document.form1.submit();
		disablePrinting();
	}
	else{
		alert("Das Format ist noch nicht ausgew�hlt.");
	}   
		window.opener.mod_back_set();
}
function disablePrinting(){
   //window.opener.mb_resolution = mb_user_resolution;
   window.close();
}
// -->
</script>
<body onunload="disablePrinting()">
<form name='form1' method='POST' action='../php/mod_printView1.php?<?php echo SID; ?>' target="_blank">
<table border='0'>
<tr>
	<td>
	Format:<br />

	<select name='size' onchange='validate()'>
	<option value=''>Paper size...</option>
	<option value='A4'>A4</option>
	<!-- <option value='A3'>A3</option> -->
	</select>
	</td>   
</tr>
<tr>
	<td>
	<select name='format' onchange='validate()'>
	<option value=''>Orientation...(portrait,landscape)</option>
	<option value='portrait'>Portrait</option>
	<option value='landscape'>Landscape</option>
	</select>
	</td>
</tr>
<tr>
	<td>
	<br />Resolution:<br />
	<select name='quality'>
	<option value='1'>Standard</option>   
	<!-- <option value='<?php #echo $printFactor; ?>'>hoch</option> -->
	</select>
	</td>
</tr>
<tr>
	<td>
	<br />Titel: <br />
	<input type="text" class="textField" name="printTitle" value= "Preview">
	</td>
</tr>
<tr>
	<td>
	<br />Text: <br />
	<textarea name="printComment" rows="4" cols="20"></textarea>
	</td>
</tr>
<table>   
<input type='hidden' name='printOffset_left' value=''>
<input type='hidden' name='printOffset_top' value=''>
<input type='hidden' name='map_width' value=''>
<input type='hidden' name='map_height' value=''>
<input type='hidden' name='page_width' value=''>
<input type='hidden' name='page_height' value=''>
<input type='hidden' name='header_height' value=''>
<input type='hidden' name='map_url' value=''>
<input type='hidden' name='map_extent' value=''>
<input type='hidden' name='map_scale' value=''>
<!-- <input type='hidden' name='footer' value='<?php echo $footer ?>'> -->
<input type='button' name='print' value="Preview" onclick='printMap()'>
</form>
</body>
</html>
