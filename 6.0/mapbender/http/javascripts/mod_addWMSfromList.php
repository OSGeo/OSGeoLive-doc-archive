<?php
# $Id: mod_addWMSfromList.php 4767 2009-09-30 17:27:36Z christoph $
# http://www.mapbender.org/index.php/mod_addWMSfromList.php
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
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Add WMS from Catalog</title>
<link rel="stylesheet" type="text/css" href="../css/administration_alloc.css">

<script type="text/javascript">
<!--

function mod_addWMS(pointer_name,version)
{
	pointer_name = pointer_name + window.opener.mb_getConjunctionCharacter(pointer_name);	

	if (version == '1.0.0'){
		var cap = pointer_name + "REQUEST=capabilities&WMTVER=1.0.0";
		var load = cap;
	}
	else if (version == '1.1.0'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0";
		var load = cap;
	}
	else if (version == '1.1.1'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1";
		var load = cap;
	}
	 
	if(load){
		window.opener.mod_addWMS_load(load);    
	}
}

function mod_addWMSfromDB(gui_id, wms_id) {
	window.opener.mod_addWMSById_load(gui_id, wms_id);
}

// -->
</script>

</head>
<body onload='window.focus()'>
<?php

require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");

$fieldHeight = 20;

$cnt_gui = 0;
$cnt_gui_wms = 0;
$cnt_wms = 0;

$exists = false;

$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");
$logged_gui_id=Mapbender::session()->get("mb_user_gui");

/*get infos from gui_element ********************************************************************************************/

$sql_target = "SELECT e_requires, e_target FROM gui_element WHERE e_id = 'addWMS' AND fkey_gui_id = $1";
$v = array($logged_gui_id);
$t = array('s');
$res_target = db_prep_query($sql_target, $v, $t);
$cnt_target = 0;
while($row = db_fetch_array($res_target)){ 
	$e_target = $row["e_target"];
	$e_require = $row["e_requires"];
	$cnt_target++;
}
if($cnt_target > 1){
	echo "alert('addWMS: ID not unique!');";
}
echo "<script type='text/javascript'>";
echo "var gui_id = '".$logged_gui_id."';";
echo "var mod_addWMS_data = '".$e_require."';";
$target = explode(",", $e_target);
echo "var mod_addWMS_target1 = '".trim($target[0])."';";
echo "var mod_addWMS_target2 = '".trim($target[1])."';";
echo "</script>";
/*get infos from gui_element ********************************************************************************************/


/*get allocated gui  ********************************************************************************************/

$arrayGuis=mb_getGUIs($logged_user_id);

$sql_gui = "SELECT * FROM gui WHERE gui_id IN (";
$v = $arrayGuis;
$t = array();
for ($i = 1; $i <= count($arrayGuis); $i++){
	if ($i > 1) { 
		$sql_gui .= ",";
	}
	$sql_gui .= "$" . $i;
	array_push($t, "s");
}
$sql_gui.= ") ORDER BY gui_name";

$res_gui = db_prep_query($sql_gui, $v, $t);
while($row = db_fetch_array($res_gui)){
	$gui_id[$cnt_gui] = $row["gui_id"];
	$gui_name[$cnt_gui] = $row["gui_name"];
	$cnt_gui++;
}
/*get allocated gui  ********************************************************************************************/

/*get allocated wms from allocated gui  ********************************************************************************************/								 
$sql_gui_wms = "SELECT DISTINCT fkey_wms_id FROM gui_wms WHERE fkey_gui_id IN (";
$v = $arrayGuis;
$t = array();
for ($i = 1; $i <= count($arrayGuis); $i++){
	if ($i > 1) { 
		$sql_gui_wms .= ",";
	}
	$sql_gui_wms .= "$".$i;
	array_push($t, "s");
}
$sql_gui_wms.= ") ORDER BY fkey_wms_id";

$res_gui_wms = db_prep_query($sql_gui_wms, $v, $t);
while($row = db_fetch_array($res_gui_wms)){
	$fkey_gui_id[$cnt_gui_wms] = $row["fkey_gui_id"];
	$fkey_wms_id[$cnt_gui_wms] = $row["fkey_wms_id"];
	$cnt_gui_wms++;
}								 
/*get allocated wms from allocated gui  ********************************************************************************************/							 

/*get allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/								 
$sql_wms = "SELECT DISTINCT wms_id, wms_title, wms_abstract, wms_getcapabilities,wms_version FROM wms WHERE wms_id IN (";
$v = $fkey_wms_id;
$t = array();
for ($i = 1; $i <= count($fkey_wms_id); $i++){
	if ($i > 1) { 
		$sql_wms .= ",";
	}
	$sql_wms .= "$" . $i;
	array_push($t, "s");
}
#$sql_wms.= ") ORDER BY wms_id";
$sql_wms.= ") ORDER BY wms_title";

$res_wms = db_prep_query($sql_wms, $v, $t);
while($row = db_fetch_array($res_wms)){
	$wms_id[$cnt_wms] = $row["wms_id"];
	$wms_title[$cnt_wms] = $row["wms_title"];
	$wms_abstract[$cnt_wms] = $row["wms_abstract"];
	$wms_getcapabilities[$cnt_wms] = $row["wms_getcapabilities"];
	$wms_version[$cnt_wms] = $row["wms_version"];
	$cnt_wms++;
}								 
/*get allocated wms-Abstract and wms-Capabilities from allocated gui   ********************************************************************************************/							 


/*INSERT HTML*/

#echo "<table border='2'  cellpadding='5' rules='rows'>";
echo "<table border='1'  style='font-size: 11;' cellpadding='2' rules='rows'>";
echo " <thead bgcolor = 'lightgrey' >";
echo "<tr ><td  height='30'>WMS-Title</td><td  align = 'left' class='fieldnames_s'>WMS-Abstract</td>";
echo " </thead>";
echo " <tbody >";
for($i=0; $i<$cnt_wms; $i++){
	echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
	echo "<td><div id ='id_".$wms_title[$i]."' class='even' name ='name_".$wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"\",\"".$wms_id[$i]."\")'>".$wms_title[$i]."</div></td>";
	echo "<td><div  id ='id_".$wms_abstract[$i]."' class='even' name ='name_".$wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"\",\"".$wms_id[$i]."\")'>".$wms_abstract[$i]."</div></td>";
	echo "</tr>";		
}		
echo "  </tbody>";							 						 
echo "</table>";


?>
<script type="text/javascript">

// -->
</script>
</body>
</html>