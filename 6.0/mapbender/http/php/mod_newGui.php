<?php
# $Id: mod_newGui.php 7205 2010-12-11 11:41:33Z tbaschetti $
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

$e_id="newGui";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$newGui=$_POST["newGui"];
$newDesc=$_POST["newDesc"];


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
<title>New GUI</title>
<?php include '../include/dyn_css.php'; ?>
<?php
if(isset($newGui) && $newGui != ""){
  $sql = "SELECT gui_id FROM gui WHERE gui_id = $1";
  $v = array($newGui);
  $t = array('s');
  $res = db_prep_query($sql,$v,$t);
  if(db_fetch_row($res)){
     echo "<script type='text/javascript'>";
     echo "alert('Error: Gui already exists!');";
     echo "</script>";
  }
  else{
	$sql = "INSERT INTO gui (gui_id,gui_name,gui_description,gui_public) ";
	$sql .= "VALUES($1, $2, $3, $4)";
	$v = array($newGui,$newGui,$newDesc,1);
	$t = array('s','s','s','i');
	$res = db_prep_query($sql,$v,$t);
	$sql = "INSERT INTO gui_mb_user (fkey_gui_id,fkey_mb_user_id,mb_user_type) ";
	$sql .= "VALUES($1, $2, $3)";
	$v = array($newGui,Mapbender::session()->get("mb_user_id"), 'owner');
	$t = array('s','i','s');
	$res = db_prep_query($sql,$v,$t);
	require_once(dirname(__FILE__)."/mb_getGUIs.php");
	$arrayGUIs = mb_getGUIs( Mapbender::session()->get("mb_user_id"));
	Mapbender::session()->set("mb_user_guis",$arrayGUIs);
	$guiCreated=true;
  }
}
?>
<script type="text/javascript">
<!--
function setFocus(){
	document.form1.newGui.focus();
}
function validate(){
	if(document.form1.newGui.value == ""){
		alert("Please enter a GUI-NAME!");
		document.form1.newGui.focus();
		return;
	}
	else if(document.form1.newDesc.value == ""){
		alert("Please enter a GUI-Description!");
		document.form1.newDesc.focus();
		return;
	}
	else{
		document.form1.submit();
	}
}
// -->
</script>
</head>
<body onload='setFocus()'>
<form name='form1' action="<?php echo $self; ?>" method="POST">
<table>
<tr><td>Name: </td><td><input type='text' name='newGui'></td></tr>
<tr><td>Description: </td><td><input type='text' name='newDesc'></td></tr>
<tr><td></td><td><input type='button' onclick='validate()' value="new"></td></tr>
</table>

<?php
if(isset($newGui) && $newGui != ""){
	if ($guiCreated==true){
		echo "<p class = 'guiList'>";
		echo "The GUI <b>".$newGui."</b> has been created successfully.";
		echo "<p>";
	}
}
?>
</form>
</body>
</html>