<?php
# $Id: mod_gazLayerObj_edit.php 2413 2008-04-23 16:21:04Z christoph $
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
?>
<html>
<head>
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Edit Gazetteer</title>
<script language="JavaScript" type="text/javascript">
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

<?php
/* save gazetteer properties */
$con = db_connect($DBSERVER,$OWNER,$PW);
db_select_db(DB,$con);
if(isset($_REQUEST["save"])){
	
	$sql = "UPDATE gazetteer SET ";
	$sql .= "gazetteer_abstract = $1, ";
	$sql .= "g_label = $2, ";
	$sql .= "g_label_id = $3, ";
	$sql .= "g_button = $4, ";
	$sql .= "g_button_id = $5, ";
	$sql .= "g_style = $6, ";
	$sql .= "g_buffer = $7 ";	
	$sql .= "WHERE gazetteer_id = $8;";
	
	$v = array($_REQUEST["gazetteer_abstract"], $_REQUEST["g_label"], $_REQUEST["g_label_id"], $_REQUEST["g_button"], $_REQUEST["g_button_id"], $_REQUEST["g_style"], $_REQUEST["g_buffer"], $_REQUEST["fkey_gazetteer_id"]);
	$t = array("s", "s", "s", "s", "s", "s", "s", "i");
	$res = db_prep_query($sql, $v, $t);		

	for ($i = 0; $i < count($_REQUEST["f_id"]); $i++){
		$sql = "UPDATE gazetteer_element SET ";		
		$sql .= "f_search = $1, ";
		$sql .= "f_pos = $2, ";
		$sql .= "f_style_id = $3, ";
		$sql .= "f_label = $4, ";
		$sql .= "f_label_id = $5, ";
		$sql .= "f_show = $6, ";
		$sql .= "f_respos = $7 ";
		$sql .= "WHERE fkey_gazetteer_id = $8 AND f_id = $9;";
		
		$v = array($_REQUEST["f_search"][$i], $_REQUEST["f_pos"][$i], $_REQUEST["f_style_id"][$i], $_REQUEST["f_label"][$i], $_REQUEST["f_label_id"][$i], $_REQUEST["f_show"][$i], $_REQUEST["f_respos"][$i], $_REQUEST["fkey_gazetteer_id"], $_REQUEST["f_id"][$i]);
		$t = array("s", "s", "s", "s", "s", "s", "s", "i", "i");
		
		$res = db_prep_query($sql, $v, $t);
	}		
}

/* end save gazetteer properties */

/* select wfs */



/* end select wfs */



/* configure elements */
if(isset($_REQUEST["fkey_gazetteer_id"])){
	$sql = "SELECT * FROM gazetteer WHERE gazetteer_id = $1";
	$v = array($_REQUEST["fkey_gazetteer_id"]);
	$t = array("i");
	$res = db_prep_query($sql, $v, $t);
	if($row = db_fetch_array($res)){	
		echo "<table>";
		echo "<tr><td>ID:</td><td>".$row["gazetteer_id"]."</td></tr>" ;
		echo "<tr><td>Abstract:</td><td><input type='text' name='gazetteer_abstract' value='".$row["gazetteer_abstract"]."'></td></tr>" ;
		echo "<tr><td>Label:</td><td><input type='text' name='g_label' value='".$row["g_label"]."'></td></tr>" ;
		echo "<tr><td>Label_id:</td><td><input type='text' name='g_label_id' value='".$row["g_label_id"]."'></td></tr>" ;
		echo "<tr><td>Button:</td><td><input type='text' name='g_button' value='".$row["g_button"]."'></td></tr>" ;
		echo "<tr><td>Button_id:</td><td><input type='text' name='g_button_id' value='".$row["g_button_id"]."'></td></tr>" ;
		echo "<tr><td>Style:</td><td><textarea cols=30 rows=5 name='g_style'>".$row["g_style"]."</textarea></td></tr>" ;
		echo "<tr><td>Buffer:</td><td><input type='text' size='4' name='g_buffer' value='".$row["g_buffer"]."'></td></tr>" ;
		echo "</table>";
	}
	
	/* set element options */
	$sql = "SELECT * FROM gazetteer_element ";
	$sql .= "JOIN wfs_element ON gazetteer_element.f_id = wfs_element.element_id ";
	$sql .= "WHERE fkey_gazetteer_id = $1";
	$v = array($_REQUEST["fkey_gazetteer_id"]);
	$t = array("i");
	$res = db_prep_query($sql, $v, $t);
	
	echo "<table border='1'>";
	echo "<tr>";
		echo "<td>ID</td>";
		echo "<td>name</td>";
		echo "<td>type</td>";
		echo "<td>search</td>";
		echo "<td>pos</td>";
		echo "<td>style_id</td>";
		echo "<td>label</td>";		
		echo "<td>label_id</td>";
		echo "<td>show</td>";
		echo "<td>position</td>";
	echo "</tr>";
	$cnt = 0;	
	while($row = db_fetch_array($res)){
		echo "<tr>";
		echo "<td><input type='text' size='4' name='f_id[]' value='".$row["gazetteer_element.f_id"]."' readonly></td>";
		echo "<td>".$row["wfs_element.element_name"]."</td>";
		echo "<td>".$row["wfs_element.element_type"]."</td>";
		echo "<td><input name='f_search[]' type='checkbox'";
		if($row["gazetteer_element.f_search"] == 1){ echo " checked"; }
		echo "></td>";
		echo "<td><input name='f_pos[]' type='text' size='2' value='".$row["gazetteer_element.f_pos"]."'></td>";
		echo "<td><input name='f_style_id[]' type='text' size='2' value='".$row["gazetteer_element.f_style_id"]."'></td>";
		echo "<td><input name='f_label[]' type='text' size='10' value='".$row["gazetteer_element.f_label"]."'></td>";		
		echo "<td><input name='f_label_id[]' type='text' size='2' value='".$row["gazetteer_element.f_label_id"]."'></td>";
		echo "<td><input name='f_show[]' type='checkbox'";
		if($row["gazetteer_element.f_show"] == 1){ echo " checked"; }
		echo "></td>";
		echo "<td><input name='f_respos[]' type='text' size='4' value='".$row["gazetteer_element.f_respos"]."'></td>";
		echo "</tr>";
		$cnt++;	
	}
	echo "</table>";
	echo "<input type='submit' name='save' value='save'>";
}


/* end configure elements */
?>
</form>
</body>
