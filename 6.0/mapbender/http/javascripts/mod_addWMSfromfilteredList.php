<?php
# $Id: mod_addWMSfromfilteredList.php 6728 2010-08-10 08:31:29Z christoph $
# http://www.mapbender.org/index.php/mod_addWMSfromfilteredList.php
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
include '../include/dyn_css.php';
?>
<title>Add WMS from Filtered Catalog</title>
<link rel="stylesheet" type="text/css" href="../css/administration_alloc.css">

<STYLE TYPE="text/css">
<!--
body{
	font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size:10pt
}    
   
table{
	font-family:Verdana, Geneva, Arial, Helvetica, sans-serif;
	font-size:11;
}   

.wms_button{
	color: black;
	border: solid thin;
	height:22px;
	width:60px;
}

-->
</STYLE>


<script type="text/javascript">
<!--
function mod_addWMSfromDB(gui_id, wms_id) {
	//alert("1/3 mod_addWMSfromDB: GUI ID = " + gui_id + ", WMS ID = " + wms_id);
	window.opener.mod_addWMSById_load(gui_id, wms_id);
}
function mod_addWMSfromfilteredList(pointer_name,version){

	pointer_name=pointer_name + window.opener.mb_getConjunctionCharacter(pointer_name);
	
	if (version == '1.0.0'){
		var cap = pointer_name + "REQUEST=capabilities&WMTVER=1.0.0";
		var load = cap;
	}
	else if (version == '1.1.0'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.0";
		var load = cap;
	}
	else if (version == '1.1.1'){
		var cap = pointer_name + "REQUEST=GetCapabilities&SERVICE=WMS&VERSION=1.1.1";
		var load = cap;
	}  
	//alert (load);

	if(load){
		if(load.charAt(0) == '/' && load.charAt(1) == 'c'){
			window.opener.mod_addWMS_load('http://localhost' + load);
		}
		else{
			window.opener.mod_addWMS_load(load);
		}  
	}
}

function mod_show_group_wms(wert){
	document.form1.show_group_wms.value = wert;
	document.form1.submit();
}

function mod_show_gui_wms(wert2){
	document.form1.show_gui_wms.value = wert2;
	document.form1.submit();
}
			
function mod_show_gui_configured_wms(wert2){
	document.form1.show_gui_configured_wms.value = wert2;
	document.form1.submit();
}
			
function mod_show_wms(wert3){

	document.form1.wms_show.value = wert3;
   if (wert3 == 4) {
		document.form1.wmsSource.nodeValue = "db";
   }
	else {
		document.form1.wmsSource.nodeValue = "capabilities";
	}
	document.form1.submit();
}			

function setButtons(wms_option, wms_option2, wms_option3, wms_option4) {
	if (typeof(option_all) == "undefined") {
		option_all = '<?php echo $set_option_all;?>';
	}
	document.getElementById("set_option_all").value = option_all;
	if (typeof(option_group) == "undefined") {
		option_group = '<?php echo $set_option_group;?>';
	}
	document.getElementById("set_option_group").value = option_group;
	if (typeof(option_gui) == "undefined") {
		option_gui = '<?php echo $set_option_gui;?>';
	}
	document.getElementById("set_option_gui").value = option_gui;
	if (typeof(option_db) == "undefined") {
		option_db = '<?php echo $set_option_db;?>';
	}
	document.getElementById("set_option_db").value = option_db;

	if (wms_option == '' && wms_option2 == '' && wms_option3 == '' && wms_option4 == '') {
		if (option_all == '1') {
			mod_show_wms(1);
		}
		else if (option_group == '1') {
			mod_show_wms(2);
		}
		else if (option_gui == '1') {
			mod_show_wms(3);
		}
		else if (option_db == '1') {
			mod_show_wms(4);
		}
	}
	else {
		if (option_all == '0') {
			var aNode = document.getElementById("_option_all");
			if (aNode != null) removeChildNodes(aNode);
		}
		if (option_group == '0') {
			var aNode = document.getElementById("_option_group");
			if (aNode != null) removeChildNodes(aNode);
		}
		if (option_gui == '0') {
			var aNode = document.getElementById("_option_gui");
			if (aNode != null) removeChildNodes(aNode);
		}
		if (option_db == '0') {
			var aNode = document.getElementById("_option_db");
			if (aNode != null) removeChildNodes(aNode);
		}
	}
}

