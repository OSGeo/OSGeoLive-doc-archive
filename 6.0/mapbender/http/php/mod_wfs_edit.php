<?php
# $Id: mod_wfs_edit.php 4691 2009-09-25 10:39:48Z christoph $
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
<title>wfs_edit</title>
<script language="JavaScript" type="text/javascript">
function validate(){	
	return true;
}
function openwindow(Adresse) {
	Fenster1 = window.open(Adresse, "Metadaten", "width=500,height=500,left=100,top=100,scrollbars=yes,resizable=no");
	Fenster1.focus();
}

function removeChildNodes(node) {
	while (node.childNodes.length > 0) {
		var childNode = node.firstChild;
		node.removeChild(childNode);
	}
}

function controlOperators(checkVal,operator,valType,opValue){
	var opSelect = document.getElementById(operator);
	removeChildNodes(opSelect);
	if(checkVal==true){
		opSelect.disabled = '';
		option1 = new Option("-----","0");
		opSelect.options[opSelect.length] = option1;
		option2 = new Option("%...%","bothside");
		opSelect.options[opSelect.length] = option2;
		option3 = new Option("...%","rightside");
		opSelect.options[opSelect.length] = option3;
		option4 = new Option("equal","equal");
		opSelect.options[opSelect.length] = option4;
		option5 = new Option(">","greater_than");
		opSelect.options[opSelect.length] = option5;
		option6 = new Option("<","less_than");
		opSelect.options[opSelect.length] = option6;
		option7 = new Option(">=","greater_equal_than");
		opSelect.options[opSelect.length] = option7;
		option8 = new Option("<=","less_equal_than");
		opSelect.options[opSelect.length] = option8;
	}
	else{
		opSelect.disabled = 'disabled';
	}
}
</script>

</head>
<body>
<br>
<b>WFS Configuration</b>
<br><br>
<form name='form1' action='<?php echo $self;?>' method='POST' onsubmit='return validate()'>
<a href="mod_wfs_conf.php?<?php echo $urlParameters;?>">new Configuration</a><br><br>
Select WFS Configuration:<br><br>
<?php
/* save wfs_conf properties */

