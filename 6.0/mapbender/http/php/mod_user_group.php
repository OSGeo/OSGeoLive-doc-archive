<?php
# $Id: mod_user_group.php 7706 2011-03-15 13:44:54Z armin11 $
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

$e_id="User_Group";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$postvars = explode(",", "filter1,selected_user,insert,remove,remove_group,selected_group");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
include '../include/dyn_css.php'; 
?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_user"].selectedIndex == -1){
		document.getElementsByName("selected_user")[0].style.backgroundColor = '#ff0000';
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
/**
 * filter the Userlist by str
 */
function filterUser(list, all, str){
	str=str.toLowerCase();
	var selection=[];
	var i,j,selected;
	for(i=0;i<list.options.length;i++){
		if(list.options[i].selected)
			selection[selection.length]=list.options[i].value;
	}
	
	list.options.length = 0;
	for(i=0; i<all.length; i++){
		if(all[i]['name'].toLowerCase().indexOf(str)==-1)
			continue;
		selected=false;
		for(j=0;j<selection.length;j++){
			if(selection[j]==all[i]['id']){
				selected=true;
				break;
			}
		}
		var newOption = new Option(selected?all[i]['name']+" ("+all[i]['email']+")":all[i]['name'],all[i]['id'],false,selected);
		newOption.setAttribute("title", all[i]['email']);
		list.options[list.options.length] = newOption;
	}	
}
/**
 * add Mail adress on selection
 */
function updateMail(list, all){
	var j=0;
	for(var i=0; i<list.options.length;i++){
		if(list.options[i].selected){
			for(j=j;j<all.length;j++){
				if(all[j]['id']==list.options[i].value){
					list.options[i].text=all[j]['name']+" ("+all[j]['email']+")";
					list.options[i].selected = true;
					break;
				}
			}
		}
		else{
			for(j=j;j<all.length;j++){
				if(all[j]['id']==list.options[i].value){
					list.options[i].text=all[j]['name'];
					list.options[i].selected = false;
					break;
				}
			}
		}
	}
}
</script>

</head>
<body>
<?php

$fieldHeight = 20;

$cnt_group = 0;
$cnt_user = 0;
$cnt_group = 0;
$cnt_group_user = 0;
$cnt_group_group = 0;
$exists = false;

/*handle remove, update and insert*****************************************************************/
if($insert){
	if(count($selected_group)>0){
		for($i=0; $i<count($selected_group); $i++){
			$exists = false;
			$sql_insert = "SELECT * from mb_user_mb_group where fkey_mb_user_id = $1 and fkey_mb_group_id = $2 AND (mb_user_mb_group_type = 1 or mb_user_mb_group_type IS NULL)";
			$v = array($selected_user,$selected_group[$i]);
			$t = array('i','i');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
    			$sql_insert = "INSERT INTO mb_user_mb_group(fkey_mb_user_id, fkey_mb_group_id) VALUES($1,$2)";
    			$v = array($selected_user,$selected_group[$i]);
    			$t = array('i','i');
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_group)>0){
		for($i=0; $i<count($remove_group); $i++){
			$sql_remove = "DELETE FROM mb_user_mb_group WHERE fkey_mb_group_id = $1 and fkey_mb_user_id = $2 AND (mb_user_mb_group_type = 1 or mb_user_mb_group_type IS NULL)";
			$v = array($remove_group[$i],$selected_user);
			$t = array('i','i');
			db_prep_query($sql_remove,$v,$t);
		}
	}
}

/*get all groups  ********************************************************************************************/
$sql_group = "SELECT * FROM mb_group ORDER BY mb_group_name";
$res_group = db_query($sql_group);
while($row = db_fetch_array($res_group)){
	$group_id[$cnt_group] = $row["mb_group_id"];
	$group_name[$cnt_group] = $row["mb_group_name"];
	$cnt_group++;
}

/*get all user **********************************************************************************************/
$sql_user = "SELECT * FROM mb_user ORDER BY mb_user_name";
$res_user = db_query($sql_user);
while($row = db_fetch_array($res_user)){
	$user_id[$cnt_user] = $row["mb_user_id"];
	$user_name[$cnt_user] = $row["mb_user_name"];
	$user_email[$cnt_user] = $row["mb_user_email"];
	$cnt_user++;
}

/*get all group from selected_user******************************************************************************/
$sql_user_mb_group = "SELECT mb_group.mb_group_id, mb_group.mb_group_name, mb_user_mb_group.fkey_mb_user_id FROM mb_user_mb_group ";
$sql_user_mb_group .= "INNER JOIN mb_group ON mb_user_mb_group.fkey_mb_group_id = mb_group.mb_group_id ";
$sql_user_mb_group .= "WHERE mb_user_mb_group.fkey_mb_user_id = $1 AND (mb_user_mb_group.mb_user_mb_group_type = 1 or mb_user_mb_group.mb_user_mb_group_type IS NULL)";
$sql_user_mb_group .= " ORDER BY mb_group.mb_group_name";

if(!$selected_user){$v = array($user_id[0]);}
if($selected_user){$v = array($selected_user);}
$t = array('i');

$res_user_mb_group = db_prep_query($sql_user_mb_group,$v,$t);
while($row = db_fetch_array($res_user_mb_group)){
	$group_id_user[$cnt_group_user] = $row["mb_group_id"];
	$group_name_user[$cnt_group_user] =  $row["mb_group_name"];
	$cnt_group_user++;
}

/*INSERT HTML*/
echo "<form name='form1' action='" . $self ."' method='post'>";

/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter1' id='filter1' name='filter1' onkeyup='filterUser(document.getElementById(\"selecteduser\"),user,this.value);'/>";
/*insert all user in selectbox*************************************************************************************/
echo "<div class='text1'>USER: </div>";
echo "<select style='background:#ffffff' onchange='updateMail(this, user);submit();' class='select1' id='selecteduser' name='selected_user' size='10'>";
for($i=0; $i<$cnt_user; $i++){
	echo "<option value='" . $user_id[$i] . "' title='".$user_email[$i]."' ";
	if($selected_user && $selected_user == $user_id[$i]){
		echo "selected>".$user_name[$i]." (".$user_email[$i].")";
	}
	else
		echo ">" . $user_name[$i];
	echo "</option>";
}
echo "</select>";

/*insert all group in selectbox**************************************************************************/
echo "<div class='text2'>GROUP:</div>";
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

/*insert user_group_dependence in selectbox**************************************************/
echo "<div class='text3'>SELECTED GROUP:</div>";
echo "<select style='background:#ffffff' class='select3' multiple='multiple' name='remove_group[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_group_user; $i++){
	echo "<option value='" . $group_id_user[$i]  . "'>" . $group_name_user[$i]  . "</option>";
}
echo "</select>";

echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_user.focus();
var user=[];
<?php
for($i=0; $i<$cnt_user; $i++){
	echo "user[".$i."]=[];\n";
	echo "user[".$i."]['id']='" . $user_id[$i]  . "';\n";
	echo "user[".$i."]['name']='" . $user_name[$i]  . "';\n";
	echo "user[".$i."]['email']='" . $user_email[$i]  . "';\n";
}
?>
// -->
</script>
</body>
</html>
