<?php
# $Id: mod_simpleWMSpreferences.php 2413 2008-04-23 16:21:04Z christoph $
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
<meta name="author-mail" content="info@ccgis.de">
<meta name="author" content="U. Rothstein">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Edit WMS Context</title>
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
   	color:#0066cc;
      font-size:10pt
   } 
   
   th{
   	background-color:white;
   }  
   
   select{
		width:240px;
   }    
   
	.up{
         color:#0066cc;
         border: solid thin;
         height:20px;
         width:70px;
   }
   
	.remove{
         color:#0066cc;
         border: solid thin;
         height:20px;
         width:60px;
   }
	-->
</STYLE>
<?php
echo '<script type="text/javascript">';
echo "var mod_WMSpreferences_target1 = '".trim($e_target[0])."';";
echo "var mod_WMSpreferences_target2 = '".trim($e_target[1])."';";
echo "</script>";
?>
<script type="text/javascript">
<!--
var ind = window.opener.getMapObjIndexByName(mod_WMSpreferences_target1);
var my = window.opener.mb_mapObj[ind];

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
function up(num){
	if(num > 0){
		var upper = new Object();
		upper = my.wms[num-1];
		var lower = new Object();
		my.wms[num-1] = my.wms[num];
		my.wms[num] = upper;
		window.opener.frames[mod_WMSpreferences_target2].document.location.reload();

		var upperLayers = my.layers[num-1];
		var upperStyles = my.styles[num-1];
		var upperQuerylayers = my.querylayers[num-1];
		my.layers[num-1] = my.layers[num];
		my.styles[num-1] = my.styles[num];
		my.querylayers[num-1] = my.querylayers[num];
		my.layers[num] = upperLayers;
		my.styles[num] = upperStyles;
		my.querylayers[num] = upperQuerylayers;
		loadWMS();
		window.opener.zoom(mod_WMSpreferences_target1, true, 1.0);
	}
}


function remove_wms(num){
  //alert(num +" length:"+my.wms.length);
  
  if (my.wms.length>1) {
	var ind = window.opener.getMapObjIndexByName(mod_WMSpreferences_target1);   
	window.opener.mb_mapObjremoveWMS(ind,num) 
  //alert(ind);	
	window.opener.frames[mod_WMSpreferences_target2].document.location.reload();
	window.location.reload();
   }
   else{
   	alert ("Last WMS can't be removed.\n(Der letzte WMS kann nicht entfernt werden.)");
   }	
}

function loadWMS(){
	var str = "";
	for(var i=0; i < my.wms.length; i++){
		str += "<table border='0' cellpading ='1' size='100%'>";  
		
		str += "<tr>";
		if(i==0){
		str += "<th><input src='/evudb/images/mapbender/button_gray/up_off.gif' disabled type='image' onclick='up("+i+")' value='nach oben'>&nbsp;&nbsp;<input src='/evudb/images/mapbender/button_gray/trash_on.gif' type='image' onclick='remove_wms("+i+")' value='entfernen'></th><th  width='300'><div id ='id_"+my.wms[i].wms_id+"' style='cursor:pointer' onmouseover = 'title=\""+my.wms[i].wms_abstract+"\"'><b>"+my.wms[i].wms_title+"</b></div></th>";
		}
		else
		{
		str += "<th><input type='image' src='/evudb/images/mapbender/button_gray/up_on.gif' onclick='up("+i+")' value='nach oben'>&nbsp;&nbsp;<input src='/evudb/images/mapbender/button_gray/trash_on.gif' type='image' onclick='remove_wms("+i+")' value='entfernen'></th><th  width='300'><div id ='id_"+my.wms[i].wms_id+"' style='cursor:pointer' onmouseover = 'title=\""+my.wms[i].wms_abstract+"\"'><b>"+my.wms[i].wms_title+"</b></div></th>";
		}
		
		
		str += "<tr>";
      str += "<table>";
      
     

	}   
	document.getElementById('data').innerHTML = str;
}

// -->
</script>
</head>
<body onload='loadWMS()'>
<div id='data'><div>
</body>
</html>
