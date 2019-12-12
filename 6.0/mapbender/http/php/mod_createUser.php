<?php
# $Id: mod_createUser.php 7285 2010-12-12 10:54:24Z apour $
# http://www.mapbender.org/index.php/CreateUser
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

require_once dirname(__FILE__) . "/../../conf/mapbender.conf";
if (PORTAL !== true) {
	echo "This module is disabled. Please check your mapbender.conf.";
	die;
}

require_once(dirname(__FILE__)."/../classes/class_gui.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");

/*  
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$postvars = explode(",", "name,password,v_password,description,email,phone,department,action");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Create New Mapbender User</title>
<link rel="stylesheet" type="text/css" href="../css/login.css">
<?php
$myPW = "**********";
echo "<script language='JavaScript'>var myPW = '".$myPW."';</script>";
?>
<script language="JavaScript">

function validate(val){
	var ok = validateInput();
	if(ok == 'true'){
		var permission = false;
		if(val == 'save'){
			permission = confirm("Save changes?");
		}
		if(permission == true){
			if(document.forms[0].password.value == myPW){
				document.forms[0].password.value = '';
			}
			document.forms[0].action.value = val;
			document.forms[0].submit();
		}
	}
}

function validateInput(){
	var str_alert = "Input incorrect!";
	if(document.forms[0].name.value == ''){
		var str_alert = "Please enter a name.";
		alert(str_alert);
		document.forms[0].name.focus();
		return 'false';
	}
	if(document.forms[0].password.value == ''){
		var str_alert = "Please enter a password.";
		alert(str_alert);
		document.forms[0].password.focus();
		return 'false';
	}
	else if(document.forms[0].password.value != document.forms[0].v_password.value){
		alert("Password verification failed. Please enter the same password twice.");
		document.forms[0].password.focus();
		return 'false';
	}
	return 'true';
}
</script>
</head>
<body>

<?php
#save
if($action == 'save'){
	$owner_id =1;
	$resolution = 72;
	$login_count = 0;
	$sql = "SELECT mb_user_id FROM mb_user WHERE mb_user_name = $1 ";
	$v = array($name);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	if(db_fetch_row($res)){
		echo "<script language='JavaScript'>alert('Username must be unique!');</script>";
	}
	else{
		
		$sql = "Insert INTO mb_user (mb_user_name, mb_user_password,mb_user_owner, ";
		$sql .= "mb_user_description, mb_user_email, mb_user_phone, mb_user_department, ";
		$sql .= "mb_user_resolution) VALUES ";
		$sql.= "($1, $2, $3, $4, $5, $6, $7, $8)";
		$v = array($name,md5($password),$owner_id,$description,$email,$phone,$department,$resolution);
		$t = array('s','s','i','s','s','s','s','i');
		$res = db_prep_query($sql,$v,$t);
		
		$selected_user = db_insert_id('platzhalter','mb_user','mb_user_id');
		$sql_owner = "Update mb_user SET mb_user_owner = $1 ";
		$sql_owner.= " WHERE mb_user_name = $2 ";
		$v = array(1,$name); #$selected_user;
		$t = array('i','s');
		$res_owner = db_prep_query($sql_owner,$v,$t);	 
		# removed, because a new user may be inserted in a group with too many privileges
		$sql_group = "Insert INTO mb_user_mb_group (fkey_mb_user_id, fkey_mb_group_id) VALUES ";
		$sql_group.= "($1, $2) ";
		$v = array($selected_user,20);
		$t = array('i','i');
		$res_group = db_prep_query($sql_group,$v,$t);
		// CB (begin)
		// adding new GUIs for new user (copies of gui and gui1 with owner rights)
		$gui = new gui();
		$admin = new administration();
		//create new name for gui
		$gui_id1 = $admin->getGuiIdByGuiName("gui");
		$gui_id2 = $admin->getGuiIdByGuiName("gui1");
		$gui_id3 = $admin->getGuiIdByGuiName("gui2");
		$gui_id4 = $admin->getGuiIdByGuiName("gui_digitize");
		$gui_newName1 = $name . "_gui";
		$gui_newName2 = $name . "_gui1";
		$gui_newName3 = $name . "_gui2";
		$gui_newName4 = $name . "_gui_digitize";
		//check if new gui names are already taken
		while ($gui->guiExists($gui_newName1)) {
			$gui_newName1 .= "_1";
		}
		while ($gui->guiExists($gui_newName2)) {
			$gui_newName2 .= "_1";
		}
		while ($gui->guiExists($gui_newName3)) {
			$gui_newName3 .= "_1";
		}
		while ($gui->guiExists($gui_newName4)) {
			$gui_newName4 .= "_1";
		}
		//create gui_(name) and gui1_(name)
		$gui->copyGui($gui_id1[0], $gui_newName1,true);
		$gui->copyGui($gui_id2[0], $gui_newName2,true);
		$gui->copyGui($gui_id3[0], $gui_newName3,true);
		$gui->copyGui($gui_id4[0], $gui_newName4,true);
		$new_guiId1 = $admin->getGuiIdByGuiName($gui_newName1);
		$new_guiId2 = $admin->getGuiIdByGuiName($gui_newName2);
		$new_guiId3 = $admin->getGuiIdByGuiName($gui_newName3);
		$new_guiId4 = $admin->getGuiIdByGuiName($gui_newName4);
		//grant owner rights for new guis to this user only!
		$admin->delAllUsersOfGui($new_guiId1[0]);
		$admin->delAllUsersOfGui($new_guiId2[0]);
		$admin->delAllUsersOfGui($new_guiId3[0]);
		$admin->delAllUsersOfGui($new_guiId4[0]);
		$admin->insertUserAsGuiOwner($new_guiId1[0], $selected_user);
		$admin->insertUserAsGuiOwner($new_guiId2[0], $selected_user);
		$admin->insertUserAsGuiOwner($new_guiId3[0], $selected_user);
		$admin->insertUserAsGuiOwner($new_guiId4[0], $selected_user);
		// delete gui from groups
		// (gui and gui1 are associated with groups 20 and 21, new guis belong to mb_user only)
		$sql_del_from_group = "DELETE FROM gui_mb_group WHERE fkey_gui_id = $1 OR fkey_gui_id = $2 OR fkey_gui_id = $3 OR fkey_gui_id = $4";
		$v = array($new_guiId1[0],$new_guiId2[0],$new_guiId3[0],$new_guiId4[0]);
		$t = array('s','s','s','s');
		$res_del_from_group = db_prep_query($sql_del_from_group,$v,$t);	 
	}
}

if (!isset($name) || $selected_user == 'new'){
	$name = "";
	$password = "";
	$description = "";
	$email = "";
	$phone = "";
	$department = "";
}

/* HTML */
echo "<form name='form1' action='" . $_SERVER["SCRIPT_NAME"] . "' method='post'>";
echo "<table border='0'>";

