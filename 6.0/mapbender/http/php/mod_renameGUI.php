m<?php
# $Id: mod_renameGUI.php 7262 2010-12-12 10:03:32Z christoph $
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

$e_id="rename_copy_Gui";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$guiList=$_POST["guiList"];
$newGuiName=$_POST["newGuiName"];
$withUsers=$_POST["withUsers"];
$rename=$_POST["rename"];
$copy=$_POST["copy"];
$withU=$_POST["withU"];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<meta name="author-mail" content="info@ccgis.de">
<meta name="author" content="U. Rothstein">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Rename GUI</title>
<style type="text/css">

body{
   background-color: #ffffff;
}
.button_rename{
   color: red;
   position:absolute;
   top:390px;
   left:10px;
}
.button_copy{
   color: red;
   position:absolute;
   top:390px;
   left:110px;
}
.button_copy_checkbox{
   color: red;
   position:absolute;
   top:390px;
   left:170px;
}
.newName{
   	position:absolute;
   	top:350px;
   	left:60px;
   	width:150px
} 
.newName_str{
   	position:absolute;
   	top:350px;
   	left:10px;
   	width:200px
} 
.guiList{
   	position:absolute;
   	top:10px;
   	left:10px;
   	width:200px
} 

</style>

<script type="text/javascript">


function validate_rename(){
	if(document.form1.newGuiName.value == ""){
		alert("Please enter a GUI name!");
		document.form1.newGuiName.focus();
	}
	else{
		// gui name already taken?
		var taken = false;
		for (var i=0; i<document.form1.guiList.options.length; i++){
			if(document.form1.guiList.options[i].text == document.form1.newGuiName.value){
				alert("GUI name already taken!");
				taken = true;
			}
		}    
		if (!taken){
			var ind = document.form1.guiList.selectedIndex;
			// check if any gui is selected
			if(ind > -1){
				var permission =  confirm("rename '" + document.form1.guiList.options[ind].text + "' to '" + document.form1.newGuiName.value + "' ?");
				if(permission == true){
					document.form1.rename.value = 1;
					document.form1.submit();
				}
			}
			else{
				alert("Please select a GUI!");
			}
		}
	}
}

function validate_copy(){
	document.form1.withU.value = document.form1.withUsers.checked;
	if(document.form1.newGuiName.value == ""){
		alert("Please enter a GUI name!");
		document.form1.newGuiName.focus();
	}
	else{
		// gui name already taken?
		var taken = false;
		for (var i=0; i<document.form1.guiList.options.length; i++){
			if(document.form1.guiList.options[i].text == document.form1.newGuiName.value){
				alert("GUI name already taken!");
				taken = true;
			}
		}
		if (!taken){
			var ind = document.form1.guiList.selectedIndex;
			// check if any gui is selected
			if(ind > -1){
				var permission =  confirm("copy '" + document.form1.guiList.options[ind].text + "' to '" + document.form1.newGuiName.value + "' ?");
				if(permission == true){
					document.form1.copy.value = 1;
					document.form1.submit();
				}
			} 
			else{
				alert("Please select a GUI!");
			}
		}
	}
}
</script>
</head>
<body>
<?php

require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_gui.php");

###rename
if($rename || $copy){

	$gui = new gui();

	if ($copy) {	
		if ($_POST['withU'] == 'true') $gui->copyGui($guiList, $newGuiName, true);
		else $gui->copyGui($guiList, $newGuiName, false);
	}
	elseif ($rename) {
		$gui->renameGui($guiList, $newGuiName);
	}
    $rename = 0;
    $copy = 0;
  
}
###
$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);

echo "<form name='form1' action='" . $self ."' method='post'>";
if(count($ownguis)>0){
	$sql = "SELECT * FROM gui WHERE gui_id IN (";
	$v = array();
	$t = array();
	for($i=0; $i<count($ownguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".($i+1);
		array_push($v,$ownguis[$i]);
		array_push($t,'s');
	}
	$sql .= ") ORDER BY gui_name";
	$res = db_prep_query($sql,$v,$t);
	$count=0;
	while($row = db_fetch_array($res)){
		$gui_id_[$count]=$row["gui_id"];
		$count++;
	}
	echo "<select class='guiList' size='20' name='guiList' onchange='document.form1.guiList.value = this.value;submit();'>";
	for ($i=0; $i<$count;$i++){
		echo "<option value='".$gui_id_[$i]."' ";
		if($guiList && $guiList == $gui_id_[$i]){
			echo "selected";
		}
		echo ">".$gui_id_[$i]."</option>";
	}
	echo "</select><br><br><br>";
}
else{
	echo "There are no guis owned by this user.";
}


if($guiList){
echo "<table>";
echo "<tr><td class='newName_str'>Name: </td><td><input class='newName' type='text' id='newGuiName' name='newGuiName'></td></tr>\n";
echo "<tr>";
echo " <td><input class='button_rename' type='button' value='rename' onclick='validate_rename()'></td>";
echo " <td><input class='button_copy' type='button' value='copy' onclick='validate_copy()'><div  class='button_copy_checkbox'>(<input name='withUsers' type='checkbox' /> copy users)</div></td>";
echo "</tr>\n";
echo "</table>";
}
?>
<input type='hidden' name='rename'>
<input type='hidden' name='copy'>
<input type='hidden' name='withU'>
</form>
</body>
</html>
