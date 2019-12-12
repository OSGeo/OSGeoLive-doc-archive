<?php
# $Id: mod_deleteGUI.php 7245 2010-12-12 08:38:08Z tbaschetti $
# http://www.mapbender.org/index.php/DeleteGUI
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
# Foundation, Inc., 59 Temple Place 

$e_id="deleteGui";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

/*  
 * @security_patch irv done
 */ 
//import_request_variables("PG");
$guiList=$_POST["guiList"];
$del=$_POST["del"];
require_once(dirname(__FILE__)."/../classes/class_administration.php");
security_patch_log(__FILE__,__LINE__); 
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
<title>Delete GUI - All Users</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
function validate(){
	var ind = document.form1.guiList.selectedIndex;
	if(ind > -1){
		var permission =  confirm("delete: " + document.form1.guiList.options[ind].text + " ?");
		if(permission === true){
			document.form1.del.value = 1;
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>

<?php
require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");
$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");

###delete
if($guiList){
	 $sql = "DELETE FROM gui WHERE gui_id = $1";
	 $v = array($guiList);
	 $t = array("s");
	 $res = db_prep_query($sql, $v, $t);
}

$sql_gui = "SELECT * FROM gui ORDER BY gui_name";
$res_gui = db_query($sql_gui);
$cnt_gui = 0;

echo "<form name='form1' action='" . $self ."' method='post'>"; 
echo "<select class='guiList' size='20' name='guiList' class='guiList'>";

while($row = db_fetch_array($res_gui)){
	echo "<option value='".$row["gui_id"]."'>".$row["gui_name"]."</option>";
	$cnt_gui++;
}
echo "</select><br>";
echo "<input class='button_del' type='button' value='delete' onclick='validate()'>";
?>
<input type='hidden' name='del'>
</form>
</body>
</html>
