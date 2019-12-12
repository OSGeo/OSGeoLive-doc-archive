<?php
# $Id: mod_filteredGui_group.php 235 2006-05-11 08:34:48Z uli $
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

require_once(dirname(__FILE__) . "/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$postvars = explode(",", "selected_wms,selected_topic,insert,remove,remove_topic");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}

require_once(dirname(__FILE__) . "/../classes/class_administration.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Administration</title>
<?php
include '../include/dyn_css.php';
?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_wms"].selectedIndex == -1){
		document.getElementsByName("selected_wms")[0].style.backgroundColor = '#ff0000';
		return;
	}else{
		if(wert == "remove"){
			if(document.forms[0]["remove_topic[]"].selectedIndex == -1){
				document.getElementsByName("remove_topic[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.remove.value = 'true';
			document.form1.submit();
		}
		if(wert == "insert"){
			if(document.forms[0]["selected_topic[]"].selectedIndex == -1){
				document.getElementsByName("selected_topic[]")[0].style.backgroundColor = '#ff0000';
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


$fieldHeight = 20;

$language_suffix = "en";
//FIXME: there seems to be an error in dyn_css.php concerning php vars.
if ($language == "'de'") {
	$language_suffix = "de";
}

$logged_user_name = Mapbender::session()->get("mb_user_name");
$logged_user_id = Mapbender::session()->get("mb_user_id");

$admin = new administration();
$own_gui_id_array = $admin->getGuisByOwner($logged_user_id,true);
$own_wms_id_array = $admin->getWmsByOwnGuis($own_gui_id_array);
$cnt_wms = count($own_wms_id_array); 

/*handle remove, update and insert*****************************************************************/
if($insert){
	if(count($selected_topic) > 0){
		for($i=0; $i<count($selected_topic); $i++){
			$exists = false;
			$sql_insert = "SELECT * FROM wms_md_topic_category WHERE fkey_wms_id = $1 and fkey_md_topic_category_id = $2";
			$v = array($selected_wms,$selected_topic[$i]);
			$t = array('i','i');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				$sql_insert = "INSERT INTO wms_md_topic_category (fkey_wms_id, fkey_md_topic_category_id) VALUES($1, $2)";
				$v = array($selected_wms,$selected_topic[$i]);
				$t = array('i','i');
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_topic)>0){
		for($i=0; $i<count($remove_topic); $i++){
			$sql_remove = "DELETE FROM wms_md_topic_category WHERE fkey_md_topic_category_id = $1 and fkey_wms_id = $2";
			$v = array($remove_topic[$i],$selected_wms);
			$t = array('i','s');
			db_prep_query($sql_remove,$v,$t);
		}
	}
}

/*get all topics from selected wms*****************************************************************/
$sql_wms_topic = "SELECT t.md_topic_category_id, t.md_topic_category_code_".$language_suffix." ";
$sql_wms_topic .= "FROM wms_md_topic_category as w, md_topic_category as t WHERE w.fkey_wms_id = $1 AND w.fkey_md_topic_category_id = t.md_topic_category_id ";
$sql_wms_topic .= "ORDER BY t.md_topic_category_code_".$language_suffix;

if(!$selected_wms){$v = array($own_wms_id_array[0]);}
if($selected_wms){$v = array($selected_wms);}
$t = array('s');
$res_wms_topic = db_prep_query($sql_wms_topic,$v,$t);
$cnt_wms_topic=0;;
while($row = db_fetch_array($res_wms_topic)){
	$topic_id_wms[$cnt_wms_topic] = $row["md_topic_category_id"];
	$topic_name_wms[$cnt_wms_topic] =  $row["md_topic_category_code_".$language_suffix];
	$cnt_wms_topic++;
}

/*get all topics **********************************************************************************/
$sql_topic = "SELECT * FROM md_topic_category ORDER BY md_topic_category_code_" . $language_suffix;
$res_topic = db_query($sql_topic);
$cnt_topic = 0;
while($row = db_fetch_array($res_topic)){
	if (!in_array($row["md_topic_category_id"], $topic_id_wms)) {
		$topic_id[$cnt_topic] = $row["md_topic_category_id"];
		$topic_name[$cnt_topic] =  $row["md_topic_category_code_".$language_suffix];
		$cnt_topic++;
	}
}

/*INSERT HTML*/
echo "<form name='form1' action='" . $self ."' method='post'>";

/*insert wms in selectbox*************************************************************************/
echo "<div class='text1'>WMS: </div>";
echo "<select style='background:#ffffff' class='select1' name='selected_wms' onChange='submit()' size='10'>";
for($i=0; $i<$cnt_wms; $i++){
	echo "<option value='" . $own_wms_id_array[$i] . "' ";
	if($selected_wms && $selected_wms == $own_wms_id_array[$i]){
		echo "selected";
	}
	echo ">" . $admin->getWmsTitleByWmsId($own_wms_id_array[$i]) . "</option>";
}
echo "</select>";

/*insert all groups in selectbox*******************************************************************/
echo "<div class='text3'>TOPICS:</div><br>";
echo "<select style='background:#ffffff' class='select3' multiple='multiple' name='selected_topic[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_topic; $i++){
	echo "<option value='" . $topic_id[$i]  . "'>" . $topic_name[$i]  . "</option>";
}
echo "</select>";

/*Button*******************************************************************************************/

echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
echo "<input type='hidden' name='insert'>";

echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
echo "<input type='hidden' name='remove'>";

/*insert wms_topic_dependence and container_group_dependence in selectbox**************************************************/
echo "<div class='text4'>SELECTED TOPICS:</div>";
echo "<select style='background:#ffffff' class='select4' multiple='multiple' name='remove_topic[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_wms_topic; $i++){
	echo "<option value='" . $topic_id_wms[$i]  . "'>" . $topic_name_wms[$i]  . "</option>";
}
echo "</select>";
echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_wms.focus();
// -->
</script>
</body>
</html>