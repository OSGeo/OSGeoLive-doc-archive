<?php
# $Id: mod_orphanWMS.php 7234 2010-12-11 16:01:43Z apour $
# http://www.mapbender.org/index.php/OrphanWMS
# Copyright (C) 2002 Melchior Moos 
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

$e_id="orphanWMS";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");
$wmsList = $_POST["wmsList"];
$del = $_POST["del"];


require_once(dirname(__FILE__)."/../classes/class_administration.php");
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
<title>Orphaned WMS</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">

function validate()
{
   var ind = document.form1.wmsList.selectedIndex;
   if(ind > -1) {
     var permission =  confirm("delete: " + document.form1.wmsList.options[ind].text + " ?");
     if(permission == true) {
        document.form1.del.value = 1;
        document.form1.submit();
     }
   }
}

-->
</script>
</head>
<body>
<?php
$admin = new administration();

$error_msg='';
{	
	// delete WMS
	if($del){
	   $sql = "DELETE FROM wms WHERE wms_id = $1";
	   $v = array($wmsList);
	   $t = array('i');
	   $res = db_prep_query($sql,$v,$t);
	}
	// display WMS List
	$sql = "SELECT * from wms WHERE wms_id NOT IN (select fkey_wms_id from gui_wms)";
	$res = db_query($sql);
	$cnt = 0;
	
	if (db_numrows($res)>0){
		echo "<form name='form1' action='" . $self ."' method='post'>";
		echo "<select class='wmsList' size='20' name='wmsList' onchange='document.form1.wmsList.value = this.value;submit()'>";
		while($row = db_fetch_array($res))
		{
			$wmsvalue = $row["wms_id"];
		   echo "<option value='".$wmsvalue."'" . (($wmsvalue == $wmsList)?" selected":"") . ">".$row["wms_title"]."</option>";
		   $cnt++;
		}
		echo "</select><br>";
	
	
		//
		//
		// If WMS is selected, show more info
		//
		//
		if($wmsList)
		{
			echo "<p class = 'guiList'>";
			
			// Show wms_id, GetCapabilities, Abstract of chosen WMS
			$sql = "SELECT wms_id,wms_abstract,wms_getcapabilities FROM wms WHERE wms_id = $1";
			$v = array($wmsList);
			$t = array('i');
			$res = db_prep_query($sql,$v,$t);

			$cnt = 0;
			while($row = db_fetch_array($res))
			{	
				echo "<b>wms_id:</b> ". $row["wms_id"]."<br>";
				echo "<br><b>GetCapabilities</b><br><br>";
				echo $row["wms_getcapabilities"]."<br>";
				echo "<br><b>Abstract</b><br><br>";
				echo $row["wms_abstract"]."<br>";
				$cnt++;
			}

			echo "</p>";
	
   			echo "<input class='button_del' type='button' value='delete' onclick='validate()'>";
		}
	}else{
		echo "There are no orphaned WMS.<br>";
	}
}
?>
<input type='hidden' name='del'>
</form>
</body>
</html>