function removeChildNodes(node) {
	while (node.childNodes.length > 0) {
		var childNode = node.firstChild;
		node.removeChild(childNode);
	}
}
// -->
</script>

</head>
<body onLoad="window.focus();setButtons('<?php echo $wms_show;?>','<?php echo $show_gui_configured_wms;?>','<?php echo $show_group_wms;?>','<?php echo $show_gui_wms;?>')">

<?php
require_once(dirname(__FILE__)."/../classes/class_wms.php"); 
require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");

$fieldHeight = 20;
$cnt_gui = 0;
$cnt_gui_wms = 0;
$cnt_wms = 0;
$cnt_user_group = 0;
$cnt_group = 0;
$cnt_gui_mb_group = 0;
$cnt_group_gui_wms = 0;
$cnt_fkey_group_gui_wms = 0;
$cnt_fkey_show_gui_wms = 0;
$cnt_show_gui_wms = 0;
$cnt_group_name = 0;
$cnt_gui_table = 0;
$exists = false;
$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");
$logged_gui_id=Mapbender::session()->get("mb_user_gui");

#   SQL 

//get infos from gui_element

//get group from logged user 
$sql_user_group = "SELECT * FROM mb_user_mb_group WHERE fkey_mb_user_id= $1 ";
$array_values = array($logged_user_id);
$array_types = array('s');
$res_user_group = db_prep_query($sql_user_group, $array_values, $array_types);
while($row = db_fetch_array($res_user_group)){
	$user_id[$cnt_user_group] = $row["fkey_mb_user_id"];
	$group_id[$cnt_user_group] = $row["fkey_mb_group_id"];
	$cnt_user_group++;
}
/*get group from logged user  ************************************************************/

# Thekla, please recheck
/*get group name  ********************************************************************************************/								 
if(count($group_id) > 0){
	$v = array();
	$t = array();
	$sql_group = "SELECT mb_group_id, mb_group_name, mb_group_description FROM mb_group WHERE mb_group_id IN (";	
	for($i=0; $i < count($group_id); $i++){
		if($i>0){ $sql_group .= ",";}
		$sql_group .= "$".strval($i + 1);
		array_push($v,$group_id[$i]);
		array_push($t,"i");
	}		
	$sql_group.= ") ORDER BY mb_group_name";	
	$res_group = db_prep_query($sql_group,$v,$t);	
	while($row = db_fetch_array($res_group)){
		$mb_group_description[$cnt_group] = $row["mb_group_description"];
		$my_group_name[$cnt_group] = $row["mb_group_name"];
		$my_group_id[$cnt_group] = $row["mb_group_id"];
		$cnt_group++;
	}				 
}
/*get group name  ********************************************************************************************/							 

/*get allocated gui  ********************************************************************************************/

$arrayGuis=mb_getGUIs($logged_user_id);
$sql_gui = "SELECT * FROM gui WHERE gui_id IN (";
$v = $arrayGuis;
$t = array();

for ($i = 1; $i <= count($arrayGuis); $i++){
	if ($i > 1) { 
		$sql_gui .= ",";
	}
	$sql_gui .= "$" . $i;
	array_push($t, "s");
}
$sql_gui.= ") ORDER BY gui_name";


$res_gui = db_prep_query($sql_gui, $v, $t);
				while($row = db_fetch_array($res_gui)){
					$gui_id[$cnt_gui] = $row["gui_id"];
					$gui_name[$cnt_gui] = $row["gui_name"];
					$gui_description[$cnt_gui] = $row["gui_description"];	
					#echo"$gui_name[$cnt_gui]";							
					$cnt_gui++;
				}