if(isset($_POST["save"])){

        $sql = "UPDATE wfs_conf SET ";
        $sql .= "wfs_conf_abstract = $1, g_label = $2, ";
        $sql .= "g_label_id = $3, g_button = $4, g_button_id = $5, g_style = $6, ";
        $sql .= "g_buffer = $7, g_res_style = $8, g_use_wzgraphics = ";
        if (!empty($_POST["g_use_wzgraphics"])) {
        	$sql .= "1";
        }
        else {
        	$sql .= "0";
        }
        $sql .= " WHERE wfs_conf_id = $9;";
        
        $v = array($_POST["wfs_conf_abstract"], $_POST["g_label"], $_POST["g_label_id"], $_POST["g_button"], $_POST["g_button_id"], $_POST["g_style"], $_POST["g_buffer"], $_POST["g_res_style"], $_POST["gaz"]);
        $t = array("s", "s", "s", "s", "s", "s", "s", "s", "s");
        $res = db_prep_query($sql, $v, $t);
		        
		if (isset($_POST["f_geom"])) {
	        $sql = "UPDATE wfs_conf_element SET f_geom = 1 ";
	        $sql .= "WHERE fkey_wfs_conf_id = $1 AND f_id = $2;";
	        $v = array($_POST["gaz"], $_POST["f_geom"]);
	        $t = array("i", "s");
			$res = db_prep_query($sql, $v, $t);
			
			$sql = "UPDATE wfs_conf_element SET f_geom = 0 ";
	        $sql .= "WHERE fkey_wfs_conf_id = $1 AND f_id <> $2;";
	        $v = array($_POST["gaz"], $_POST["f_geom"]);
	        $t = array("i", "s");
			$res = db_prep_query($sql, $v, $t);
		}
		else {
			$sql = "UPDATE wfs_conf_element SET f_geom = 0 ";
	        $sql .= "WHERE fkey_wfs_conf_id = $1;";
	        $v = array($_POST["gaz"]);
	        $t = array("i");
			$res = db_prep_query($sql, $v, $t);
		}
		
        for($i=0; $i<$_POST["num"]; $i++){
        	
                $sql = "UPDATE wfs_conf_element SET f_search = '";
                if (!empty($_POST["f_search".$i])) {
                	$sql .= "1";
                }
                else {
                	$sql .= "0";
                }
                $sql .= "', f_pos = $1, f_min_input = $2, f_style_id = $3,";
                $sql .= "f_toupper = '" ;
                if (!empty($_POST["f_toupper".$i])) {
                	$sql .= "1";
                }
                else { 
                	$sql .= "0";
                }
                $sql .= "',f_label = $4, f_label_id = $5,";
                $sql .= "f_show = '";
                if (!empty($_POST["f_show".$i])) {
                	$sql .= "1";
                }
                else {
                	$sql .= "0";
                }
				$sql .= "',f_respos = $6,";
                $sql .= "f_edit = '";
                if (!empty($_POST["f_edit".$i])) {
                	$sql .= "1";
                }
                else {
                	$sql .= "0";
                }
				$sql .= "', f_form_element_html = $7,";
                $sql .= "f_mandatory = '";
                if (!empty($_POST["f_mandatory".$i])) {
                	$sql .= "1";
                }
                else {
                	$sql .= "0";
                }
				$sql .= "', f_auth_varname = $8";
				$sql .= ", f_show_detail = '";
                if(!empty($_POST["f_show_detail".$i])){
                	$sql .= "1";
                }
                else {
                	$sql .= "0";
                }
                $sql .= "', f_detailpos = $9";
                $sql .= ", f_operator = $10";
				$sql .= " WHERE fkey_wfs_conf_id = $11 AND f_id = $12;";

				$v = array($_POST["f_pos".$i], $_POST["f_min_input".$i], $_POST["f_style_id".$i], $_POST["f_label".$i], $_POST["f_label_id".$i], $_POST["f_respos".$i], $_POST["f_form_element_html".$i], $_POST["f_auth_varname".$i], $_POST["f_detailpos".$i], $_POST["f_operator".$i], $_POST["gaz"], $_POST["f_id".$i]);
				$t = array("s", "i", "s", "s", "s", "s", "s", "s", "i", "s", "i", "s");
                $res = db_prep_query($sql, $v, $t);
        }
}

/* end save wfs_conf properties */

/* select wfs */

$sql = "SELECT * FROM wfs_conf ORDER BY wfs_conf_id";
$res = db_query($sql);
echo "<select size='10' name='gaz' onchange='submit()'>";
$cnt = 0;
while($row = db_fetch_array($res)){
        echo "<option value='".$row["wfs_conf_id"]."' ";
        if(isset($_POST["gaz"]) && $row["wfs_conf_id"] == $_POST["gaz"]){
                echo "selected";
        }
        echo ">".$row["wfs_conf_id"]." ".$row["wfs_conf_abstract"]."</option>";
        $cnt++;
}
echo "</select>";



/* end select wfs */

function toImage($text) {
	$angle = 90;
	if (extension_loaded("gd2")) {
		return "<img src='../php/createImageFromText.php?text=" . urlencode($text) . "&angle=" . $angle . "'>";
	}
	return $text;
}

