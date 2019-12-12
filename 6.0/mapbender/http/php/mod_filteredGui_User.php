<?php
# $Id: mod_filteredGui_User.php 7261 2010-12-12 09:57:18Z christoph $
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

$e_id="filteredGui_user";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$postvars = explode(",", "selected_gui,filter2,insert,remove,filter3,remove_user,selected_user");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}

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
	if(document.forms[0]["selected_gui"].selectedIndex == -1){
		document.getElementsByName("selected_gui")[0].style.backgroundColor = '#ff0000';
		return;
	}else{
		if(wert == "remove"){
			if(document.forms[0]["remove_user[]"].selectedIndex == -1){
				document.getElementsByName("remove_user[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.remove.value = 'true';
			document.form1.submit();
		}
		if(wert == "insert"){
			if(document.forms[0]["selected_user[]"].selectedIndex == -1){
				document.getElementsByName("selected_user[]")[0].style.backgroundColor = '#ff0000';
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
require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");

$fieldHeight = 20;

$cnt_gui = 0;
$cnt_user = 0;
$cnt_group = 0;
$cnt_gui_user = 0;
$cnt_gui_group = 0;
$gui_id_array = array();
$exists = false;

$logged_user_name = Mapbender::session()->get("mb_user_name");
$logged_user_id = Mapbender::session()->get("mb_user_id");

#echo Mapbender::session()->get("mb_user_name");

/*handle remove, update and insert*****************************************************************/
if($insert){
	if(count($selected_user)>0){
		for($i=0; $i<count($selected_user); $i++){
			$exists = false;
			$sql_insert = "SELECT * from gui_mb_user where fkey_gui_id = $1 and fkey_mb_user_id = $2";
			$v = array($selected_gui,$selected_user[$i]);
			$t = array('s','i');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				$sql_insert = "INSERT INTO gui_mb_user(fkey_gui_id, fkey_mb_user_id) VALUES($1, $2)";
				$v = array($selected_gui,$selected_user[$i]);
				$t = array('s','i');				
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_user)>0){
		for($i=0; $i<count($remove_user); $i++){
			$sql_remove = "DELETE FROM gui_mb_user WHERE fkey_mb_user_id = $1 and fkey_gui_id = $2";
			$v = array($remove_user[$i],$selected_gui);
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

/*get all user **********************************************************************************************/
$sql_user = "SELECT * FROM mb_user ORDER BY mb_user_name";
$res_user = db_query($sql_user);
while($row = db_fetch_array($res_user)){
	$user_id[$cnt_user] = $row["mb_user_id"];
	$user_name[$cnt_user] = $row["mb_user_name"];
	$user_email[$cnt_user] = $row["mb_user_email"];
	$cnt_user++;
}

/*get all user from selected gui*******************************************************************/
$sql_gui_mb_user = "SELECT mb_user.mb_user_email, mb_user.mb_user_id, mb_user.mb_user_name, gui_mb_user.fkey_gui_id FROM gui_mb_user ";
$sql_gui_mb_user .= "INNER JOIN mb_user ON gui_mb_user.fkey_mb_user_id = mb_user.mb_user_id ";
$sql_gui_mb_user .= "WHERE gui_mb_user.fkey_gui_id= $1 ";
$sql_gui_mb_user .= " ORDER BY mb_user.mb_user_name";
if(!$selected_gui){$v = array($gui_id_array[0]);}
if($selected_gui){$v = array($selected_gui);}
$t = array('s');
$res_gui_mb_user = db_prep_query($sql_gui_mb_user,$v,$t);
while($row = db_fetch_array($res_gui_mb_user)){
	$user_id_gui[$cnt_gui_user] = $row["mb_user_id"];
	$user_name_gui[$cnt_gui_user] =  $row["mb_user_name"];
	$user_email_gui[$cnt_gui_user] = $row["mb_user_email"];
	$cnt_gui_user++;
}


/*INSERT HTML*/

echo "<form name='form1' action='" . $self ."' method='post'>";

/*insert projects in selectbox********************************************************************/
echo "<div class='text1'>GUI: </div>";
echo "<select style='background:#ffffff' class='select1' name='selected_gui' size='10' onchange='submit()'>";
for($i=0; $i<$cnt_gui; $i++){
	echo "<option value='" . $gui_id_array[$i] . "' ";
	if($selected_gui && $selected_gui == $gui_id_array[$i]){
		echo "selected";
	}
	echo ">" . $gui_name[$i]  . "</option>";                 }
echo "</select>";

/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter2' id='filter2' name='filter2' onkeyup='filterUser(document.getElementById(\"selecteduser\"),user,this.value);'/>";
/*insert all profiles in selectbox*****************************************************************/
echo "<div class='text2'>USER:</div>";
echo "<select style='background:#ffffff' onchange='updateMail(this, user)' class='select2' multiple='multiple' id='selecteduser' name='selected_user[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_user; $i++){
	echo "<option value='" . $user_id[$i]  . "' title='".$user_email[$i]."'>" . $user_name[$i]  . "</option>";
}
echo "</select>";
/*Button*******************************************************************************************/

echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
echo "<input type='hidden' name='insert'>";

echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
echo "<input type='hidden' name='remove'>";

/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter3' id='filter3' name='filter3' onkeyup='filterUser(document.getElementById(\"removeuser\"),guiuser,this.value);'/>";
/*insert container_profile_dependence and container_group_dependence in selectbox******************/
echo "<div class='text3'>SELECTED USER:</div>";
echo "<select style='background:#ffffff' onchange='updateMail(this, user)' class='select3' multiple='multiple' name='remove_user[]' id='removeuser' size='$fieldHeight' >";
for($i=0; $i<$cnt_gui_user; $i++){
	echo "<option value='" . $user_id_gui[$i]  . "' title='".$user_email_gui[$i]."'>" . $user_name_gui[$i]  . "</option>";
}
echo "</select>";

echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_gui.focus();
var user=[];
<?php
for($i=0; $i<$cnt_user; $i++){
	echo "user[".$i."]=[];\n";
	echo "user[".$i."]['id']='" . $user_id[$i]  . "';\n";
	echo "user[".$i."]['name']='" . $user_name[$i]  . "';\n";
	echo "user[".$i."]['email']='" . $user_email[$i]  . "';\n";
}
?>
var guiuser=[];
<?php
for($i=0; $i<$cnt_gui_user; $i++){
	echo "guiuser[".$i."]=[];\n";
	echo "guiuser[".$i."]['id']='" . $user_id_gui[$i]  . "';\n";
	echo "guiuser[".$i."]['name']='" . $user_name_gui[$i]  . "';\n";
	echo "guiuser[".$i."]['email']='" . $user_email_gui[$i]  . "';\n";
}
?>
// -->
</script>
</body>
</html>