if(isset($selected_user) && $selected_user != 0){
	$sql = "SELECT * FROM mb_user WHERE mb_user_id = $1 ORDER BY mb_user_name ";
	$v = array($selected_user);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	if(db_fetch_row($res)){
		$name = db_result($res,0,"mb_user_name");
		$password = db_result($res,0,"mb_user_password");
		$owner_id = db_result($res,0,"mb_user_owner");
		$description = db_result($res,0,"mb_user_description");
		$login_count = db_result($res,0,"mb_user_login_count");
		$email = db_result($res,0,"mb_user_email");
		$phone = db_result($res,0,"mb_user_phone");
		$department = db_result($res,0,"mb_user_department");
		$resolution = db_result($res,0,"mb_user_resolution");
	}
	$sql = "SELECT mb_user_name FROM mb_user WHERE mb_user_id = $1 ";
	$v = array($owner_id);
	$t = array('i');
	$res = db_prep_query($sql,$v,$t);
	if(db_fetch_row($res)){
		$owner_name = db_result($res,0,"mb_user_name");
	}
}
#name
echo "<tr>";
   echo "<td>Name*:</td>";
   echo "<td>";
      echo "<input type='text' size='30' name='name' value='".$name."'>";
   echo "</td>";
echo "</tr>";

#password
echo "<tr>";
	echo "<td>Password*: </td>";
	echo "<td>";
		echo "<input type='password' size='30' name='password' value='";
		if(isset($selected_user) && $selected_user != 'new'){
			echo $myPW;
		}
		echo "'>";
	echo "</td>";
echo "</tr>";

#password
echo "<tr>";
	echo "<td>Confirm password*: </td>";
	echo "<td>";
		echo "<input type='password' size='30' name='v_password' value='";
		echo "'>";
	echo "</td>";
echo "</tr>";

#description
echo "<tr>";
	echo "<td>Description: </td>";
	echo "<td>";
		echo "<input type='text' size='30' name='description' value='".$description."'>";
	echo "</td>";
echo "</tr>";

#email
echo "<tr>";
	echo "<td>Email <A HREF='http://wms1.ccgis.de/ewiki/index.php?id=CreateUser' target='_blank'><b>(Why?): </b></A><BR></td>";
	echo "<td>";
		echo "<input type='text' size='30' name='email' value='".$email."'>";
	echo "</td>";
echo "</tr>";

#phone
echo "<tr>";
	echo "<td>Phone: </td>";
	echo "<td>";
		echo "<input type='text' size='30' name='phone' value='".$phone."'>";
	echo "</td>";
echo "</tr>";

#department
echo "<tr>";
	echo "<td>Department: </td>";
	echo "<td>";
		echo "<input type='text' size='30' name='department' value='".$department."'>";
	echo "</td>";
echo "</tr>";

echo "<tr>";
	echo "<td></td>";
	echo "<td>";
if($selected_user == 'new' || !isset($selected_user)){
	echo "<input type='button' class='myButton' value='save'  onclick='validate(\"save\")'>";
}
	echo "</td>";
echo "</tr>";
echo"</table>";
?>
<input type='hidden' name='action' value=''>
</form>
</body>
</html>