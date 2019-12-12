<?php
# $Id: mod_group_user.php 7706 2011-03-15 13:44:54Z armin11 $
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
$e_id = "allowPublishMetadata";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
require_once(dirname(__FILE__)."/../../core/globalSettings.php");
//include variable for registrating departments - this are the php element vars:
//$authorizeRoleId
//$adminGroupId
include '../include/dyn_php.php';
//group id of the users who have the right to registrate services
//$adminGroupId = 25;
//id of the role which will be given to the selected user - here metadata editor
//$authorizeRoleId = 3;
//here the primary group of the user is relevant therefor it will be a good idea to show the group names which are primary roles to some registrating user
//get the selected group as the primary group of the current user!
//get user id:
$userId = (integer)$_SESSION["mb_user_id"];
if (!is_int($userId)) {
	echo _mb("You have no rights to use this module!");	
	die();
}
$sqlGetPrimaryGroup = "SELECT fkey_mb_group_id FROM mb_user_mb_group WHERE fkey_mb_user_id = $1 and mb_user_mb_group_type = 2";
$v = array($userId);
$t = array('i');
$resPrimaryGroup = db_prep_query($sqlGetPrimaryGroup,$v,$t);
$cntPG = 0;
while($row = db_fetch_array($resPrimaryGroup)){
	$selected_group = $row["fkey_mb_group_id"];
	$cntPG++;
}

if ($cntPG > 1) {
	echo _mb("There are too many primary groups defined for the user with ID: ").$userId;
	die();
}
if ($cntPG == 0) {
//give an error if now primary_group is defined for the current user
	echo _mb("You have not yet an primary group. Please contact the central system admin to define a primary group for user with ID: ").$userId;
	die();
}

//get only those users which have the right to registrate and have a primary role defined - they are the metadata publishers in sense of the registry


