<?php
# $Id: mod_exportGUI.php 7255 2010-12-12 09:52:43Z apour $
# http://www.mapbender.org/index.php/mod_exportGUI.php
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

$e_id="exportGUI";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done
 */

//security_patch_log(__FILE__,__LINE__);
$guiList=$_POST["guiList"];
$del=$_POST["export"];

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
<title>Export GUI</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
<!--
function validate(){
   var ind = document.form1.guiList.selectedIndex;
   if(ind > -1){
	   //alert (ind);
     var permission =  confirm("export: " + document.form1.guiList.options[ind].text + " ?");
     if(permission == true){
        document.form1.del.value = 1;
        document.form1.submit();
     }
   }
}
// -->
</script>
</head>
<body>

<?php

require_once dirname(__FILE__)."/../classes/class_administration.php";
require_once dirname(__FILE__)."/../classes/class_gui.php";

$admin = new administration();
$permguis = $admin->getGuisByPermission(Mapbender::session()->get("mb_user_id"),true);

 ###export

if($guiList){
	$gui = gui::byName($guiList);
	try {
		$insert = $gui->toSql();
	}
	catch (Exception $e) {
		$insert = $e->message;
	}
	
	echo "<textarea rows=40 cols=80>";
	echo htmlentities($insert, ENT_QUOTES, CHARSET); 
	echo "</textarea>";
}

###
if(!$guiList){
	$v = array();
	$t = array();
	$sql = "SELECT * FROM gui WHERE gui_id IN (";
	for($i=0; $i<count($permguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".($i + 1);
		array_push($v,$permguis[$i]);
		array_push($t,'s');
	}
	$sql .= ") ORDER BY gui_name";
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	echo "<form name='form1' action='" . $self ."' method='post'>";
	echo "<select class='guiList' size='20' name='guiList' class='guiList' onchange='document.forms[0].submit()'>";
	while($row = db_fetch_array($res)){
		print_r($row);
		echo "<option value='".$row["gui_id"]."'>".$row["gui_name"]."</option>";
		$cnt++;
	}
	echo "</select><br>";
}

?>
<input type='hidden' name='export'>
</form>
</body>
</html>