/*get allocated gui  ********************************************************************************************/
				 
/*get allocated wms from allocated gui  ********************************************************************************************/								 
$sql_gui_wms = "SELECT DISTINCT fkey_wms_id, fkey_gui_id FROM gui_wms WHERE fkey_gui_id IN (";
$v = $arrayGuis;
$t = array();
for ($i = 1; $i <= count($arrayGuis); $i++){
	if ($i > 1) { 
		$sql_gui_wms .= ",";
	}
	$sql_gui_wms .= "$".$i;
	array_push($t, "s");
}
$sql_gui_wms.= ") ORDER BY fkey_wms_id";

$res_gui_wms = db_prep_query($sql_gui_wms, $v, $t);
while($row = db_fetch_array($res_gui_wms)){
				$fkey_gui_id[$cnt_gui_wms] = $row["fkey_gui_id"];
	$fkey_wms_id[$cnt_gui_wms] = $row["fkey_wms_id"];
	$cnt_gui_wms++;
}
//get allocated wms from allocated gui							 

//get allocated wms-Abstract and wms-Capabilities from allocated gui								 
$sql_wms = "SELECT DISTINCT wms_title, wms_abstract, wms_getcapabilities, wms_version FROM wms WHERE wms_id IN (";
$v = $fkey_wms_id;
$t = array();
for ($i = 1; $i <= count($fkey_wms_id); $i++){
	if ($i > 1) { 
		$sql_wms .= ",";
	}
	$sql_wms .= "$".$i;
	array_push($t, "s");
}
$sql_wms.= ") ORDER BY wms_title";

$res_wms = db_prep_query($sql_wms, $v, $t);
				while($row = db_fetch_array($res_wms)){
					$wms_title[$cnt_wms] = $row["wms_title"];
					$wms_abstract[$cnt_wms] = $row["wms_abstract"];
					$wms_getcapabilities[$cnt_wms] = $row["wms_getcapabilities"];
					$wms_version[$cnt_wms] = $row["wms_version"];
					$cnt_wms++;
				}							 
//get allocated wms-Abstract and wms-Capabilities from allocated gui							 