/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$postvars = explode(",", "filter2,insert,remove,filter3,remove_user,selected_user");
foreach ($postvars as $value) {
   $$value = $_POST[$value];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Allow departments to publish own metadata</title>
<?php include '../include/dyn_css.php'; ?>

<script language="JavaScript">
function validate(wert){
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

/*handle remove, update and insert**************************************************************************************/
if($insert){
	if(count($selected_user)>0){
		for($i=0; $i<count($selected_user); $i++){
			$exists = false;
			$sql = "SELECT * from mb_user_mb_group where fkey_mb_group_id = $1 AND fkey_mb_user_id = $2 AND mb_user_mb_group_type = $3";
			$v = array($selected_group,$selected_user[$i],$authorizeRoleId);
			$t = array('i','i','i');
			$res_insert = db_prep_query($sql,$v,$t);

			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				//allow the user to be a metadata editor
				$sql = "INSERT INTO mb_user_mb_group(fkey_mb_group_id, fkey_mb_user_id, mb_user_mb_group_type) ";
				$sql .= "VALUES($1, $2, $3)";

				$v = array($selected_group,$selected_user[$i], $authorizeRoleId);
				$t = array('i','i','i');
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
			$v = array($remove_user[$i],$selected_group, $authorizeRoleId);
			$t = array('i','i','i');
			db_prep_query($sql_remove,$v,$t);
			//delete all references from owned services of this user to metadata point of contact 
			$sql_wms_update = "UPDATE wms SET fkey_mb_group_id = 0 WHERE wms_owner = $1 AND fkey_mb_group_id = $2";
			$v = array($remove_user[$i],$selected_group);
			$t = array('i','i');
			db_prep_query($sql_wms_update,$v,$t);
			$sql_wfs_update = "UPDATE wfs SET fkey_mb_group_id = 0 WHERE wfs_owner = $1 AND fkey_mb_group_id = $2";
			$v = array($remove_user[$i],$selected_group);
			$t = array('i','i');
			db_prep_query($sql_wfs_update,$v,$t);
		}
	}
}



/*get all user which have the right to load services and have assigned a primary group! **********************************************************************************************/
$sql_user = "SELECT b.mb_user_id, b.mb_user_name, b.mb_group_name, b.mb_user_email FROM ( ";
$sql_user .= "SELECT mb_group_name, mb_user_name, mb_user_email, mb_user_id , fkey_mb_group_id FROM ";
$sql_user .= "(SELECT mb_user.mb_user_id, mb_user.mb_user_name, mb_user.mb_user_email, "; $sql_user .= "mb_user_mb_group.fkey_mb_group_id FROM ";
$sql_user .= "mb_user_mb_group INNER JOIN mb_user ON ";
$sql_user .= "mb_user_mb_group.fkey_mb_user_id = mb_user.mb_user_id WHERE ";
$sql_user .= "mb_user_mb_group.mb_user_mb_group_type = 2 ) as a INNER JOIN ";
$sql_user .= "mb_group ON mb_group.mb_group_id = a.fkey_mb_group_id  ";
$sql_user .= ") AS b ";
$sql_user .= "INNER JOIN mb_user_mb_group ON 	mb_user_mb_group.fkey_mb_user_id = b.mb_user_id WHERE mb_user_mb_group.fkey_mb_group_id = $1 ";

$v = array($adminGroupId);
$t = array('i');
$res_user = db_prep_query($sql_user,$v,$t);

while($row = db_fetch_array($res_user)){
	$user_id[$cnt_user] = $row["mb_user_id"];
	$user_name[$cnt_user] = $row["mb_group_name"]." - ".$row["mb_user_name"];
	$user_email[$cnt_user] = $row["mb_user_email"];	
	$cnt_user++;
}

/*get all user which are metadata editors for my primary group ******************************************************************************/
$sql_mb_user_mb_group = "SELECT mb_group_name, mb_user_name, mb_user_email, mb_user_id  FROM ";
$sql_mb_user_mb_group .= "(SELECT  mb_user_name, mb_user_email, mb_user_id, ";
$sql_mb_user_mb_group .= "mb_user_mb_group.fkey_mb_group_id FROM (SELECT ";
$sql_mb_user_mb_group .= "mb_user.mb_user_id, mb_user.mb_user_name, mb_user.mb_user_email, ";
$sql_mb_user_mb_group .= "mb_user_mb_group.fkey_mb_group_id,mb_user_mb_group.mb_user_mb_group_type ";
$sql_mb_user_mb_group .= "FROM mb_user_mb_group INNER JOIN mb_user ON ";
$sql_mb_user_mb_group .= "mb_user_mb_group.fkey_mb_user_id = mb_user.mb_user_id ";
$sql_mb_user_mb_group .= "WHERE (mb_user_mb_group.mb_user_mb_group_type = $2 ) ";
$sql_mb_user_mb_group .= " AND mb_user_mb_group.fkey_mb_group_id = $1 ";
$sql_mb_user_mb_group .= ") as a INNER JOIN mb_user_mb_group ON ";
$sql_mb_user_mb_group .= "mb_user_mb_group.fkey_mb_user_id = a.mb_user_id WHERE ";
$sql_mb_user_mb_group .= "mb_user_mb_group.mb_user_mb_group_type = 2 ";
$sql_mb_user_mb_group .= ") as b , mb_group WHERE mb_group.mb_group_id = b.fkey_mb_group_id";

$v = array($selected_group, $authorizeRoleId);
$t = array('i','i');

$res_mb_user_mb_group = db_prep_query($sql_mb_user_mb_group,$v,$t);

while($row = db_fetch_array($res_mb_user_mb_group)){
	$user_id_group[$cnt_group_user] = $row["mb_user_id"];
	$user_name_group[$cnt_group_user] = $row["mb_group_name"]." - ".$row["mb_user_name"];
	$user_email_group[$cnt_group_user] =  $row["mb_user_email"];
	$cnt_group_user++;
}


/*INSERT HTML*/
echo "<form name='form1' action='" . $self . "' method='post'>";
/*filterbox*************************************************************************/
echo "<input type='text' value='' class='filter2' id='filter2' name='filter2' onkeyup='filterUser(document.getElementById(\"selecteduser\"),user,this.value);'/>";
/*insert all profiles in selectbox*****************************************************************/
echo "<div class='text2'>"._mb("Registrating institutions").":</div>";
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

/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter3' id='filter3' name='filter3' onkeyup='filterUser(document.getElementById(\"removeuser\"),groupuser,this.value);'/>";
/*insert container_profile_dependence and container_group_dependence in selectbox******************/
echo "<div class='text3'>"._mb("My (metadata-)provider").":</div>";
echo "<select style='background:#ffffff' onchange='updateMail(this, user)' class='select3' multiple='multiple' name='remove_user[]' id='removeuser' size='$fieldHeight' >";
for($i=0; $i<$cnt_group_user; $i++){
	echo "<option value='" . $user_id_group[$i]  . "' title='".$user_email_group[$i]."'>" . $user_name_group[$i]  . "</option>";
}
echo "</select>";

echo "</form>";

?>
<script type="text/javascript">

//document.forms[0].selected_group.focus();
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
}
?>

</script>
</body>
</html>
