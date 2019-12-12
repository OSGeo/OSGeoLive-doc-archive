<?php
# $Id: mod_WMSpreferences.php 4206 2009-06-25 10:34:39Z vera $
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
<title>WMS Preferences</title>
<?php
include '../include/dyn_css.php';
?>
<STYLE TYPE="text/css">
		<!--
   body{
   	font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;
   	color:#0066cc;
      font-size:10pt
   }    
   
   table{
   	font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;
   	color:#808080;;
      font-size:9pt
   } 
   
   th{
   	background-color:#F0F0F0;
   }  
   
   select{
		width:240px;
   }    
   
	-->
</STYLE>
<?php
echo '<script type="text/javascript">';
$sql = "SELECT * FROM gui_element WHERE e_id = 'WMS_preferences' AND fkey_gui_id = $1";
$v = array(Mapbender::session()->get("mb_user_gui"));
$t = array("s");
$res = db_prep_query($sql, $v, $t);
$cnt = 0;
$vis = "";
$wmsid = "";

while($row = db_fetch_array($res)){
   $e_target = $row["e_target"];
   $cnt++;
}
if($cnt > 1){ echo "alert('WMS_preferences: ID not unique!');";}
$e_target = explode(",", $e_target);
echo "var mod_WMSpreferences_target1 = '".trim($e_target[0])."';";
echo "var mod_WMSpreferences_target2 = '".trim($e_target[1])."';";
echo "</script>";

$sql_visible = "SELECT * FROM gui_wms WHERE fkey_gui_id = $1";
$v = array(Mapbender::session()->get("mb_user_gui"));
$t = array("s"); 
$res_visible = db_prep_query($sql_visible, $v, $t); 
$cnt_visible = 0; 

while($row = db_fetch_array($res_visible)){
	$gui_wms_visible[$cnt_visible] = $row["gui_wms_visible"];
	$fkey_wms_id_visible[$cnt_visible] = $row["fkey_wms_id"];
	if($cnt_visible>0){
		$vis .= ",";
		$wmsid .= ",";		
	}
	$vis .= $gui_wms_visible[$cnt_visible];
	$wmsid .= $fkey_wms_id_visible[$cnt_visible];
	$cnt_visible++;
}

echo '<script type="text/javascript">';
echo "var mod_gui_wms_visible = '".$vis."';";
echo "var mod_fkey_wms_id_visible = '".$wmsid."';";
echo "</script>";
?>

<script type="text/javascript">
<!--
var ind = window.opener.getMapObjIndexByName(mod_WMSpreferences_target1);
var my = window.opener.mb_mapObj[ind];

 function mb_swapWmsByIndex(mapObj_ind, indexA, indexB) {
 	var myMapObj = window.opener.mb_mapObj[mapObj_ind];
	if (indexA != indexB && indexA >= 0 && indexA < myMapObj.wms.length && indexB >= 0 && indexB < myMapObj.wms.length) {
		upper = myMapObj.wms[indexA];
		myMapObj.wms[indexA] = myMapObj.wms[indexB];
		myMapObj.wms[indexB] = upper;
		var upperLayers = myMapObj.layers[indexA];
		var upperStyles = myMapObj.styles[indexA];
		var upperQuerylayers = myMapObj.querylayers[indexA];
		myMapObj.layers[indexA] = myMapObj.layers[indexB];
		myMapObj.styles[indexA] = myMapObj.styles[indexB];
		myMapObj.querylayers[indexA] = myMapObj.querylayers[indexB];
		myMapObj.layers[indexB] = upperLayers;
		myMapObj.styles[indexB] = upperStyles;
		myMapObj.querylayers[indexB] = upperQuerylayers;
		return true;
	}
	else {
		return false;
	}
}

 

// Opacity version

function cambia_opacity(id,incremento){
	opacity = my.wms[id].gui_wms_mapopacity*100 + parseInt(incremento);
	my.wms[id].setOpacity(opacity);
	loadWMS();
}




function setMapformat(val){
	var tmp = val.split(",");
	my.wms[tmp[0]].gui_wms_mapformat = tmp[1];
	loadWMS();
}

function setFeatureformat(val){
	var tmp = val.split(",");
	my.wms[tmp[0]].gui_wms_featureinfoformat = tmp[1];
	loadWMS();
}

function setExceptionformat(val){
	var tmp = val.split(",");
	my.wms[tmp[0]].gui_wms_exceptionformat = tmp[1];
	loadWMS();
}

function swap(index1, index2){
	if (mb_swapWmsByIndex(ind, index1, index2) == true) {
		loadWMS();
		window.opener.zoom(mod_WMSpreferences_target1, true, 1.0);
		window.opener.mb_execloadWmsSubFunctions();
	}
}

