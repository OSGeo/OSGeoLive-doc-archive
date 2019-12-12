<?php
# $Id: mod_gazetteer_conf.php 2413 2008-04-23 16:21:04Z christoph $
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require(dirname(__FILE__)."/../classes/class_wfs_conf.php");
?>
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>gazetteer</title>
<script language="JavaScript" type="text/javascript">
  function selectWFS(){
  	if(document.forms[0].featuretype){
  		if(document.forms[0].featuretype.length){
  			for(var i=0; i<document.forms[0].featuretype.length;i++){  			
  				document.forms[0].featuretype[i].checked = false;
  			}
  		}
  		else{
  			document.forms[0].featuretype.checked = false;
  		}  		
  	}
  	document.forms[0].submit();
  }
  function validate(){
   	for(var i=0; i<document.forms[0].length; i++){
  		if(document.forms[0].elements[i].type == 'checkbox'){
   			if(document.forms[0].elements[i].checked){
  				document.forms[0].elements[i].value = 1;
  			}
  			else{
  				document.forms[0].elements[i].value = 0;
  			}
  			document.forms[0].elements[i].checked = true;
  		}
  	}
  	return true;
  }
</script>
</head>
<body>
Gazetteer Configuration<br>
<form method='POST' onsubmit='return validate()'>
<a href="mod_gazetteer_edit.php">edit</a><br>
<?php
$aWFS = new wfs_conf();
$aWFS->getallwfs();	

/* save gazetteer properties */

if(isset($_REQUEST["save"])){
	global $DBSERVER,$DB,$OWNER,$PW;
	$con = db_connect($DBSERVER,$OWNER,$PW);
	db_select_db($DB,$con);
	
	$sql = "INSERT INTO gazetteer (gazetteer_abstract, fkey_wfs_id, ";
	$sql .= "fkey_featuretype_id, g_label, g_label_id, g_button, ";
	$sql .= "g_button_id, g_style, g_buffer, g_res_style, g_use_wzgraphics) ";
	$sql .= "VALUES($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11);";
	$v = array($_REQUEST["gazetteer_abstract"], $_REQUEST["wfs"], $_REQUEST["featuretype"], $_REQUEST["g_label"], $_REQUEST["g_label_id"], $_REQUEST["g_button"], $_REQUEST["g_button_id"], $_REQUEST["g_style"], $_REQUEST["g_buffer"], $_REQUEST["g_res_style"], $_REQUEST["g_use_wzgraphics"]);
	$t = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "i");
	$res = db_prep_query($sql, $v, $t);		
	$wfsID = db_insert_id($con);
	for($i=0; $i<count($_REQUEST["f_id"]); $i++){
		$sql = "INSERT INTO gazetteer_element (fkey_gazetteer_id, ";
		$sql .= "f_id, f_search, f_pos, f_style_id, f_toupper, f_label, ";
		$sql .= "f_label_id, f_show, f_respos) VALUES (";
		$sql .= "$1, $2, $3, $4, $5, $6, $7, $8, $9, $10);";
		$v = array($wfsID, $_REQUEST["f_id"][$i], $_REQUEST["f_search"][$i], $_REQUEST["f_pos"][$i], $_REQUEST["f_style_id"][$i], $_REQUEST["f_toupper"][$i], $_REQUEST["f_label"][$i], $_REQUEST["f_label_id"][$i], $_REQUEST["f_show"][$i], $_REQUEST["f_respos"][$i]);
		$t = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s");
		$res = db_prep_query($sql, $v, $t);
	}		
}

/* end save gazetteer properties */

/* select wfs */

if(isset($_REQUEST["wfs"]) && $_REQUEST["wfs"] == ""){
	unset($_REQUEST["wfs"]);
	unset($_REQUEST["featuretype"]);
}

echo "<select name='wfs' onchange='selectWFS()'>";
echo "<option value=''>...</option>";
for($i=0; $i<count($aWFS->wfs_id);$i++){
	echo "<option value='".$aWFS->wfs_id[$i]."' ";
	if(isset($_REQUEST["wfs"]) && $aWFS->wfs_id[$i] == $_REQUEST["wfs"]){
		echo "selected";
	}
	echo ">".$aWFS->wfs_title[$i]."</option>";
}
echo "</select>";
echo "";

/* end select wfs */


/* select featuretype */

