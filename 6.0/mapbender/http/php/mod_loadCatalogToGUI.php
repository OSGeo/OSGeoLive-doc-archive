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

$e_id="loadCSWGUI";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$guiList = $_POST["guiList"];
$catList = $_POST["catList"];
$catID = $_POST["catID"];


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Load WMS from Catalog</title>
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
	.text1{
	   font-family: Arial, Helvetica, sans-serif;
	   font-size : 15px;
	   position:absolute;
	   top:190px;
	}
	.select1{
	   position:absolute;
	   top:210px;
	   width:270px;
	}
	.text2{
	   font-family: Arial, Helvetica, sans-serif;
	   font-size : 15px;
	   position:absolute;
	   top:190px;
	   left:300px;
	}
	.select2{
	   position:absolute;
	   top:210px;
	   left:300px;
	}
	.getcapabilities{
	   font-family: Arial, Helvetica, sans-serif;
	   font-size : 15px;
	   position:absolute;
	   top:570px;
	}

  	-->
</style>
<script language="JavaScript">
function validate(vals){
   if(vals == 'guiList'){
      var listIndex = document.form1.guiList.selectedIndex;
      if(listIndex<0){
		   alert("Please select a GUI.");
			return false;
      }
      else{
         var gui_id=document.form1.guiList.options[listIndex].value;
         	//LOAD CATALOG
			document.form1.action='../php/mod_loadCatalog.php<?php echo SID;?>';
			document.form1.submit();
      }
   }
}
function load(){
      if(document.form1.guiList.selectedIndex<0){
		   alert("Please Select a GUI.");
			return false;
      }
      var gui_ind = document.form1.guiList.selectedIndex;
      var ind = document.form1.catID.selectedIndex;
      var ind2 = document.form1.guiID_.selectedIndex;
			var indexCatList = document.form1.catID.selectedIndex;
			var permission = true;

			var selectedCatId = document.form1.catID.options[document.form1.catID.selectedIndex].value;
			for (i = 0; i < document.form1.catList.length; i++) {
						if (document.form1.catList.options[i].value == selectedCatId){
							 permission = false;							 
							 alert ('The Catalog (' + selectedCatId + ') is already loaded in this application.');
							 break;
						}
			}			 
			
  			if (permission) { // only check if permission is not false 
        	var loadConfirmed = confirm("Load " + document.form1.catID.options[ind].text + " FROM " + document.form1.guiID_.options[ind2].value + " INTO "+document.form1.guiList.options[gui_ind].value+" ?");

            if(loadConfirmed){
             document.form1.submit();
          	}
          	else{
             	document.form1.guiID_.selectedIndex = -1;
          	}
		}	
			
}
</script>
</head>
<body>

<?php

require_once(dirname(__FILE__)."/../classes/class_administration.php");
$admin = new administration();
$ownguis = $admin->getGuisByOwner($_SESSION["mb_user_id"],true);


// insert values here
if(isset($catID) && isset($guiID_)){
	
	$sql_ins = "INSERT INTO gui_cat (fkey_gui_id,fkey_cat_id) ";
	$sql_ins .= "VALUES ($1,$2)";
	$v = array($guiList,$catID);
	$t = array('s','i');
	db_prep_query($sql_ins,$v,$t);

}

echo "<form name='form1' action='" . $self."' method='post'>";