/*INSERT HTML*/
echo "<form name='form1' action='" . $_SERVER["SCRIPT_NAME"] . "?".SID."' method='post'>";
# Button
echo "<table border='0' cellpadding='3'  rules='rows'>";
echo "<tr>";
if (!empty($wms_show) || !empty($show_gui_configured_wms) || !empty($show_group_wms) || !empty($show_gui_wms)){
	echo "<td id='_option_all'><input type='button' class='wms_button' name='wms1' value='all wms' onclick='mod_show_wms(1)'></td>";
	echo "<td id='_option_group'><input type='button' class='wms_button' name='wms2' value='group' onclick = 'mod_show_wms(2)'></td>";	
	echo "<td id='_option_gui'><input type='button' class='wms_button' name='wms3' value='gui' onclick = 'mod_show_wms(3)'></td>";
	echo "<td id='_option_db'><input type='button' class='wms_button' name='wms4' value='db' onclick = 'mod_show_wms(4)'></td>";
	echo "</tr>";
	echo "</table>";


######   SQL   #####################################################################################


######   SHOW GUI WMS OR GROUP WMS   #####################################################################################

/*show goup wms  ********************************************************************************************/
#if (isset($show_group_wms))
if (!empty($show_group_wms)){
	/*get gui goup   ********************************************************************************************/
	$sql_gui_mb_group = "SELECT fkey_gui_id, fkey_mb_group_id FROM gui_mb_group WHERE fkey_mb_group_id=$1";
	$v = array($show_group_wms);
	$t = array("s");
	$res_gui_mb_group = db_prep_query($sql_gui_mb_group, $v, $t);

				while($row = db_fetch_array($res_gui_mb_group)){
					$group_gui_id[$cnt_gui_mb_group] = $row["fkey_gui_id"];
					$fkey_mb_group_id[$cnt_gui_mb_group] = $row["fkey_mb_group_id"];
					#echo"$group_gui_id[$cnt_gui_mb_group]";  
					$cnt_gui_mb_group++;
				}
								 
  /*get gui goup   ********************************************************************************************/			

	/*get group gui WMS  ********************************************************************************************/
	if(count($group_gui_id)>0)	{								 
		$sql_fkey_group_gui_wms = "SELECT DISTINCT fkey_wms_id, fkey_gui_id FROM gui_wms WHERE fkey_gui_id IN (";
		$v = $group_gui_id;
		$t = array();
		for ($i = 1; $i <= count($group_gui_id); $i++){
			if ($i > 1) { 
				$sql_fkey_group_gui_wms .= ",";
			}
			$sql_fkey_group_gui_wms .= "$".$i;
			array_push($t, "s");
		}
		$sql_fkey_group_gui_wms.=  ") ORDER BY fkey_wms_id";
		
		$res_fkey_group_gui_wms = db_prep_query($sql_fkey_group_gui_wms, $v, $t);
		while($row = db_fetch_array($res_fkey_group_gui_wms)){
			$fkey_group_gui_gui_id[$cnt_fkey_group_gui_wms] = $row["fkey_gui_id"];
			$fkey_group_gui_wms_id[$cnt_fkey_group_gui_wms] = $row["fkey_wms_id"];
			#echo"$fkey_group_gui_wms_id[$cnt_fkey_group_gui_wms]";
			$cnt_fkey_group_gui_wms++;
		}	

		//get group gui WMS		

		//group - get allocated wms-Abstract and wms-Capabilities from allocated gui 								 
		if(count($fkey_group_gui_wms_id)>0){
			$sql_group_gui_wms = "SELECT DISTINCT wms_title, wms_abstract, wms_getcapabilities, wms_version FROM wms WHERE wms_id IN (";
			$v = $fkey_group_gui_wms_id;
			$t = array();
			for ($i = 1; $i <= count($fkey_group_gui_wms_id); $i++){
				if ($i > 1) { 
					$sql_group_gui_wms .= ",";
				}
				$sql_group_gui_wms .= "$".$i;
				array_push($t, "s");
			}
			$sql_group_gui_wms.= ") ORDER BY wms_title";
		  
			$res_group_gui_wms = db_prep_query($sql_group_gui_wms, $v, $t);
			while($row = db_fetch_array($res_group_gui_wms)){
				$group_wms_title[$cnt_group_gui_wms] = $row["wms_title"];
				$group_wms_abstract[$cnt_group_gui_wms] = $row["wms_abstract"];
				$group_wms_getcapabilities[$cnt_group_gui_wms] = $row["wms_getcapabilities"];
				$group_wms_version[$cnt_group_gui_wms] = $row["wms_version"];
				#echo"$group_wms_title[$cnt_group_gui_wms]";
				$cnt_group_gui_wms++;
			}
		}         	
	}		
					 
	//group - get allocated wms-Abstract and wms-Capabilities from allocated gui

	//table with allocated wms-Abstract and wms-Capabilities from allocated gui
	#if ($show_group_wms > 0)
	if ($cnt_group_gui_wms > 0){
		/*get goup name for showing in the table ********************************************************************************************/								 
		$sql_group_name = "SELECT mb_group_id, mb_group_name FROM mb_group WHERE mb_group_id = $1";   
		$v = array($show_group_wms);
		$t = array("s");
		$res_group_name = db_prep_query($sql_group_name, $v, $t);
		while($row = db_fetch_array($res_group_name)){
			$group_name_table[$cnt_group_name] = $row["mb_group_name"];
			$my_group_id_table[$cnt_group_name] = $row["mb_group_id"];
			$cnt_group_name++;
		}
					 
	/*get goup name  ********************************************************************************************/

	echo"<br>";
	echo"<br>";
		echo"wms from group: $group_name_table[0]";
		echo"<br>";
		echo"<br>";	
	echo "<table border='1' width ='98%' cellpadding='3' rules='rows'>";
	echo " <thead bgcolor = '#FAEBD7' >";
	echo "<tr><td width='200' height='10'>WMS-Title</td><td  align = 'left' class='fieldnames_s'>WMS-Abstract</td>";
	echo " </thead>";
	echo " <tbody >";
		for($i=0; $i<$cnt_group_gui_wms; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			
			echo "<td><div id ='id_".$group_wms_title[$i]."' class='even' name ='name_".$group_wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$group_wms_getcapabilities[$i]."\",\"".$group_wms_version[$i]."\")'>".$group_wms_title[$i]."</div></td>";
			echo "<td><div id ='id_".$group_wms_abstract[$i]."' class='even' name ='name_".$group_wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$group_wms_getcapabilities[$i]."\",\"".$group_wms_version[$i]."\")'>".$group_wms_abstract[$i]."</div></td>";
			echo "</tr>";		
		}		
		echo "  </tbody>";							 						 
		echo "</table>";
		}  
		else{
			echo"<br>";
			echo"<br>";
			echo "no wms in this group";
		}/*End: if ($show_group_wms > 0)   *********/
		/*table with allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/	


	}/*End: if (!empty($show_group_wms))   *********/

/*show gui wms  ********************************************************************************************/
if (!empty($show_gui_wms)){
	/*get group gui WMS  ********************************************************************************************/								 
	$sql_fkey_show_gui_wms = "SELECT DISTINCT fkey_wms_id, fkey_gui_id FROM gui_wms WHERE fkey_gui_id = $1";
	$v = array($show_gui_wms);
	$t = array("s");
	#$sql_fkey_show_gui_wms.= ") ORDER BY fkey_wms_id";

	$res_fkey_show_gui_wms = db_prep_query($sql_fkey_show_gui_wms, $v, $t);
	while($row = db_fetch_array($res_fkey_show_gui_wms)){
		$fkey_show_gui_gui_id[$cnt_fkey_show_gui_wms] = $row["fkey_gui_id"];
		$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms] = $row["fkey_wms_id"];
		#echo"$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms]";
		$cnt_fkey_show_gui_wms++;
	}								 
	/*get group gui WMS  ********************************************************************************************/		

	//gui - get allocated wms-Abstract and wms-Capabilities from allocated gui								 
	if(count($fkey_show_gui_wms_id)>0){
		$sql_show_gui_wms = "SELECT DISTINCT wms_title, wms_abstract, wms_getcapabilities, wms_id, wms_version FROM wms WHERE wms_id IN (";
		$v = $fkey_show_gui_wms_id;
		$t = array();
		for ($i = 1; $i <= count($fkey_show_gui_wms_id); $i++){
			if ($i > 1) { 
				$sql_show_gui_wms .= ",";
			}
			$sql_show_gui_wms .= "$".$i;
			array_push($t, "s");
		}
		$sql_show_gui_wms.= ") ORDER BY wms_title";

		$res_show_gui_wms = db_prep_query($sql_show_gui_wms, $v, $t);
		while($row = db_fetch_array($res_show_gui_wms)){
			$gui_wms_id[$cnt_show_gui_wms] = $row["wms_id"];
			$gui_wms_title[$cnt_show_gui_wms] = $row["wms_title"];
			$gui_wms_abstract[$cnt_show_gui_wms] = $row["wms_abstract"];
			$gui_wms_getcapabilities[$cnt_show_gui_wms] = $row["wms_getcapabilities"];
			$gui_wms_version[$cnt_show_gui_wms] = $row["wms_version"];
			#echo"$gui_wms_title[$cnt_show_gui_wms]";
			$cnt_show_gui_wms++;
		}							 
		//gui - get allocated wms-Abstract and wms-Capabilities from allocated gui

		/*table with allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/
		#if (isset($cnt_show_gui_wms > 0))
	}

	if ($cnt_show_gui_wms > 0){
	/*get selected gui name for table caption ********************************************************************************************/  
	$sql_gui_table = "SELECT * FROM gui WHERE gui_id = $1";       
	$v = array($show_gui_wms);
	$t = array("s");
	$res_gui_table = db_prep_query($sql_gui_table, $v, $t);
		while($row = db_fetch_array($res_gui_table)){
			$gui_id_table[$cnt_gui_table] = $row["gui_id"];
			$gui_name_table[$cnt_gui_table] = $row["gui_name"];							
			$cnt_gui_table++;
			#echo"$gui_id_table[0]";
		}

		/*get selected gui name for table caption ********************************************************************************************/

		echo"<br>";
		echo"<br>";
			echo"wms from gui: $gui_name_table[0]";
			echo"<br>";
			echo"<br>";
		echo "<table border='1'  width ='98%'   cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = '#FAEBD7' >";
		echo "<tr><td width='200' height='10'>WMS-Title</td><td  align = 'left' class='fieldnames_s'>WMS-Abstract</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_show_gui_wms; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$gui_wms_title[$i]."' class='even' name ='name_".$gui_wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$gui_wms_getcapabilities[$i]."\",\"".$gui_wms_version[$i]."\")'>".$gui_wms_title[$i]."</div></td>";
			echo "<td><div  id ='id_".$gui_wms_abstract[$i]."' class='even' name ='name_".$gui_wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$gui_wms_getcapabilities[$i]."\",\"".$gui_wms_version[$i]."\")'>".$gui_wms_abstract[$i]."</div></td>";
			echo "</tr>";	
		}	
		echo "  </tbody>";							 						 
		echo "</table>";
	}
	else{
		echo"<br>";
		echo"<br>";
		echo"<br>";

		echo "no wms in this gui";
  	}  /*End: if ($cnt_show_gui_wms > 0)  *********/
	/*table with allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/	


} /*End: if(isset($show_gui_wms))   *********/



/*show gui wms  ********************************************************************************************/
if (!empty($show_gui_configured_wms)){
	/*get group gui WMS  ********************************************************************************************/								 
	$sql_fkey_show_gui_wms = "SELECT DISTINCT fkey_wms_id, fkey_gui_id FROM gui_wms WHERE fkey_gui_id = $1";
	#$sql_fkey_show_gui_wms.= ") ORDER BY fkey_wms_id";
	$v = array($show_gui_configured_wms);
	$t = array("s");
	$res_fkey_show_gui_wms = db_prep_query($sql_fkey_show_gui_wms, $v, $t);
	while($row = db_fetch_array($res_fkey_show_gui_wms)){
		$fkey_show_gui_gui_id[$cnt_fkey_show_gui_wms] = $row["fkey_gui_id"];
		$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms] = $row["fkey_wms_id"];
		#echo"$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms]";
		$cnt_fkey_show_gui_wms++;
	}								 
	/*get group gui WMS  ********************************************************************************************/		

	//gui - get allocated wms-Abstract and wms-Capabilities from allocated gui								 
	if(count($fkey_show_gui_wms_id)>0){
		$sql_show_gui_wms = "SELECT DISTINCT wms_title, wms_abstract, wms_getcapabilities, wms_id, wms_version FROM wms WHERE wms_id IN (";
		$v = $fkey_show_gui_wms_id;
		$t = array();
		for ($i = 1; $i <= count($fkey_show_gui_wms_id); $i++){
			if ($i > 1) { 
				$sql_show_gui_wms .= ",";
			}
			$sql_show_gui_wms .= "$".$i;
			array_push($t, "s");
		}
		$sql_show_gui_wms.= ") ORDER BY wms_title";

		$res_show_gui_wms = db_prep_query($sql_show_gui_wms, $v, $t);
		while($row = db_fetch_array($res_show_gui_wms)){
			$gui_wms_id[$cnt_show_gui_wms] = $row["wms_id"];
			$gui_wms_title[$cnt_show_gui_wms] = $row["wms_title"];
			$gui_wms_abstract[$cnt_show_gui_wms] = $row["wms_abstract"];
			$gui_wms_getcapabilities[$cnt_show_gui_wms] = $row["wms_getcapabilities"];
			$gui_wms_version[$cnt_show_gui_wms] = $row["wms_version"];
			#echo"$gui_wms_title[$cnt_show_gui_wms]";
			$cnt_show_gui_wms++;
		}							 
		//gui - get allocated wms-Abstract and wms-Capabilities from allocated gui  

		//table with allocated wms-Abstract and wms-Capabilities from allocated gui
		#if (isset($cnt_show_gui_wms > 0))
	}

	if ($cnt_show_gui_wms > 0){
	//get selected gui name for table caption  
	$sql_gui_table = "SELECT * FROM gui WHERE gui_id = $1";
	$v = array($show_gui_configured_wms);
	$t = array("s");       
	$res_gui_table = db_prep_query($sql_gui_table, $v, $t);
		while($row = db_fetch_array($res_gui_table)){
			$gui_id_table[$cnt_gui_table] = $row["gui_id"];
			$gui_name_table[$cnt_gui_table] = $row["gui_name"];							
			$cnt_gui_table++;
			#echo"$gui_id_table[0]";
		}

		/*get selected gui name for table caption ********************************************************************************************/

		echo"<br>";
		echo"<br>";
			echo"wms from gui: $gui_name_table[0]";
			echo"<br>";
			echo"<br>";
		echo "<table border='1'  width ='98%'   cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = '#FAEBD7' >";
		echo "<tr><td width='200' height='10'>WMS-Title</td><td  align = 'left' class='fieldnames_s'>WMS-Abstract</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_show_gui_wms; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$gui_wms_title[$i]."' class='even' name ='name_".$gui_wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"".$show_gui_configured_wms."\",\"".$gui_wms_id[$i]."\")'>".$gui_wms_title[$i]."</div></td>";
			echo "<td><div  id ='id_".$gui_wms_abstract[$i]."' class='even' name ='name_".$gui_wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"".$show_gui_configured_wms."\",\"".$gui_wms_id[$i]."\")'>".$gui_wms_abstract[$i]."</div></td>";
			echo "</tr>";	
		}	
		echo "  </tbody>";							 						 
		echo "</table>";
	}
	else{
		echo"<br>";
		echo"<br>";
		echo"<br>";

		echo "no wms in this gui";
  	}  /*End: if ($cnt_show_gui_wms > 0)  *********/
	/*table with allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/	


} /*End: if(isset($show_gui_configured_wms))   *********/