if(isset($_REQUEST["wfs"])){
	
	for($i=0; $i<count($aWFS->wfs_id);$i++){
		if($aWFS->wfs_id[$i] == $_REQUEST["wfs"]){
			echo "<table>";
			echo "<tr><td>ID:</td><td>".$aWFS->wfs_id[$i]."</td></tr>";
			echo "<tr><td>Name:</td><td>".$aWFS->wfs_name[$i]."</td></tr>";
			echo "<tr><td>Title:</td><td>".$aWFS->wfs_title[$i]."</td></tr>";
			echo "<tr><td>Abstract:</td><td>".$aWFS->wfs_abstract[$i]."</td></tr>";
			echo "<tr><td>Capabilities:</td><td>".$aWFS->wfs_getcapabilities[$i]."</td></tr>";
			echo "<tr><td>FeaturTypes:</td><td>".$aWFS->wfs_describefeaturetype[$i]."</td></tr>";
			echo "<tr><td>Feature:</td><td>".$aWFS->wfs_getfeature[$i]."</td></tr>";
			echo "</table>";
		}
	}
	
	$aWFS->getfeatures($_REQUEST["wfs"]);
	echo "<table>";
	for($i=0; $i<count($aWFS->features->featuretype_id); $i++){
		echo "<tr>";
		echo "<td><input type='radio' name='featuretype' value='".$aWFS->features->featuretype_id[$i]."' onclick='submit()' ";
		if(isset($_REQUEST["featuretype"]) && $_REQUEST["featuretype"] == $aWFS->features->featuretype_id[$i]){
			echo "checked ";
		}
		echo "/></td>";
		echo "<td>".$aWFS->features->featuretype_name[$i]."</td>";
		echo "</tr>";	
	}
	echo "</table>";
}

/* end select featuretype */

/* configure elements */
if(isset($_REQUEST["featuretype"])){
	
	
	for($i=0; $i<count($aWFS->features->featuretype_id); $i++){
		if($_REQUEST["featuretype"] == $aWFS->features->featuretype_id[$i]){
			echo "<hr>SRS: ".$aWFS->features->featuretype_srs[$i];
		}
	}
	
	/* set featuretype options */
	echo "<table>";
	echo "<tr><td>Abstract:</td><td><input type='text' name='gazetteer_abstract'></td></tr>" ;
	echo "<tr><td>Label:</td><td><input type='text' name='g_label'></td></tr>" ;
	echo "<tr><td>Label_id:</td><td><input type='text' name='g_label_id'></td></tr>" ;
	echo "<tr><td>Button:</td><td><input type='text' name='g_button'></td></tr>" ;
	echo "<tr><td>Button_id:</td><td><input type='text' name='g_button_id'></td></tr>" ;
	echo "<tr><td>Style:</td><td><textarea cols=50 rows=5 name='g_style'></textarea></td></tr>" ;
	echo "<tr><td>Buffer:</td><td><input type='text' size='4' name='g_buffer'></td></tr>" ;
	echo "<tr><td>ResultStye:</td><td><textarea cols=50 rows=5 name='g_res_style'></textarea></td></tr>" ;
	echo "<tr><td>WZ-Graphics:</td><td><input name='g_use_wzgraphics' type='checkbox'></td></tr>";
	echo "</table>";
	
	
	/* set element options */
	$aWFS->getelements($_REQUEST["featuretype"]);
	echo "<table border='1'>";
	echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>name</td>";
		echo "<td>type</td>";
		echo "<td>search</td>";
		echo "<td>pos</td>";
		echo "<td>style_id</td>";
		echo "<td>to_upper</td>";
		echo "<td>label</td>";		
		echo "<td>label_id</td>";
		echo "<td>show</td>";
		echo "<td>position</td>";
	echo "</tr>";
	
	for($i=0; $i<count($aWFS->elements->element_id); $i++){
		echo "<tr>";
		echo "<td>".$aWFS->elements->element_id[$i]."<input type='hidden' name='f_id[]' value='".$aWFS->elements->element_id[$i]."'></td>";
		echo "<td>".$aWFS->elements->element_name[$i]."</td>";
		echo "<td>".$aWFS->elements->element_type[$i]."</td>";
		echo "<td><input name='f_search[]' type='checkbox'></td>";
		echo "<td><input name='f_pos[]' type='text' size='2'></td>";
		echo "<td><input name='f_style_id[]' type='text' size='2'></td>";
		echo "<td><input name='f_toupper[]' type='checkbox'></td>";
		echo "<td><input name='f_label[]' type='text' size='10'></td>";		
		echo "<td><input name='f_label_id[]' type='text' size='2'></td>";
		echo "<td><input name='f_show[]' type='checkbox'></td>";
		echo "<td><input name='f_respos[]' type='text' size='4'></td>";
		echo "</tr>";	
	}
	echo "</table>";
	echo "<input type='submit' name='save' value='save'>";
}


/* end configure elements */
?>
</form>
</body>
