<?php
# $Id: mod_filteredGroup_filteredGui.php 7232 2010-12-11 15:36:45Z christoph $
# http://www.mapbender.org/index.php/Administration
#
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

$e_id="filteredGroup_filteredGui";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done 
 */
//security_patch_log(__FILE__,__LINE__); 
$selected_group = $_POST["selected_group"];
$selected_gui = $_POST["selected_gui"];
$insert = $_POST["insert"];
$remove = $_POST["remove"];
$remove_gui = $_POST["remove_gui"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Add Permission for Filtered GUI to Filtered Group</title>
<?php
include '../include/dyn_css.php';
?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_group"].selectedIndex == -1){
		document.getElementsByName("selected_group")[0].style.backgroundColor = '#ff0000';
		return;
	}else{
		if(wert == "remove"){
			if(document.forms[0]["remove_gui[]"].selectedIndex == -1){
				document.getElementsByName("remove_gui[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.remove.value = 'true';
			document.form1.submit();
		}
		if(wert == "insert"){
			if(document.forms[0]["selected_gui[]"].selectedIndex == -1){
				document.getElementsByName("selected_gui[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.insert.value = 'true';
			document.form1.submit();
		}
	}
}
</script>

</head>
<body>
<?php
require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");

$fieldHeight = 20;

$cnt_gui = 0;
$cnt_group = 0;
$cnt_group = 0;
$cnt_gui_group = 0;
$cnt_gui_group = 0;
$exists = false;
$gui_id_array = array();

$logged_user_name = Mapbender::session()->get("mb_user_name");
$logged_user_id = Mapbender::session()->get("mb_user_id");

/*handle remove, update and insert*****************************************************************/
if($insert){
	if(count($selected_gui)>0){
		for($i=0; $i<count($selected_gui); $i++){
			$exists = false;
			$sql_insert = "SELECT * from gui_mb_group where fkey_mb_group_id = $1 and fkey_gui_id = $2 ";
			$v = array($selected_group,$selected_gui[$i]);
			$t = array('i','s');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				$sql_insert = "INSERT INTO gui_mb_group(fkey_mb_group_id, fkey_gui_id) VALUES($1, $2)";
				$v = array($selected_group,$selected_gui[$i]);
				$t = array('i','s');
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_gui)>0){
		for($i=0; $i<count($remove_gui); $i++){
			$sql_remove = "DELETE FROM gui_mb_group WHERE fkey_gui_id = $1 and fkey_mb_group_id = $2";
			$v = array($remove_gui[$i],$selected_group);
			$t = array('s','i');
			db_prep_query($sql_remove,$v,$t);
		}
	}
}

/*get allocated gui  ******************************************************************************/

$arrayGuis=mb_getGUIs($logged_user_id);
$v = array();
$t = array();
$sql_gui = "SELECT * FROM gui WHERE gui_id IN (";
for($i=0; $i<count($arrayGuis); $i++){
	if($i>0){ $sql_gui .= ",";}
	$sql_gui .= "$".($i+1);
	array_push($v,$arrayGuis[$i]);
	array_push($t,'s');
}
$sql_gui.= ") ORDER BY gui_name";

$res_gui = db_prep_query($sql_gui,$v,$t);
while($row = db_fetch_array($res_gui)){
	$gui_id_array[$cnt_gui] = $row["gui_id"];
	$gui_name[$cnt_gui] = $row["gui_name"];
	$cnt_gui++;
}

/*get owner group *********************************************************************************/

$sql_group = "SELECT * FROM mb_group WHERE mb_group_owner = $1 ORDER BY mb_group_name";
$v = array($logged_user_id);
$t = array('i');
$res_group = db_prep_query($sql_group,$v,$t);
while($row = db_fetch_array($res_group)){
	$group_id[$cnt_group] = $row["mb_group_id"];
	$group_name[$cnt_group] = $row["mb_group_name"];
	$cnt_group++;
}

if ($cnt_group > 0) {

	/*get allocated gui from selected_group******************************************************************************/
	$arrayGuis=mb_getGUIs($logged_user_id);
	$v = array();
	$t = array();
	$sql_group_mb_gui = "SELECT gui.gui_id, gui.gui_name, gui_mb_group.fkey_mb_group_id FROM gui_mb_group ";
	$sql_group_mb_gui .= " JOIN gui ON gui_mb_group.fkey_gui_id = gui.gui_id ";
	$sql_group_mb_gui .= "WHERE gui_mb_group.fkey_mb_group_id = $1 ";
	if(!$selected_group){
		if($group_id[0] > 0){
			array_push($v,$group_id[0]);
			array_push($t,'i');
		}
		else{
			array_push($v, "NULL");
			array_push($t,'i');
		}
	}
	else if($selected_group){
		array_push($v,$selected_group);
		array_push($t,'i');
	}
	$sql_group_mb_gui .= " AND gui.gui_id IN (";
	$c = 2;
	for($i=0; $i<count($arrayGuis); $i++){
		if($i>0){ $sql_group_mb_gui .= ",";}
		$sql_group_mb_gui .= "$".$c;
		array_push($v,$arrayGuis[$i]);
		array_push($t,'s');
		$c++;
	}
	$sql_group_mb_gui .= ") ORDER BY gui.gui_name";
	
	$res_group_mb_gui = db_prep_query($sql_group_mb_gui,$v,$t);
	while($row = db_fetch_array($res_group_mb_gui)){
		$gui_id_group[$cnt_gui_group] = $row["gui_id"];
		$gui_name_group[$cnt_gui_group] =  $row["gui_name"];
		$cnt_gui_group++;
	}

	/*INSERT HTML*/
	echo "<form name='form1' action='" . $self ."' method='post'>";
	
	/*insert all group in selectbox********************************************************************/
	echo "<div class='text1'>GROUP: </div>";
	echo "<select style='background:#ffffff' class='select1' name='selected_group' onChange='submit()' size='10'>";
	for($i=0; $i<$cnt_group; $i++){
		echo "<option value='" . $group_id[$i] . "' ";
		if($selected_group && $selected_group == $group_id[$i]){
			echo "selected";
		}
		echo ">" . $group_name[$i]  . "</option>";
	}
	echo "</select>";
	
	/*insert all gui in selectbox**********************************************************************/
	echo "<div class='text2'>GUI:</div>";
	echo "<select style='background:#ffffff' class='select2' multiple='multiple' name='selected_gui[]' size='$fieldHeight' >";
	for($i=0; $i<$cnt_gui; $i++){
		echo "<option value='" . $gui_id_array[$i]  . "'>" . $gui_name[$i]  . "</option>";
	}
	echo "</select>";
	
	/*Button*******************************************************************************************/
	
	echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
	echo "<input type='hidden' name='insert'>";
	
	echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
	echo "<input type='hidden' name='remove'>";
	
	/*insert group_gui_dependence in selectbox*********************************************************/
	echo "<div class='text3'>SELECTED GUI:</div>";
	echo "<select style='background:#ffffff' class='select3' multiple='multiple' name='remove_gui[]' size='$fieldHeight' >";
	for($i=0; $i<$cnt_gui_group; $i++){
		echo "<option value='" . $gui_id_group[$i]  . "'>" . $gui_name_group[$i]  . "</option>";
	}
	echo "</select>";
	
	echo "</form>";
}
else {
	echo "<div class = 'text3'>There are no groups available for this user.</div>";
}


?>
<script type="text/javascript">
<!--
document.forms[0].selected_group.focus();
// -->
</script>
</body>
</html>