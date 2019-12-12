<?php
# $Id: mod_addWMSfromfilteredListDB.php 6728 2010-08-10 08:31:29Z christoph $
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
<meta http-equiv="Content-Type" content="text/html; charset='<?php echo CHARSET;?>'">	
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
}
-->
</STYLE>
<script type="text/javascript">
<!--
function mod_addWMSfromDB(gui_id, wms_id) {
	//alert("1/3 mod_addWMSfromDB: GUI ID = " + gui_id + ", WMS ID = " + wms_id);
	window.opener.mod_addWMSById_load(gui_id, wms_id);
}

function mod_show_gui_configured_wms(wert3){
	document.form1.wms_show.value = wert3;
	document.form1.submit();
}			

function mod_show_gui(){
	document.form1.wms_show.value = '';
	document.form1.submit();
}			

// -->
</script>
<?php
include '../include/dyn_css.php';
?>
</head>
<body onLoad="window.focus()">

<?php
$wms_show = $_POST["wms_show"];

require_once(dirname(__FILE__)."/../classes/class_wms.php"); 
require_once(dirname(__FILE__)."/../php/mb_getGUIs.php");

$gui_id = array();
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
for ($i = 1; $i <= count($arrayGuis); $i++) {
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
/*get allocated wms from allocated gui  ********************************************************************************************/							 

/*get allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/								 
$sql_wms = "SELECT DISTINCT wms_title, wms_abstract, wms_getcapabilities, wms_version FROM wms WHERE wms_id IN (";
$v = $fkey_wms_id;
$t = array();
for ($i = 1; $i <= count($fkey_wms_id); $i++){
	if ($i > 1) { 
		$sql_wms .= ",";
	}
	$sql_wms .= "$" . $i;
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
/*get allocated wms-Abstract and wms-Capabilities from allocated gui   ********************************************************************************************/							 


/*INSERT HTML*/
echo "<form name='form1' action='" . $_SERVER["SCRIPT_NAME"] . "?".SID."' method='post'>";

if (empty($wms_show)){ #gui
	echo $selectGuiText;
	echo"<br>";
	echo"<br>";	
	echo "<table border='1' width='98%' cellpadding='3' rules='rows'>";
	echo " <thead bgcolor = 'lightgrey' >";
	echo "<tr><td width='200' height='10'>".$guiNameText."</td><td  align = 'left' class='fieldnames_s'>".$guiAbstractText."</td>";
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

else {
	echo "<input type='button' class='wms_button' name='wms2' value='" . $selectOtherGuiText . "' onclick = 'mod_show_gui()'></td>";

	/*get group gui WMS  ********************************************************************************************/								 
	$sql_fkey_show_gui_wms = "SELECT DISTINCT fkey_wms_id, fkey_gui_id FROM gui_wms WHERE fkey_gui_id = $1";
	#$sql_fkey_show_gui_wms.= ") ORDER BY fkey_wms_id";

	$v = array($wms_show);
	$t = array("s");
	$res_fkey_show_gui_wms = db_prep_query($sql_fkey_show_gui_wms, $v, $t);
	while($row = db_fetch_array($res_fkey_show_gui_wms)){
		$fkey_show_gui_gui_id[$cnt_fkey_show_gui_wms] = $row["fkey_gui_id"];
		$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms] = $row["fkey_wms_id"];
		#echo"$fkey_show_gui_wms_id[$cnt_fkey_show_gui_wms]";
		$cnt_fkey_show_gui_wms++;
	}								 
	/*get group gui WMS  ********************************************************************************************/		

	/*gui: get allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/								 
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
		/*gui: get allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/

		/*table with allocated wms-Abstract and wms-Capabilities from allocated gui  ********************************************************************************************/
		#if (isset($cnt_show_gui_wms > 0))
	}

	if ($cnt_show_gui_wms > 0){
	/*get selected gui name for table caption ********************************************************************************************/  
	$sql_gui_table = "SELECT * FROM gui WHERE gui_id = $1";
	$v = array($wms_show);
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
			echo $selectWmsText . " " . $gui_name_table[0];
			echo"<br>";
			echo"<br>";
		echo "<table border='1'  width ='98%'   cellpadding='3' rules='rows'>";
		echo " <thead bgcolor = '#FAEBD7' >";
		echo "<tr><td width='200' height='10'>".$wmsTitleText."</td><td  align = 'left' class='fieldnames_s'>".$wmsAbstractText."</td>";
		echo " </thead>";
		echo " <tbody >";
		for($i=0; $i<$cnt_show_gui_wms; $i++){
			echo "<tr class='Farbe' onmouseover='this.style.backgroundColor = \"#F08080\"' onmouseout='this.style.backgroundColor = \"#ffffff\"'>";
			echo "<td><div id ='id_".$gui_wms_title[$i]."' class='even' name ='name_".$gui_wms_title[$i]."'  style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"".$wms_show."\",\"".$gui_wms_id[$i]."\")'>".$gui_wms_title[$i]."</div></td>";
			echo "<td><div  id ='id_".$gui_wms_abstract[$i]."' class='even' name ='name_".$gui_wms_abstract[$i]."' style='cursor:pointer' onclick = 'mod_addWMSfromDB(\"".$wms_show."\",\"".$gui_wms_id[$i]."\")'>".$gui_wms_abstract[$i]."</div></td>";
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
  	}  
}

echo "<input type='hidden' name='guiID' value='" . $_REQUEST["guiID"] . "'>";
echo "<input type='hidden' name='elementID' value='" . $e_id_css . "'>";
echo "<input type='hidden' id='wms_show' name='wms_show'>";
echo "</form>";
?>
</body>
</html>