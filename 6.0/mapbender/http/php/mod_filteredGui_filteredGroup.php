<?php
# $Id: mod_filteredGui_filteredGroup.php 7232 2010-12-11 15:36:45Z christoph $
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

$e_id="filteredGui_filteredGroup";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done 
 */ 
//security_patch_log(__FILE__,__LINE__); 

$selected_gui = $_POST["selected_gui"];
$insert = $_POST["insert"];
$remove = $_POST["remove"];
$remove_group = $_POST["remove_group"];
$selected_group = $_POST["selected_group"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Add Permission for Filtered Group to GUI</title>
<?php
include '../include/dyn_css.php';
?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_gui"].selectedIndex == -1){
		document.getElementsByName("selected_gui")[0].style.backgroundColor = '#ff0000';
		return;
	}else{
		if(wert == "remove"){
			if(document.forms[0]["remove_group[]"].selectedIndex == -1){
				document.getElementsByName("remove_group[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.remove.value = 'true';
			document.form1.submit();
		}
		if(wert == "insert"){
			if(document.forms[0]["selected_group[]"].selectedIndex == -1){
				document.getElementsByName("selected_group[]")[0].style.backgroundColor = '#ff0000';
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

/*handle remove, update and insert****************************************************************/
if($insert){
	if(count($selected_group)>0){
		for($i=0; $i<count($selected_group); $i++){
			$exists = false;
			$sql_insert = "SELECT * from gui_mb_group where fkey_gui_id = $1 and fkey_mb_group_id = $2";
			$v = array($selected_gui,$selected_group[$i]);
			$t = array('s','i');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				$sql_insert = "INSERT INTO gui_mb_group(fkey_gui_id, fkey_mb_group_id) VALUES($1, $2);";
				$v = array($selected_gui,$selected_group[$i]);
				$t = array('s','i');
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_group)>0){
		for($i=0; $i<count($remove_group); $i++){
			$sql_remove = "DELETE FROM gui_mb_group WHERE fkey_mb_group_id = $1 and fkey_gui_id = $2";
			$v = array($remove_group[$i],$selected_gui);
			$t = array('i','s');
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

/*get owner groups ********************************************************************************/

$sql_group = "SELECT * FROM mb_group WHERE mb_group_owner = $1 ORDER BY mb_group_name";
$v = array($logged_user_id);
$t = array('i');
$res_group = db_prep_query($sql_group,$v,$t);
while($row = db_fetch_array($res_group)){
	$group_id[$cnt_group] = $row["mb_group_id"];
	$group_name[$cnt_group] =  $row["mb_group_name"];
	$cnt_group++;
}

/*get owner groups from selected gui***************************************************************/
$sql_gui_group = "SELECT mb_group.mb_group_id, mb_group.mb_group_name, gui_mb_group.fkey_gui_id FROM gui_mb_group ";
$sql_gui_group .= "INNER JOIN mb_group ON gui_mb_group.fkey_mb_group_id = mb_group.mb_group_id ";
$sql_gui_group .= "WHERE gui_mb_group.fkey_gui_id = $1 ";

if(!$selected_gui){$v = array($gui_id_array[0]);}
if($selected_gui){$v = array($selected_gui);}
$t = array('s');
$sql_gui_group .= " AND mb_group.mb_group_owner = $2 ";
array_push($v,$logged_user_id);
array_push($t,'i');
$sql_gui_group .= " ORDER BY mb_group.mb_group_name";
$res_gui_group = db_prep_query($sql_gui_group,$v,$t);
while($row = db_fetch_array($res_gui_group)){
	$group_id_gui[$cnt_gui_group] = $row["mb_group_id"];
	$group_name_gui[$cnt_gui_group] =  $row["mb_group_name"];
	$cnt_gui_group++;
}

/*INSERT HTML*/
echo "<form name='form1' action='" . $self ."' method='post'>";

/*insert guis in selectbox*************************************************************************/
echo "<div class='text1'>GUI: </div>";
echo "<select style='background:#ffffff' class='select1' name='selected_gui' onChange='submit()' size='10'>";
for($i=0; $i<$cnt_gui; $i++){
	echo "<option value='" . $gui_id_array[$i] . "' ";
	if($selected_gui && $selected_gui == $gui_id_array[$i]){
		echo "selected";
	}
	echo ">" . $gui_name[$i]  . "</option>";
}
echo "</select>";

/*insert all groups in selectbox**************************************************************************/
echo "<div class='text2'>GROUP:</div><br>";
echo "<select style='background:#ffffff' class='select2' multiple='multiple' name='selected_group[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_group; $i++){
	echo "<option value='" . $group_id[$i]  . "'>" . $group_name[$i]  . "</option>";
}
echo "</select>";

/*Button****************************************************************************************************/

echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
echo "<input type='hidden' name='insert'>";

echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
echo "<input type='hidden' name='remove'>";

/*insert gui_group_dependence and container_group_dependence in selectbox**************************************************/
echo "<div class='text3'>SELECTED GROUP:</div>";
echo "<select style='background:#ffffff' class='select3' multiple='multiple' name='remove_group[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_gui_group; $i++){
	echo "<option value='" . $group_id_gui[$i]  . "'>" . $group_name_gui[$i]  . "</option>";
}
echo "</select>";
echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_gui.focus();
// -->
</script>
</body>
</html>