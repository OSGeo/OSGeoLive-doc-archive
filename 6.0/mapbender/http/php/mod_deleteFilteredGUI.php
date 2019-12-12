<?php
# $Id: mod_deleteFilteredGUI.php 7207 2010-12-11 11:53:07Z tbaschetti $
# http://www.mapbender.org/index.php/DeleteGUI
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

$e_id="delete_filteredGui";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$guiList=$_POST["guiList"];
$del=$_POST["del"];

require_once(dirname(__FILE__)."/../classes/class_administration.php");

$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
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
<title>Delete GUI</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
function validate(){
	var ind = document.form1.guiList.selectedIndex;
	if(ind > -1){
		var permission =  confirm("delete: " + document.form1.guiList.options[ind].text + " ?");
		if(permission == true){
			document.form1.del.value = 1;
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>

<?php
###delete
if($del){
$sql = "DELETE FROM gui WHERE gui_id = $1";
$v = array($guiList);
$t = array('s');
$res = db_prep_query($sql,$v,$t);
}
###
if(count($ownguis)>0){
	$v = array();
	$t = array();
	$sql = "SELECT * from gui WHERE gui.gui_id IN(";
	for($i=0; $i<count($ownguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".($i+1);
		array_push($v,$ownguis[$i]);
		array_push($t,'s');
	}
	$sql .= ") order by gui_id";
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	echo "<form name='form1' action='" . $self ."' method='post'>";
	echo "<select class='guiList' size='20' name='guiList' class='guiList' onchange='document.form1.guiList.value = this.value;submit()'>";
	while($row = db_fetch_array($res)){
		$guivalue = $row["gui_id"];
		//mark previously selected GUI <==> text = " selected" 
		if ($guivalue == $guiList) {
			$text = " selected";
		}
		else {
			$text = "";
		}
	   echo "<option value='".$guivalue."'" . $text . ">".$row["gui_name"]."</option>";
	   $cnt++;
	}
	echo "</select><br>";
	
	// If WMS is selected, show more info
	if($guiList)
	{
		echo "<p class = 'wmsList'>";
		// Show description
		$sql = "SELECT gui_description FROM gui WHERE gui_id = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		
		echo "<b>Description:</b><br><br>";
		
		$cnt = 0;
		while($row = db_fetch_array($res))
		{
			$text = $row["gui_description"];
			if ($text){
				echo $text . "<br>";
				$cnt++;
			}
		}
		if ($cnt == 0) {
			echo "<i>- none -</i><br>";
		}
		
		
		// Show users
		$sql = "SELECT mb_user_name FROM mb_user, gui_mb_user WHERE fkey_mb_user_id = mb_user_id AND fkey_gui_id = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		
		echo "<br><br><b>Users using this GUI</b><br><br>";
	
		$cnt = 0;
		while($row = db_fetch_array($res))
		{
			echo $row["mb_user_name"]."<br>";
			$cnt++;
		}
		if ($cnt == 0) {
			echo "<i>- none -</i><br>";
		}
	
	
		// Show groups
		$sql = "SELECT mb_group_name FROM mb_group, gui_mb_group WHERE fkey_mb_group_id = mb_group_id AND fkey_gui_id = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		
		echo "<br><br><b>Groups using this GUI</b><br><br>";
	
		$cnt = 0;
		while($row = db_fetch_array($res))
		{
			echo $row["mb_group_name"]."<br>";
			$cnt++;
		}
		if ($cnt == 0) {
			echo "<i>- none -</i><br>";
		}
	
	
		// Show list of WMS exclusive to this GUI
		$sql = "SELECT wms_id, wms_title FROM wms, gui_wms WHERE fkey_wms_id = wms_id AND fkey_gui_id = $1";
		$v = array($guiList);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		
		echo "<br><br><b>List of WMS exclusive to this GUI</b><br><br>";
	
		$cnt = 0;
		$count = 0;
		while($row = db_fetch_array($res))
		{
			$wmstitle = $row["wms_title"];
			$wmsid =  $row["wms_id"];
			
			// Check how many GUIs use current WMS
			$sql2 = "SELECT COUNT(fkey_wms_id) FROM gui_wms WHERE fkey_wms_id = $1";
			$v = array($wmsid);
			$t = array('i');
			$res2 = db_prep_query($sql2,$v,$t);
			
			// Display if only selected GUI uses current WMS
			if (db_result($res2,0,0) == 1){
				//echo "<input type = checkbox name = wms" . $count . ">"; 
				echo $wmstitle . "<br>";
				$count++;
			}
			$cnt++;
		}
		if ($count == 0) {
			echo "<i>- none -</i><br>";
		}
		echo "</p>";
	}

	echo "<input class='button_del' type='button' value='delete' onclick='validate()'>";
	echo "<input type='hidden' name='del'>";
	echo "</form>";
}else{
	echo "There are no guis available for this user. Please create a gui first.";
}
?>
</body>
</html>
