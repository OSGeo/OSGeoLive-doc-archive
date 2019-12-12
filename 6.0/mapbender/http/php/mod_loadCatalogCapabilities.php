<?php
# $Id$
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

//Include required files
$e_id="loadCSW";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done
 */ 
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");
$guiList = $_POST["guiList"];
$catList = $_POST["catList"];
$xml_file = $_POST["xml_file"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<!-- What about localization of Title values as below - do we handle that? -->
<title>Load Catalog</title>
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
  	#csw_info {
  		font-family: Arial, Helvetica, sans-serif;
  		text-decoration : none;
  		color: #808FFF;
  		font-size : 18px;
  	}
  	
  	.confirmation { border: #070 1px solid; background: url(img/dialog-confirmation.png) #E5FFE5 no-repeat 5px 5px; }
	.confirmation p em { color:#070; }
  	
  	-->
</style>

<script language="JavaScript">
function validate(value){
	if(value == 'guiList'){
		var listIndex = document.form1.guiList.selectedIndex;
		if(listIndex<0){
			alert("Please select a GUI to add Catalog to.");
			return false;
		}
		else{
			var gui_id=document.form1.guiList.options[listIndex].value;
			document.form1.action = '../php/mod_loadCatalog.php?<?php echo $urlParameters ?>';
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>

<?php

//Get GUIs for present user
require_once(dirname(__FILE__)."/../classes/class_administration.php");
$admin = new administration();
$ownguis = $admin->getGuisByOwner($_SESSION["mb_user_id"],true);

echo "<form name='form1' action='" . $self ."' method='post'>";
echo "<fieldset name=form1_field1><legend>GUI Catalogs</legend>";
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
	$arrayGUIs=$_SESSION["mb_user_guis"];
	echo count($arrayGUIs);
	echo "</select><br><br>";
	echo "</td>";
	echo "<td>";
	echo"CATALOG";
	echo"<br>";
	
	//Change to catalog tables: mif
	if(isset($guiList) && $guiList!=""){
		$sql = "SELECT DISTINCT cat.cat_title from gui_cat JOIN ";
		$sql .= "gui on gui_cat.fkey_gui_id = gui.gui_id JOIN cat ON gui_cat.fkey_cat_id = cat.cat_id ";
		$sql .= "and gui_cat.fkey_gui_id = gui.gui_id where gui.gui_name = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		$count=0;
		echo"<select size='8' name='catList' style='width:200px'>";
	
		while($row = db_fetch_array($res)){
			if ($row["cat_title"]!=""){
				echo "<option value='' ";
				echo ">".$row["cat_title"]."</option>";
			}
			$count++;
		}
	    echo "</select><br><br>";
	}
	else{
		echo"<select size='8' name='catList' style='width:200px' on Click='submit()'>";
		echo "</select><br><br>";
	}
	
	echo "</td>";
	echo "<tr></table><br>";
	echo "</fieldset>";
	//echo "<div id='csw_info'>";
	echo "<fieldset name=form1_field2>";
	echo "<div class='confirmation'>";
	echo "<p>Provide a link here to the Catalog Capabilities URL:<br/>";
	echo "Add one of the following REQUEST to the Online Resource URL to obtain the CSW Capabilities document:<br />";
	echo "<i>(Triple click to select and copy)</i><br>"; 
	echo "REQUEST=GetCapabilities&SERVICE=CSW&VERSION=2.0.2<br/></p>";
	echo "</div>";
	
	echo "<fieldset name=form1_field1_field1><legend>Link to Capabilities URL</legend>";
	if (isset($xml_file)){
		echo"<input type='text' name='xml_file' size='50' value='".$xml_file."'>";
	}else{
		echo"<input type='text' name='xml_file' size='50' value='http://'>";
	}
	echo"<input type='button' name='loadCap' value='Load Catalog' onClick='validate(\"guiList\")'>";
	echo "</fieldset>";
	echo "</fieldset>";
	echo "</form>";
}
else{
	echo "There are no guis available for this user. Please create a gui first.";
}
?>
</body>
</html>