######   SHOW GUI WMS OR GROUP WMS   #####################################################################################

######   SHOW GUI OR GROUP OR ALL WMS   #####################################################################################

	if ($wms_show == 4){ #gui
		echo"<br>";
		echo"<br>";
		echo"Please select a gui:";
		echo"<br>";
		echo"<br>";	
		echo "<table border='1' width='98%' cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = 'lightgrey' >";
		echo "<tr><td width='200' height='10'>gui name</td><td  align = 'left' class='fieldnames_s'>description</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_gui; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$gui_name[$i]."' value='".$gui_id[$i]."' class='even' name ='".$gui_name[$i]."'  style='cursor:pointer'  onclick = 'mod_show_gui_configured_wms(\"".$gui_id[$i]."\")'>".$gui_name[$i]."</div></td>";
			echo "<td><div id ='id_".$gui_description[$i]."' value='".$gui_id[$i]."' class='even' name ='".$gui_description[$i]."' style='cursor:pointer' onclick = 'mod_show_gui_configured_wms(\"".$gui_id[$i]."\")'>".$gui_description[$i]."</div></td>";
			echo "</tr>";
		}		
		echo "  </tbody>";							 						 
		echo "</table>";		

	}
	if ($wms_show == 3){ #gui
		echo"<br>";
		echo"<br>";
		echo"Please select a gui:";
		echo"<br>";
		echo"<br>";	
		echo "<table border='1' width='98%' cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = 'lightgrey' >";
		echo "<tr><td width='200' height='10'>gui name</td><td  align = 'left' class='fieldnames_s'>description</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_gui; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$gui_name[$i]."' value='".$gui_id[$i]."' class='even' name ='".$gui_name[$i]."'  style='cursor:pointer'  onclick = 'mod_show_gui_wms(\"".$gui_id[$i]."\")'>".$gui_name[$i]."</div></td>";
			echo "<td><div id ='id_".$gui_description[$i]."' value='".$gui_id[$i]."' class='even' name ='".$gui_description[$i]."' style='cursor:pointer' onclick = 'mod_show_gui_wms(\"".$gui_id[$i]."\")'>".$gui_description[$i]."</div></td>";
			echo "</tr>";
		}		
		echo "  </tbody>";							 						 
		echo "</table>";		

	}
	elseif  ($wms_show== 2){  # group
		echo"<br>";
		echo"<br>";
  
		if($cnt_group>0){
			echo "Please select a group:";
			echo"<br>";
			echo"<br>";
			echo "<table border='1' width='98%' cellpadding='3' rules='rows'>";
			echo " <thead bgcolor = 'lightgrey' >";
			echo "<tr><td  width='200' height='10'>group name</td><td  align = 'left' class='fieldnames_s'>description</td>";
			echo " </thead>";
			echo " <tbody >";
			for($i=0; $i<$cnt_group; $i++){
				echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";  				
				echo "<td><div id ='id_".$my_group_name[$i]."' value='".$my_group_id[$i]."' class='even' name ='".$my_group_name[$i]."'  style='cursor:pointer'  onclick = 'mod_show_group_wms(\"".$my_group_id[$i]."\")'>".$my_group_name[$i]."</div></td>";
				echo "<td><div id ='id_".$mb_group_description[$i]."' value='".$my_group_id[$i]."' class='even' name ='".$mb_group_description[$i]."' style='cursor:pointer' onclick = 'mod_show_group_wms(\"".$my_group_id[$i]."\")'>".$mb_group_description[$i]."</div></td>";
				echo "</tr>";
			}		
			echo "  </tbody>";							 						 
			echo "</table>";
		}
		else{
			echo"no group for this user";
		}
	
	}
	elseif  ($wms_show== 1){  # all wms
		echo"<br>";
		echo"<br>";

		echo "<table border='1'  width ='98%'   cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = '#FAEBD7' >";
		echo "<tr><td width='200' height='10'>WMS-Title</td><td  align = 'left' class='fieldnames_s'>WMS-Abstract</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_wms; $i++){
        	echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$wms_title[$i]."' class='even' name ='name_".$wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$wms_getcapabilities[$i]."\",\"".$wms_version[$i]."\")'>".$wms_title[$i]."</div></td>";
			echo "<td><div  id ='id_".$wms_abstract[$i]."' class='even' name ='name_".$wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromfilteredList(\"".$wms_getcapabilities[$i]."\",\"".$wms_version[$i]."\")'>".$wms_abstract[$i]."</div></td>";
			echo "</tr>"; 					 					
		}		
		echo "  </tbody>";							 						 
		echo "</table>";
	}
}


######   SHOW GUI OR GROUP OR ALL WMS   #####################################################################################


/*show group wms  ********************************************************************************************/
echo "<input type='hidden' name='show_group_wms'>";
/*show gui wms  ********************************************************************************************/
echo "<input type='hidden' name='show_gui_wms'>";
echo "<input type='hidden' name='show_gui_configured_wms'>";
/*show button  ********************************************************************************************/
echo "<input type='hidden' name='wms_show'>";
echo "<input id='wmsSource' type='hidden' name='wmsSource'>";
echo "<input name ='set_option_all' id='set_option_all' type='hidden'>";
echo "<input name='set_option_group' id='set_option_group' type='hidden'>";
echo "<input name='set_option_gui' id='set_option_gui' type='hidden'>";
echo "<input name='set_option_db' id='set_option_db' type='hidden'>";
echo "</form>";
?>
<script type="text/javascript">

// -->
</script>
</body>
</html>