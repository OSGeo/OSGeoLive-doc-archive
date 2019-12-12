<?php
# $Id: mod_deleteWFS.php 7643 2011-02-24 10:17:05Z armin11 $
# http://www.mapbender.org/index.php/DeleteWFS
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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

$e_id="deleteWFS";
$gui_id = Mapbender::session()->get("mb_user_gui");
/*  
 * @security_patch irv done
 */ 
//security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");
#require_once(dirname(__FILE__)."/../../conf/mapbender.conf");
#$con = db_connect($DBSERVER,$OWNER,$PW);
#db_select_db(DB,$con);
#require_once(dirname(__FILE__)."/../classes/class_administration.php");
#$admin = new administration();
#$ownguis = $admin->getGuisByOwner($_SESSION["mb_user_id"],true);

$wfsList = $_POST["wfsList"];
$del = $_POST["del"];

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
<title>Delete WFS - All Users</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
function validate(){
	var ind = document.form1.wfsList.selectedIndex;
	var wfsData = document.form1.wfsList.options[ind].value.split("###");
	if(ind > -1){
		var permission =  confirm("delete: "  + wfsData[0] + " ?");
		if(permission === true){
			document.form1.del.value = wfsData[0];
			document.form1.submit();
		}
	}
}






function sel(){
	var ind = document.form1.wfsList.selectedIndex;
	var wfsData = document.form1.wfsList.options[ind].value.split("###");
	var i,wfsConfList,wfsConfLinkList;
	var wfsConfList = wfsData[1].split(",");
	//document.form1.capURL.value = wmsData[1];
	//document.form1.myWMS.value = wmsData[0];
	//new for showing metadata - 30.05.2008 AR
	document.getElementById("metadatalink").href = "mod_wfsMetadata.php?wfs_id="+wfsData[0];
	document.getElementById("metadatatext").firstChild.nodeValue = "WFS-ID: "+wfsData[0];
	//delete all childs of wfsconflist
	var countChild = document.getElementById("wfsconflist").childNodes.length;
	for (i = 0; i < countChild; ++i) {
			document.getElementById("wfsconflist").removeChild(document.getElementById("wfsconflist").childNodes[0]);
	}
	//document.getElementById("wfsconflist").removeChild(document.getElementById("wfsconflist").firstChild);
	if (wfsData[1] != "") {
	for (i = 0; i < wfsConfList.length; ++i) {
		//create anchors:
		var newAnchor = document.createElement("a");
		//var newBr = document.createElement("br");
		var newSpace = document.createTextNode(" | ");
		var newAnchorText = document.createTextNode("WFS-CONF: " + wfsConfList[i]);
		//set anchor href
		newAnchor.setAttribute("href", "mod_featuretypeMetadata.php?wfs_conf_id=" + wfsConfList[i]);
		newAnchor.setAttribute("onclick","window.open(this.href,'Metadaten','width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes'); return false");
		newAnchor.appendChild(newAnchorText);
		//newAnchor.appendChild(newSpace);
		//append childs
		document.getElementById("wfsconflist").appendChild(newAnchor);
		document.getElementById("wfsconflist").appendChild(newSpace);
  	}
	//delete last komma
	}
	document.getElementById("guilist").firstChild.nodeValue = wfsData[2];
	//ggf. create child 
	//document.getElementById("wfsConfTable").firstChild.nodeValue = wfsConfLinkList;
	//end ***
}

</script>

<style type="text/css">
  	<!--
  	body{
      background-color: #ffffff;
  		font-family: Arial, Helvetica, sans-serif;
  		font-size : 12px;
  		color: #808080
  	}
  	
  	-->
</style>
</head>
<body>

<?php
$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");

###delete

if($del!='-1'){
	 $sql = "DELETE FROM wfs WHERE wfs_id = '".$del."'";
	 $res = db_query($sql);
}



//adopted for owned wfs and not all!!!!!!

$sql_wfs = "SELECT * FROM wfs ";
$sql_wfs .= " where wfs_owner=".$_SESSION["mb_user_id"]." ORDER BY wfs_name";
$res_wfs = db_query($sql_wfs);
$cnt_wfs = 0;