function remove_wms(num){
	var cnt_vis=0;
	var wms_visible_down = mod_gui_wms_visible.split(",");
	var wms_vis_down = wms_visible_down.length;
	
	//check if there are more than two visible wms's
	for(var i=0; i < wms_visible_down.length; i++){
		var my_wms_visible = wms_visible_down[i];
		if(my_wms_visible == 0){
  			var cnt_vis = cnt_vis+1;		
		}
	}	

	if(my.wms.length - cnt_vis>1){
	  	var ind = window.opener.getMapObjIndexByName(mod_WMSpreferences_target1);  
  		window.opener.mb_mapObjremoveWMS(ind,num) 
		window.opener.mb_execloadWmsSubFunctions();
//  	window.opener.frames[mod_WMSpreferences_target2].document.location.reload();
  		window.location.reload();
	}
	else{
		alert ("Last WMS can't be removed.\n(Der letzte WMS kann nicht entfernt werden.)");
	}	
}

function loadWMS(){
	var str = "";
	var wms_visible = mod_gui_wms_visible.split(",");
	var wms_id_visible = mod_fkey_wms_id_visible.split(",");
	var visibleWmsIndexArray = new Array();
	
	for(var i=0; i < my.wms.length; i++){
		var found = false;
		for(var j=0; j < wms_id_visible.length; j++){
			if (wms_visible[j] == 1 && wms_id_visible[j] == my.wms[i].wms_id){
				visibleWmsIndexArray[visibleWmsIndexArray.length] = i;
				found = true;
			}
		}
		if (found == false && my.wms[i].gui_wms_visible == 1) {
			visibleWmsIndexArray[visibleWmsIndexArray.length] = i;
		}
	}
	
	for (var i = 0 ; i < visibleWmsIndexArray.length ; i++) {
		z = visibleWmsIndexArray[i];
		var mapString = "";
		var featureinfoString = "";
		var exceptionString = "";
				
		for(var j=0; j<my.wms[z].data_type.length; j++){
			if(my.wms[z].data_type[j] == 'map'){
				mapString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
				if(my.wms[z].data_format[j] == my.wms[z].gui_wms_mapformat){
					mapString += "selected";
				}
				mapString += ">"+my.wms[z].data_format[j]+"</option>";
			}
			else if(my.wms[z].data_type[j] == 'featureinfo'){
				featureinfoString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
				if(my.wms[z].data_format[j] == my.wms[z].gui_wms_featureinfoformat){
					featureinfoString += "selected";
				}
				featureinfoString += ">"+my.wms[z].data_format[j]+"</option>";
			}
			else if(my.wms[z].data_type[j] == 'exception'){
				exceptionString += "<option value='"+z+","+my.wms[z].data_format[j]+"'";
				if(my.wms[z].data_format[j] == my.wms[z].gui_wms_exceptionformat){
					exceptionString += "selected";
				}
				exceptionString += ">"+my.wms[z].data_format[j]+"</option>";
			}
		}

		str += "<table border='1' rules='rows'>";  
		str += "<tr><th>";
		str += "<img src='../img/button_gray/up.png' style='filter:Chroma(color=#C2CBCF);' onclick='swap("+visibleWmsIndexArray[i-1]+","+z+")' value='up' title='move WMS up'>&nbsp;";
		str += "<img src='../img/button_gray/down.png' style='filter:Chroma(color=#C2CBCF);' onclick='swap("+z+", "+visibleWmsIndexArray[i+1]+")'value='down'title='move WMS down'>&nbsp;</td>";
		str += "<img src='../img/button_gray/del.png' onclick='remove_wms("+z+")' value='remove' title='remove WMS from GUI'>&nbsp;";	
		str += "</th><th  width='300'><div id ='id_"+my.wms[z].wms_id+"' style='cursor:pointer' onmouseover = 'title=\""+"id:"+my.wms[z].wms_id+" "+my.wms[z].wms_abstract+"\"'><b>"+my.wms[z].wms_title+"</b>";
		str += "</div></th></tr>";
		//str += "<tr><td>ID:</td><td>"+my.wms[z].wms_id+"</td></tr>";
		str += "<tr><td>MapImageFormat: </td><td>";
		str += "<select onchange='setMapformat(this.value)'>"
		str += mapString;
		str += "</select></td></tr>";
		str += "<tr><td>FeatureInfoFormat: </td><td>";
		str += "<select onchange='setFeatureformat(this.value)'>";
		str += featureinfoString;
		str += "</select></td></tr>";
		str += "<tr><td>ExceptionFormat: </td><td>";
		str += "<select onchange='setExceptionformat(this.value)'>"
		str += exceptionString;
		str += "</select></td></tr></table>";	
		str += "<table><tr>";

		//opacity version
		str += "<tr><td>Opacity:</td><td><input type=\"button\" onclick=\"cambia_opacity('"+visibleWmsIndexArray[i]+"','-10')\" value=\"-\">";
		str += "<input id=\"valor_opacity_"+visibleWmsIndexArray[i]+"\" type=\"text\" disabled=\"disabled\" size=\"3\" value=\""+my.wms[visibleWmsIndexArray[i]].gui_wms_mapopacity*100+" %\">";
		str += "<input type=\"button\" onclick=\"cambia_opacity('"+visibleWmsIndexArray[i]+"','+10')\" value=\"+\">";
		str += "</td></tr>";

		str += "</tr></table><br>";
	}
	
	document.getElementById('data').innerHTML = str;
}

// -->
</script>
</head>
<body onload='loadWMS()'>
<div id='data'><div>
<form>
<input type='hidden' name='visibility' value=''>
</form>
</body>
</html>
