<?php
# $Id: mod_loadWFSCapabilities.php 7234 2010-12-11 16:01:43Z apour $
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

$e_id="loadWFS";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");

$guiList = $_POST["guiList"];
$wfsList = $_POST["wfsList"];
$xml_file = $_POST["xml_file"];

require_once(dirname(__FILE__)."/../classes/class_administration.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Load WFS Capabilities</title>
<?php
include '../include/dyn_css.php';
?>
<style type="text/css">
  	<!--
  	body{
      background-color: #ffffff;
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		color: #808080
  	}
  	.list_guis{
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		color: #808080;
  	}
  	a:link{
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		text-decoration : none;
  		color: #808080;
  	}
  	a:visited {
  		font-family: Arial, Helvetica, sans-serif;
  		text-decoration : none;
  		color: #808080;
  		font-size : 12px;
  	}
  	a:active {
  		font-family: Arial, Helvetica, sans-serif;
  		text-decoration : none;
  		color: #808080;
  		font-size : 12px;
  	}
  	-->
</style>
<script language="JavaScript">
function validate(wert){
   if(wert == 'guiList'){
      var listIndex = document.form1.guiList.selectedIndex;
      if(listIndex<0){
		   alert("Please select a GUI.");
			return false;
      }
      else{
         var gui_id=document.form1.guiList.options[listIndex].value;
			document.form1.action='../php/mod_loadwfs.php?<?php echo $urlParameters;?>';
			document.form1.submit();
      }
   }
}
</script>
</head>
<body>

<?php
$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
echo count($ownguis)."<br>";
echo "<form name='form1' action='" . $self ."' method='post'>";
echo "<table cellpadding='0' cellspacing='0' border='0'>";
echo "<tr>";
echo "<td>";
echo"GUI";
echo"<br>";
$gui_id =array();
if (count($ownguis)>0){
	for($i=0; $i<count($ownguis); $i++){
		$gui_id[$i]=$ownguis[$i];
	}
}

  echo"<select size='8' name='guiList' style='width:200px' onClick='submit()'>";
	for ($i=0; $i<count($ownguis);$i++){
   		echo "<option value='".$gui_id[$i]."' ";
	   if($guiList && $guiList == $gui_id[$i]){
	      echo "selected";
	      $selected_gui_id=$gui_id[$i];
	   }
	   else{
	      if ($i==0){
	         echo "selected";
	         $selected_gui_id=$gui_id[$i];
			}
	   }
	   echo ">".$gui_id[$i]."</option>";
   }
echo "</select><br><br>";

echo "</td>";
echo "<td>";
echo"<br>";


if(isset($guiList) && $guiList!=""){
	$sql = "SELECT Distinct wfs.wfs_title from gui_wfs LEFT JOIN wfs ON gui_wfs.fkey_wfs_id=wfs.wfs_id ";
	$sql .= "where gui_wfs.fkey_gui_id = $1 order by wfs.wfs_title";
	$v = array($guiList);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);

  $count=0;
  echo"<select size='8' name='wfsList' style='width:200px'>";

  while($row = db_fetch_array($res)){
  	if ($row["wfs_title"]!=""){
	echo "<option value='' ";
    echo ">".$row["wfs_title"]."</option>";
	}
	$count++;
  }
    echo "</select><br><br>";
}
echo "</td>";
echo "<tr></table><br>";
echo "Add the following REQUEST to the Online Resource URL to obtain the Capabilities document:<br>";
echo "<i>(Triple click to select and copy)</i><br>"; 
echo "REQUEST=GetCapabilities&VERSION=1.0.0&SERVICE=WFS<br>";
echo "REQUEST=GetCapabilities&VERSION=1.1.0&SERVICE=WFS<br>";
echo "<br><br>";
echo "Link to WFS Capabilities URL:<br>";

if (isset($xml_file)){
	echo"<input type='text' name='xml_file' size='50' value='".$xml_file."'>";
}else{
	echo"<input type='text' name='xml_file' size='50' value='http://'>";
}
echo"<input type='button' name='loadCap' value='Load' onClick='validate(\"guiList\")'>";
echo "</form>";
?>
</body>
</html>
