<?php
# $Id: mod_exportMapImage.php 2413 2008-04-23 16:21:04Z christoph $
# http://www.mapbender.org/ExportMapimage
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

require_once(dirname(__FILE__) . "/../php/mb_validatePermission.php");

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
<title>Export Mapimage</title>
<?php
 include '../include/dyn_css.php';
?>
</head>
<style type="text/css">
<!-- 
 
input{
	width:50px;
    font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
div{
	font-family : Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.imageformat{
	width:50px;
	font-family : Arial, Helvetica, sans-serif;
	font-size: 14px;
    font-weight: bold;
}

-->
</style>
<?php



echo "<script type='text/javascript'>";
echo "var target = '".$_REQUEST["target"]."';";

echo "</script>";
?>
<script type="text/javascript">


// some defaults
try{if (pngExport){}}catch(e){pngExport = 'true';}
try{if (jpegExport){}}catch(e){jpegExport = 'true';}
try{if (geotiffExport){}}catch(e){geotiffExport = 'true';}


function generateExportOptions(){
if (pngExport=='true'){
	document.write('<tr><td><span class="imageformat"><input type="radio" name="imageformat" value="png">PNG</span></td></tr>');		
}
if (jpegExport=='true'){
	document.write('<tr><td><span class="imageformat"><input type="radio" name="imageformat" value="jpeg">JPEG / JPG</span></td></tr>');		
}
if (geotiffExport=='true'){
	document.write('<tr><td><span class="imageformat"><input type="radio" name="imageformat" value="geotiff">GeoTIFF</span></td></tr>');		
}



}

function exportMapimage(){
		
	choosen = "";
	len = document.form1.imageformat.length;
	
	for (i = 0; i <len; i++) {
		if (document.form1.imageformat[i].checked) {
			choosen = document.form1.imageformat[i].value;
		}
	}
	
	if (choosen == "") {
		alert("Keine Wahl getroffen, per DEFAULT wird die Karte als PNG exportiert");
		choosen = document.form1.imageformat[0].value;
	}
	else {
		//alert(choosen)
	}

	
	var idx = window.opener.getMapObjIndexByName(target);
	
    
	var wms_string = "";
    
	for(var ii=0; ii<window.opener.mb_mapObj[idx].wms.length; ii++){

    
    if (window.opener.mb_mapObj[idx].mapURL[ii] == false || typeof(window.opener.mb_mapObj[idx].mapURL[ii]) == 'undefined' || window.opener.mb_mapObj[idx].mapURL[ii] == 'undefined'){
				
				//alert('Keine WMSe vorhanden.');
			} else{

   				if (ii==0){
					wms_string = window.opener.mb_mapObj[idx].mapURL[ii];	
				} else {
					wms_string += "___"+window.opener.mb_mapObj[idx].mapURL[ii];
				}
			}		
	}
	wms_string = encodeURIComponent(wms_string);
	var myLocation = "../php/mod_exportMapImage_server.php?target="+target+"&imagetype="+choosen+"&wms_urls="+wms_string;
	//mynewwin = window.open("../php/mod_exportMapImage_server.php?target="+target+"&imagetype="+choosen+"&wms_urls="+wms_string+"","exportMapImage","width=180, height=200, resizable=yes ");
	document.location.href = myLocation; 
	
//	alert('ImageExport done');
//	window.close();
	
}

function close_exportMapimage(){
	window.close();
}

</script>
<body>
<form name='form1' method='POST' action='' target="_blank" onSubmit="return FormCheck()">
<table border='0'>
<div>Please select a format for the exported image!</div><br>

<script type="text/javascript"> generateExportOptions();
</script>

<tr>
<td> <br><br> </td>
</tr>

</table>  
<div id="buttons" align='right'>
		<input type='button' name='expImg_ok' value="OK" onclick='exportMapimage();'>
		<input type='button' name='expImg_close' value="Close" onclick='close_exportMapimage();'>
</div>
</form>
</body>
</html>
