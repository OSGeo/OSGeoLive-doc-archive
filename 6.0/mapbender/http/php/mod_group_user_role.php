<?php
# $Id: mod_group_user.php 7276 2010-12-12 10:36:40Z apour $
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

$e_id="Group_User_Role";
require_once(dirname(__FILE__)."/../classes/class_mb_exception.php");
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
//require_once(dirname(__FILE__)."/../core/globalSettings.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$postvars = explode(",", "selected_group,filter2,insert,remove,filter3,remove_user,selected_user,select_role,alterrole");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Edit Group Members</title>
<?php include '../include/dyn_css.php'; ?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_group"].selectedIndex == -1){
		document.getElementsByName("selected_group")[0].style.backgroundColor = '#ff0000';
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
		if(wert == "showrole"){
			document.forms[0]["select_role"].disabled=false;
			var removeUser = document.forms[0]["remove_user[]"].value;
			var removeUserArray = removeUser.split("+");
			//alert(removeUserArray[1]);
			for(index = 0; index < document.forms[0]["select_role"].length; index++) {
   				if(document.forms[0]["select_role"][index].value == removeUserArray[1]) {
     					document.forms[0]["select_role[]"].selectedIndex = index;
   				}
			}
		}
		if(wert == "changerole"){
			document.form1.alterrole.value = 'true';
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
function showAvailableRoles(){
	
}
function selectRole(){

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
$cnt_role = 0;
$exists = false;

/*handle remove, update and insert**************************************************************************************/
if($insert){
	if(count($selected_user)>0){
		for($i=0; $i<count($selected_user); $i++){
			$exists = false;
			//check if a user is already in this group a standard role
			$sql = "SELECT * from mb_user_mb_group where fkey_mb_group_id = $1 and fkey_mb_user_id = $2 and mb_user_mb_group_type = $3";
			$v = array($selected_group,$selected_user[$i],1);
			$t = array('i','i','i');
			$res_insert = db_prep_query($sql,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				//add a user without any special role to the selected group
				$sql = "INSERT INTO mb_user_mb_group(fkey_mb_group_id, fkey_mb_user_id) ";
				$sql .= "VALUES($1, $2);";
				$v = array($selected_group,$selected_user[$i]);
				$t = array('i','i');
				$res = db_prep_query($sql,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_user)>0){
		for($i=0; $i<count($remove_user); $i++){
			$sql_remove = "DELETE FROM mb_user_mb_group WHERE ";
			$sql_remove .= "fkey_mb_user_id = $1 and fkey_mb_group_id = $2 AND mb_user_mb_group_type = $3";
			//explode identifier for selected user option 
			$remove_user_explode = explode('+',$remove_user[$i]);
			$v = array($remove_user_explode[0],$selected_group,$remove_user_explode[1]);
			$t = array('i','i','i');
			db_prep_query($sql_remove,$v,$t);
		}
	}
}
if($alterrole){
	$e = new mb_exception("alterrole send");
	if(count($remove_user)==1){
		$e = new mb_exception("only one user selected");
		//update role of the selected user-role combination - if it doesn't exists at all!
		//check if combi exists
		$exists = false;
		//check if a user is already in this group without that role
		$sql = "SELECT * from mb_user_mb_group where fkey_mb_group_id = $1 and fkey_mb_user_id = $2 and mb_user_mb_group_type = $3";
		//explode user-role combi to single elements userId and roleId
		$remove_user_explode = explode('+',$remove_user[0]);
		$v = array($selected_group,$remove_user_explode[0],$select_role[0]);
		$t = array('i','i','i');
		$res_insert = db_prep_query($sql,$v,$t);
		while(db_fetch_row($res_insert)){$exists = true;}
		if($exists == false){
			//the given one will be updated
			$e = new mb_exception("Requested combination user_group_role does not exist in database - update will be performed");
			//change this role
			$sql_alterrole = "UPDATE mb_user_mb_group SET mb_user_mb_group_type = $1 WHERE ";
			$sql_alterrole .= "fkey_mb_user_id = $2 and fkey_mb_group_id = $3 AND mb_user_mb_group_type = $4";
			$e = new mb_exception("select_row: ".$select_row);
			$v = array($select_role[0],$remove_user_explode[0],$selected_group,$remove_user_explode[1]);
			$t = array('i','i','i','i');
			db_prep_query($sql_alterrole,$v,$t);
		} else {
			$e = new mb_exception("Combi user_group_role does already exist in the database - it need not to be added again!");
		}
	}
}

/*get all group  ********************************************************************************************/
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
	$user_name[$cnt_user] =  $row["mb_user_name"];
	$user_email[$cnt_user] = $row["mb_user_email"];	
	$cnt_user++;
}
/*get all roles **********************************************************************************************/
$sql_role = "SELECT * FROM mb_role ORDER BY role_name";
$res_role = db_query($sql_role);
while($row = db_fetch_array($res_role)){
	$role_id[$cnt_role] = $row["role_id"];
	$role_name[$cnt_role] =  $row["role_name"];
	$role_description[$cnt_role] = $row["role_description"];
	$role_exclude_auth[$cnt_role] = $row["role_exclude_auth"];
	$cnt_role++;
}
/*get all user from selected group******************************************************************************/
$sql_mb_user_mb_group = "SELECT mb_user.mb_user_id, mb_user.mb_user_name, "; $sql_mb_user_mb_group .= "mb_user.mb_user_email, mb_user_mb_group.fkey_mb_group_id, "; $sql_mb_user_mb_group .= "mb_user_mb_group.mb_user_mb_group_type, "; $sql_mb_user_mb_group .= "mb_user_mb_group.role_exclude_auth ";
$sql_mb_user_mb_group .= "FROM (select * from mb_user_mb_group left join mb_role on "; $sql_mb_user_mb_group .= "mb_user_mb_group.mb_user_mb_group_type = mb_role.role_id ) as "; $sql_mb_user_mb_group .= "mb_user_mb_group INNER JOIN mb_user ON  ";
$sql_mb_user_mb_group .= "mb_user_mb_group.fkey_mb_user_id = mb_user.mb_user_id  ";
$sql_mb_user_mb_group .= "WHERE mb_user_mb_group.fkey_mb_group_id= $1 ";
$sql_mb_user_mb_group .= "ORDER BY mb_user.mb_user_name ";



/*$sql_mb_user_mb_group = "SELECT mb_user.mb_user_id, mb_user.mb_user_name, mb_user.mb_user_email, mb_user_mb_group.fkey_mb_group_id, mb_user_mb_group.mb_user_mb_group_type FROM mb_user_mb_group ";
$sql_mb_user_mb_group .= "INNER JOIN mb_user ON mb_user_mb_group.fkey_mb_user_id = mb_user.mb_user_id ";
$sql_mb_user_mb_group .= "WHERE mb_user_mb_group.fkey_mb_group_id= $1 ";
$sql_mb_user_mb_group .= " ORDER BY mb_user.mb_user_name";
*/
if(!$selected_group){$v = array($group_id[0]);}
if($selected_group){$v = array($selected_group);}
$t = array('i');

$res_mb_user_mb_group = db_prep_query($sql_mb_user_mb_group,$v,$t);
while($row = db_fetch_array($res_mb_user_mb_group)){
	$user_id_group[$cnt_group_user] = $row["mb_user_id"];
	$user_name_group[$cnt_group_user] =  $row["mb_user_name"];
	$user_email_group[$cnt_group_user] =  $row["mb_user_email"];
	$user_group_type_group[$cnt_group_user] =  $row["mb_user_mb_group_type"];
	$user_role_exclude_auth_group[$cnt_group_user] =  $row["role_exclude_auth"];
	$cnt_group_user++;
}


/*INSERT HTML*/
echo "<form name='form1' action='" . $self . "' method='post'>";

/*insert projects in selectbox*************************************************************************************/
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

/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter2' id='filter2' name='filter2' onkeyup='filterUser(document.getElementById(\"selecteduser\"),user,this.value);'/>";
/*insert all profiles in selectbox*****************************************************************/
echo "<div class='text2'>USER:</div>";
echo "<select style='background:#ffffff' onchange='updateMail(this, user)' class='select2' multiple='multiple' id='selecteduser' name='selected_user[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_user; $i++){
	echo "<option value='" . $user_id[$i]  . "' title='".$user_email[$i]."'>" . $user_name[$i]  . "</option>";
}
echo "</select>";

/*Button****************************************************************************************************/

echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
echo "<input type='hidden' name='insert'>";

echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
echo "<input type='hidden' name='remove'>";
echo "<input type='hidden' name='alterrole'>";
/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter3' id='filter3' name='filter3' onkeyup='filterUser(document.getElementById(\"removeuser\"),groupuser,this.value);'/>";
/*insert container_profile_dependence and container_group_dependence in selectbox******************/
echo "<div class='text3'>SELECTED USER:</div>";
echo "<select style='background:#ffffff' onchange='validate(\"showrole\")' class='select3' name='remove_user[]' id='removeuser' size='$fieldHeight' >";
for($i=0; $i<$cnt_group_user; $i++){
	if ($user_role_exclude_auth_group[$i] == 1) {
		echo "<option value='".$user_id_group[$i]."+".$user_group_type_group[$i]."' title='".$user_email_group[$i]."' style='background-color: Red;'>" . $user_name_group[$i]  . " - " .$user_group_type_group[$i]. "</option>";
	} else {
		echo "<option value='".$user_id_group[$i]."+".$user_group_type_group[$i]."' title='".$user_email_group[$i]."'>" . $user_name_group[$i]  . " - " .$user_group_type_group[$i]. "</option>";
	}
}
echo "</select>";
echo "<div class='roleHeader'>SELECTED ROLE:</div>";
echo "<select style='background:#ffffff' onchange='validate(\"changerole\")' class='selectRole' name='select_role[]' id='select_role' size='1' disabled='true'>";
//echo "<option value='0' title='no role defined'>no special role defined yet!</option>";
for($i=0; $i<$cnt_role; $i++){
	echo "<option value='" . $role_id[$i]  . "' title='".$role_id[$i]." - ".$role_description[$i]."'>".$role_id[$i]." - ". $role_name[$i]  . "</option>";
}
echo "</select>";
echo "<table><tr><td bgcolor=\"#FF0000\">Role has no influence on authorization!</td></tr></table>";
echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_group.focus();
var user=[];
<?php
for($i=0; $i<$cnt_user; $i++){
	echo "user[".$i."]=[];\n";
	echo "user[".$i."]['id']='" . $user_id[$i]  . "';\n";
	echo "user[".$i."]['name']='" . $user_name[$i]  . "';\n";
	echo "user[".$i."]['email']='" . $user_email[$i]  . "';\n";
}
?>
var groupuser=[];
<?php
for($i=0; $i<$cnt_group_user; $i++){
	echo "groupuser[".$i."]=[];\n";
	echo "groupuser[".$i."]['id']='" . $user_id_group[$i]  . "';\n";
	echo "groupuser[".$i."]['name']='" . $user_name_group[$i]  . "';\n";
	echo "groupuser[".$i."]['email']='" . $user_email_group[$i]  . "';\n";
	echo "groupuser[".$i."]['group_type']='" . $user_group_type_group[$i]  . "';\n";
}
?>
var groupuserrole=[];
<?php
for($i=0; $i<$cnt_role; $i++){
	echo "groupuserrole[".$i."]=[];\n";
	echo "groupuserrole[".$i."]['id']='" . $role_id[$i]  . "';\n";
	echo "groupuserrole[".$i."]['name']='" . $role_name[$i]  . "';\n";
	echo "groupuserrole[".$i."]['description']='" . $role_description[$i]  . "';\n";
}
?>
// -->
</script>
</body>
</html>