echo "<table cellpadding='0' cellspacing='0' border='0'>";
echo "<tr>";
echo "<td>";
if (count($ownguis)>0){
	echo"GUI";
	echo"<br/>";
	 
	$sql = "SELECT * FROM gui WHERE gui_id IN (";
	$v = $ownguis;
	$t = array();
	for ($i = 1; $i <= count($ownguis); $i++){
		if ($i > 1) { 
			$sql .= ",";
		}
		$sql .= "$".$i;
		array_push($t, "s");
	}
	$sql .= ") ORDER BY gui_name";	
	$res = db_prep_query($sql, $v, $t);
	$count=0;
	echo"<select size='8' name='guiList' style='width:200px' onClick='submit()'>";
	while($row = db_fetch_array($res)){
		$gui_name[$count]=$row["gui_name"];
		$gui_description[$count]=$row["gui_description"];
		$count++;
		echo "<option value='".$row["gui_id"]."' ";
		if($guiList && $guiList == $row["gui_name"]){
			echo "selected";
		}
		echo ">".$row["gui_name"]."</option>";
	}
	
	$arrayGUIs=$_SESSION["mb_user_guis"];
	echo count($arrayGUIs);
	echo "</select><br/><br/>";
	
	echo "</td>";
	echo "<td>";
	echo "Catalog";
	echo "<br/>";
	if(isset($guiList) && $guiList!=""){
		$sql = "SELECT DISTINCT cat_id, cat_title FROM gui_cat ";
		$sql .= "JOIN gui ON gui_cat.fkey_gui_id = gui.gui_id JOIN cat ON gui_cat.fkey_cat_id=cat.cat_id ";
		$sql .= "AND gui_cat.fkey_gui_id=gui.gui_id WHERE gui.gui_name = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);	
		$count=0;
		echo"<select size='8' name='catList' style='width:200px'>";
	
		while($row = db_fetch_array($res)){
			if ($row["cat_title"]!=""){
				echo "<option value='".$row["cat_id"]."' ";
				echo ">".$row["cat_title"]."</option>";
			}
			$count++;
		}
		echo "</select><br><br>";
	}else{
		echo"<select size='8' name='catList' style='width:200px' on Click='submit()'>";
		echo "</select><br><br>";
	}
	echo "</td>";
	echo "<tr></table><br>";
	
	echo"<div class='text1'>Load Catalog</div>";
	$sql = "SELECT DISTINCT cat.cat_id,cat.cat_title,cat.cat_abstract,cat.cat_owner FROM gui_cat JOIN cat ON ";
	$sql .= "cat.cat_id = gui_cat.fkey_cat_id WHERE gui_cat.fkey_gui_id IN(";
	$v = $arrayGUIs;
	$t = array();
	for ($i = 1; $i <= count($arrayGUIs); $i++){
		if ($i > 1) {
			$sql .= ",";
		}
		$sql .= "$" . $i;
		array_push($t, "s");
	}
	$sql .= ") ORDER BY cat.cat_title";
	$res = db_prep_query($sql, $v, $t);
	echo "<select class='select1' name='catID' size='20' onchange='submit()'>";
	$cnt = 0;
	while($row = db_fetch_array($res)){
		echo "<option value='".$row["cat_id"]."' ";
		if($row["cat_owner"] == $_SESSION["mb_user_id"]){
			echo "style='color:green' ";	
		}
		else{
			echo "style='color:red' ";
		}
		if(isset($catID) && $catID == $row["cat_id"]){
			echo "selected";
			$wms_getcapabilities = $row["wms_getcapabilities"];
		}
		echo ">".$row["cat_title"]."</option>";
		$cnt++;
	}
	echo "</select>";
	
	if(isset($catID)){
		echo "<div class='text2'>FROM:</div>";
		$sql = "SELECT * from gui_cat WHERE fkey_cat_id = $1 ORDER BY fkey_gui_id";
		$v = array($catID);
		$t = array("s");
		$res = db_prep_query($sql, $v, $t);
		echo "<select class='select2' name='guiID_' size='20' onchange='load()'>";
		$cnt = 0;
		while($row = db_fetch_array($res)){
			echo "<option value='".$row["fkey_gui_id"]."' ";
			echo ">".$row["fkey_gui_id"]."</option>";
			$cnt++;
		}
	echo "</select>";
}
echo "</form>";
}else{
	echo "There are no GUIs available for this user. Please create a GUI first.";
}
echo "<div class='getcapabilities'>" . $wms_getcapabilities . "</div>";
?>
</body>
</html>