echo "<form name='form1' action='' method='post'>"; //" . $_SERVER["SCRIPT_NAME"] . "?".SID."
echo "<br><b>WFS List: <b><br><br>";
echo "<select class='wfsList' size='20' name='wfsList'  onchange='sel();'>";
//var wfsInfo = this.value.split(\"###\");document.form1.wfsList.value=wfsInfo[0];
//morgen zu kontrollieren


while($row1 = db_fetch_array($res_wfs)){
	
	$wfs_conf_gui=array();
	//$wfs_conf_gui_single=array();	
	$wfs_conf_id=array();

	//get wfs_conf information by wfs_id
	$sql_wfs_conf = "SELECT  wfs_conf_id, wfs_conf_abstract from wfs_conf where fkey_wfs_id=".$row1["wfs_id"]."";
	$res_wfs_conf = db_query($sql_wfs_conf);
	$cnt_wfs_conf=0;

	while($row2 = db_fetch_array($res_wfs_conf)){
		//$wfs_conf[$cnt_wfs_conf]=$row2["wfs_conf_id"];
		array_push($wfs_conf_id,$row2["wfs_conf_id"]);		
		//get GUI list assigned to wfs_conf
		$sql_wfs_conf_gui = "select fkey_gui_id from gui_element_vars where var_name = 'wfsConfIdString' and ',' || var_value || ',' like '%,".$row2["wfs_conf_id"].",%'";
		$res_wfs_conf_gui = db_query($sql_wfs_conf_gui);
		$cnt_wfs_gui=0;
		while($row3 = db_fetch_array($res_wfs_conf_gui)){
			array_push($wfs_conf_gui,$row3["fkey_gui_id"]);
			//$wfs_conf_gui[$cnt_wfs_gui]=$row3["wfs_conf_id"];
			$cnt_wfs_gui++;
		}
		$cnt_wfs_conf++;
	}
	//make the entries unique
	$unique_wfs_conf=array_unique($wfs_conf_id);
	$unique_wfs_gui=array_unique($wfs_conf_gui);
	//create lists:
	$str_wfs_conf="";
	$str_wfs_gui="";

	for ($i = 0; $i < count($unique_wfs_conf); $i++) {
    		$str_wfs_conf.=",".$unique_wfs_conf[$i];
	}
	$str_wfs_conf=ltrim($str_wfs_conf, ",");

	for ($i = 0; $i < count($unique_wfs_gui); $i++) {
    		$str_wfs_gui.=",".$unique_wfs_gui[$i];
	}
	$str_wfs_gui=ltrim($str_wfs_gui, ",");

	//output to table
	echo "<option value='".$row1["wfs_id"]."###".$str_wfs_conf."###".$str_wfs_gui."'>".$row1["wfs_id"]." ".$row1["wfs_name"]." - ".$row1["wfs_title"]."</option>";
	$cnt_wfs++;
}

echo "</select><br><br>";



?>
<!--Line for showing wfs metadata-->
	View wfs metadata: <a id='metadatalink' href='' onclick="window.open(this.href,'Metadata','width=500,height=600,left=100,top=200,scrollbars=yes ,dependent=yes'); return false" target="_blank"><span id="metadatatext">no WFS selected</span></a><br><br>
List of dependend wfs_conf:
 <div id="wfsconflist"> no WFS selected</div><br><br>


List of GUIs where dependend wfs_conf are used:<span id="guilist"> no WFS selected</span><br><br>
<input type='hidden' name='del' value='-1'>
<?php

//kann ja nur beim neu laden passieren. Man sollte hier dynamisch was hineinsetzen lassen(javascript) dafuer muss es aber schon vorher ausgewaehlt worden sein, d.h. die options um titel_list, abstarct_list, wfs_conf_id_list, sowie GUI_list erweitern lassen. is aufwand!
//Liste der wfs_conf's 
//SELECT wfs_conf_id, wfs_conf_abstract from wfs_conf where fkey_wfs_id=...
//hier muss tabelle angezeigt werden
//Liste der GUI's die eine WFS Conf des WFS enthalten
//select fkey_gui_id from gui_element_vars where var_name = 'wfsConfIdString' and ',' || var_value || ',' like '%,54,%'
//muss iterativ ueber or verknuepft werden!



echo "<input class='button_del' type='button' value='delete' onclick='validate()'>";
?>

</form>


</body>
</html>