/* configure elements */
if (isset($_POST["gaz"])) {
        $sql = "SELECT * FROM wfs_conf WHERE wfs_conf_id = $1";
        $v = array($_POST["gaz"]);
        $t = array("i");
        $res = db_prep_query($sql, $v, $t);
        if($row = db_fetch_array($res)){
                echo "<table>";
                echo "<tr><td>GazetterID:</td><td>".$row["wfs_conf_id"]."</td></tr>" ;
                echo "<tr><td>Abstract:</td><td><input type='text' name='wfs_conf_abstract' value='".$row["wfs_conf_abstract"]."'></td></tr>" ;
                echo "<tr><td>Label:</td><td><input type='text' name='g_label' value='".$row["g_label"]."'></td></tr>" ;
                echo "<tr><td>Label_id:</td><td><input type='text' name='g_label_id' value='".$row["g_label_id"]."'></td></tr>" ;
                echo "<tr><td>Button:</td><td><input type='text' name='g_button' value='".$row["g_button"]."'></td></tr>" ;
                echo "<tr><td>Button_id:</td><td><input type='text' name='g_button_id' value='".$row["g_button_id"]."'></td></tr>" ;
                echo "<tr><td>Style:</td><td><textarea cols=50 rows=5 name='g_style'>".$row["g_style"]."</textarea></td></tr>" ;
                echo "<tr><td>Buffer:</td><td><input type='text' size='4' name='g_buffer' value='".$row["g_buffer"]."'></td></tr>" ;
                echo "<tr><td>ResultStyle:</td><td><textarea cols=50 rows=5 name='g_res_style'>".$row["g_res_style"]."</textarea></td></tr>" ;
//                echo "<tr><td>WZ-Graphics:</td><td><input name='g_use_wzgraphics' type='checkbox'";
//                if($row["g_use_wzgraphics"] == 1){ echo " checked"; }
//                echo "></td></tr>";
                echo "</table>";
        }

        /* set element options */
        $sql = "SELECT * FROM wfs_conf_element ";
        $sql .= "JOIN wfs_element ON wfs_conf_element.f_id = wfs_element.element_id ";
        $sql .= "WHERE fkey_wfs_conf_id = $1 ORDER BY f_id";
		$v = array($_POST["gaz"]);
		$t = array("i");
        $res = db_prep_query($sql, $v, $t);
		
        echo "<table border='1'>";
        echo "<tr valign = bottom>";
                echo "<td>" . toImage('ID') . "</td>";
                echo "<td>" . toImage('name / type') . "</td>";
                echo "<td>" . toImage('geom') . "</td>";
                echo "<td>" . toImage('search') . "</td>";
                echo "<td>" . toImage('pos') . "</td>";
                echo "<td>" . toImage('minimum_input') . "</td>";
                echo "<td>" . toImage('style_id') . "</td>";
                echo "<td>" . toImage('upper') . "</td>";
                echo "<td>" . toImage('label') . "</td>";
                echo "<td>" . toImage('label_id') . "</td>";
                echo "<td>" . toImage('show') . "</td>";
                echo "<td>" . toImage('position') . "</td>";
                echo "<td>" . toImage('show_detail') . "</td>";
                echo "<td>" . toImage('detail_position') . "</td>";
                echo "<td>" . toImage('mandatory') . "</td>";
                echo "<td>" . toImage('edit') . "</td>";
                echo "<td>" . toImage('html') . "</td>";
                echo "<td>" . toImage('auth') . "</td>";
                echo "<td>" . toImage('operator') . "</td>";
        echo "</tr>";
        $cnt = 0;
        while($row = db_fetch_array($res)){
                echo "<tr>";
                echo "<td><input type='text' size='1' name='f_id".$cnt."' value='".$row["f_id"]."' readonly></td>";
                echo "<td>".$row["element_name"]."<br>";
				if ($row["element_type"]) {
					echo "<div style='font-size:10'>(".$row["element_type"].")</div>";
				}
				echo "</td>";
                echo "<td><input name='f_geom' type='radio' value='".$row["f_id"]."' ";
                	if($row["f_geom"] == 1){ echo " checked"; }
				echo "></td>";
                echo "<td><input name='f_search".$cnt."' type='checkbox'";
                if($row["f_search"] == 1){ echo " checked"; }
                echo " onclick='controlOperators(document.forms[0].f_search".$cnt.".checked,\"f_operator".$cnt."\",\"".$row["element_type"]."\",\"".$row["f_operator"]."\");'></td>";
                echo "<td><input name='f_pos".$cnt."' type='text' size='1' value='".$row["f_pos"]."'></td>";
                echo "<td><select name='f_min_input".$cnt."' id='f_min_input".$cnt."' ";
                echo ">";
                echo "<option value='0' ";
                if($row["f_min_input"] == 0){ echo " selected"; } echo ">-----</option>";
                echo "<option value='1' ";
                if($row["f_min_input"] == 1){ echo " selected"; } echo ">1</option>";
                echo "<option value='2' ";
                if($row["f_min_input"] == 2){ echo " selected"; } echo ">2</option>";
                echo "<option value='3' ";
                if($row["f_min_input"] == 3){ echo " selected"; } echo ">3</option>";
                echo "<option value='4' ";
                if($row["f_min_input"] == 4){ echo " selected"; } echo ">4</option>";
                echo "<option value='5' ";
                if($row["f_min_input"] == 5){ echo " selected"; } echo ">5</option>";
                echo "</select></td>";				
                echo "<td><input name='f_style_id".$cnt."' type='text' size='2' value='".$row["f_style_id"]."'></td>";
                echo "<td><input name='f_toupper".$cnt."' type='checkbox'";
                if($row["f_toupper"] == 1){ echo " checked"; }
                echo "></td>";
                echo "<td><input name='f_label".$cnt."' type='text' size='4' value=\"".htmlentities($row["f_label"], ENT_QUOTES, "UTF-8")."\"></td>";
                echo "<td><input name='f_label_id".$cnt."' type='text' size='2' value=\"".htmlentities($row["f_label_id"], ENT_QUOTES, "UTF-8")."\"></td>";
                echo "<td><input name='f_show".$cnt."' type='checkbox'";
                if($row["f_show"] == 1){ echo " checked"; }
                echo "></td>";
                echo "<td><input name='f_respos".$cnt."' type='text' size='1' value='".$row["f_respos"]."'></td>";
                echo "<td><input name='f_show_detail".$cnt."' type='checkbox'";
                if($row["f_show_detail"] == 1){ echo " checked"; }
                echo "></td>";
                echo "<td><input name='f_detailpos".$cnt."' type='text' size='1' value='".$row["f_detailpos"]."'></td>";
                echo "<td><input name='f_mandatory".$cnt."' type='checkbox'";
                if($row["f_mandatory"] == 1){ echo " checked"; }
                echo "></td>";
                echo "<td><input name='f_edit".$cnt."' type='checkbox'";
                if($row["f_edit"] == 1){ echo " checked"; }
                echo "></td>";
                echo "<td><textarea name='f_form_element_html".$cnt."' cols='15' rows='1' >".htmlentities($row["f_form_element_html"], ENT_QUOTES, "UTF-8")."</textarea></td>";
                echo "<td><input name='f_auth_varname$cnt' type='text' size='8' value=\"" . htmlentities($row["f_auth_varname"], ENT_QUOTES, "UTF-8") . "\"></td>";
                echo "<td><select name='f_operator".$cnt."' id='f_operator".$cnt."' ";
                if($row["f_search"] != 1){
                	echo "disabled";
                }
                echo ">";
                echo "<option value='0' ";
                if($row["f_operator"] == 0){ echo " selected"; }
				echo ">-----</option>";
				echo "<option value='bothside' ";
                if($row["f_operator"] == 'bothside'){ echo " selected"; }
				echo ">%...%</option>";
				echo "<option value='rightside' ";
                if($row["f_operator"] == 'rightside'){ echo " selected"; }
				echo ">...%</option>";
				echo "<option value='equal' ";
                if($row["f_operator"] == 'equal'){ echo " selected"; }
				echo ">equal</option>";
				echo "<option value='greater_than' ";
                if($row["f_operator"] == 'greater_than'){ echo " selected"; }
				echo ">></option>";
				echo "<option value='less_than' ";
                if($row["f_operator"] == 'less_than'){ echo " selected"; }
				echo "><</option>";
				echo "<option value='less_equal_than' ";
                if($row["f_operator"] == 'less_equal_than'){ echo " selected"; }
				echo "><=</option>";
				echo "<option value='greater_equal_than' ";
                if($row["f_operator"] == 'greater_equal_than'){ echo " selected"; }
				echo ">>=</option>";
				
     			echo "</select></td>";
                echo "</tr>";
                $cnt++;
        }
        echo "</table>";
        echo "<input type='hidden' name='num' value='".$cnt."'>";
        echo "<input type='submit' name='save' value='save'>";
}


/* end configure elements */
?>
</form>
</body>
