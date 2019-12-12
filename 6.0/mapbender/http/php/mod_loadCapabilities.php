<?php
# $Id: mod_loadCapabilities.php 7204 2010-12-11 11:36:13Z christoph $
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

$e_id = "loadWMS";
require_once dirname(__FILE__) . "/../php/mb_validatePermission.php";
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$guiList = $_POST["guiList"];
$xml_file = $_POST["xml_file"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Load WMS</title>
<?php
include '../include/dyn_css.php';
?>
<style type="text/css">
  	<!--
  	body{
      background-color: #ffffff;
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		color: #808080
  	}
  	.list_guis{
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		color: #808080;
  	}
  	a:link{
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		text-decoration : none;
  		color: #808080;
  	}
  	a:visited {
  		font-family: Arial, Helvetica, sans-serif;
  		text-decoration : none;
  		color: #808080;
  		font-size : 12px;
  	}
  	a:active {
  		font-family: Arial, Helvetica, sans-serif;
  		text-decoration : none;
  		color: #808080;
  		font-size : 12px;
  	}
  	-->
</style>
<script language="JavaScript">
function validate(wert){
	if(wert == 'guiList'){
		var listIndex = document.form1.guiList.selectedIndex;
		if(listIndex<0){
			alert("Please select a GUI.");
			return false;
		}
		else{
			var gui_id=document.form1.guiList.options[listIndex].value;
			document.form1.action = '../php/mod_loadwms.php?<?php echo $urlParameters ?>';
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>

<?php

require_once(dirname(__FILE__)."/../classes/class_administration.php");
$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);

echo "<form name='form1' action='" . $self ."' method='post'>";
echo "<table cellpadding='0' cellspacing='0' border='0'>";
echo "<tr>";
echo "<td>";
if (count($ownguis)>0){
	echo"GUI";
	echo"<br>";
	$v = array();
	$t = array();
	$sql = "SELECT * FROM gui WHERE gui_id IN ("; 
	for($i=0; $i<count($ownguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".($i+1);
		array_push($v,$ownguis[$i]);
		array_push($t,'s');
	}
	$sql .= ") ORDER BY gui_name";
	$res = db_prep_query($sql,$v,$t);
	echo"<select size='8' name='guiList' style='width:200px' onClick='submit()'>";
	while($row = db_fetch_array($res)){
		echo "<option value='".$row["gui_id"]."' ";
		if($guiList && $guiList == $row["gui_name"]){
			echo "selected";
		}
		echo ">".$row["gui_name"]."</option>";
	} 
	$arrayGUIs = Mapbender::session()->get("mb_user_id");
	echo count($arrayGUIs);
	echo "</select><br><br>";
	echo "</td>";
	echo "<td>";
	echo"WMS";
	echo"<br>";
	
	if(isset($guiList) && $guiList!=""){
		$sql = "SELECT DISTINCT wms.wms_title,gui_wms_position from gui_wms JOIN ";
		$sql .= "gui on gui_wms.fkey_gui_id = gui.gui_id JOIN wms ON gui_wms.fkey_wms_id = wms.wms_id ";
		$sql .= "and gui_wms.fkey_gui_id = gui.gui_id where gui.gui_name = $1 order by gui_wms_position";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		$count=0;
		echo"<select size='8' name='wmsList' style='width:600px'>";
	
		while($row = db_fetch_array($res)){
			if ($row["wms_title"]!=""){
				echo "<option value='' ";
				echo ">".$row["wms_title"]."</option>";
			}
			$count++;
		}
	    echo "</select><br><br>";
	}
	else{
		echo"<select size='8' name='wmsList' style='width:600px' on Click='submit()'>";
		echo "</select><br><br>";
	}
	echo "</td>";
	echo "<tr></table><br>";
	echo "Add the following REQUEST to the Online Resource URL to obtain the Capabilities document:<br>";
	echo "<i>(Triple click to select and copy)</i><br>"; 
	echo "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1<br>";
	echo "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0<br>";
	echo "REQUEST=capabilities&WMTVER=1.0.0<br><br>";
	echo "Link to WMS Capabilities URL (Note: Registrating of http-secured services only possible if the <b>curl library</b>  is used for connections - defined in mapbender.conf):<br>";
	#echo "Load WMS capabilities URL:<br>"
	
	if (isset($xml_file)){
		echo"<input type='text' name='xml_file' size='50' value='".$xml_file."'><br>";
	}else{
		echo"<input type='text' name='xml_file' size='50' value='http://'><br>";
	}
	//show fields for authentication - only possible if curl is used as connector!
	if (CONNECTION == 'curl') {
		echo"HTTP Authentication:<br>";
		echo"<input type='radio' name='auth_type' checked='checked' value='none'>None<br>";
		echo"<input type='radio' name='auth_type' value='digest'>Digest<br>";
    		echo"<input type='radio' name='auth_type' value='basic'>Basic<br>";
		echo"Username<br>";
		echo"<input type='text' name='username' size='50' value=''><br>";
		echo"Password:<br>";
		echo"<input type='text' name='password' size='50' value=''><br>";
	}
	echo"<input type='button' name='loadCap' value='Load' onClick='validate(\"guiList\")'>";	
	echo "</form>";
}
else{
	echo "There are no guis available for this user. Please create a gui first.";
}
?>
</body>
</html>
