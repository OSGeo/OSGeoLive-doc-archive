<?php
# $Id: mod_wfs_conf.php 4691 2009-09-25 10:39:48Z christoph $
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

#include '../include/dyn_css.php';
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

<title>wfs_conf</title>
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
	return true;
}

function removeChildNodes(node) {
	while (node.childNodes.length > 0) {
		var childNode = node.firstChild;
		node.removeChild(childNode);
	}
}

function controlOperators(checkVal,operatorField,valType){
	var opSelect = document.getElementById(operatorField);
	removeChildNodes(opSelect);
	option1 = new Option("-----","0");
	opSelect.options[opSelect.length] = option1;
	if(checkVal==true){
		opSelect.disabled = '';
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
<br>
<form method='POST' action='<?php echo $self;?>'onsubmit='return validate()'>
<br>
<a href="mod_wfs_edit.php?<?php echo $urlParameters;?>">edit WFS Configuration</a><br><br>
Select WFS:&nbsp;
<?php
$aWFS = new wfs_conf();
$aWFS->getallwfs(Mapbender::session()->get("mb_user_id"));


function toImage($text) {
	$angle = 90;
	if (extension_loaded("gd2")) {
		return "<img src='../php/createImageFromText.php?text=" . urlencode($text) . "&angle=" . $angle . "'>";
	}
	return $text;
}

/* save wfs_conf properties */

if(isset($_POST["save"])){

        db_select_db($DB,$con);

        $sql = "INSERT INTO wfs_conf (";
        $sql .= "wfs_conf_abstract, fkey_wfs_id, ";
        $sql .= "fkey_featuretype_id, g_label, g_label_id, g_button, ";
        $sql .= "g_button_id, g_style, g_buffer, g_res_style, g_use_wzgraphics";
		$sql .= ") VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, ";
        if (!empty($_POST["g_use_wzgraphics"])) {
			$sql .= "'1'";
		}
		else {
			$sql .= "'0'";
		}
        $sql .= "); ";
        
		$v = array(
			$_POST["wfs_conf_abstract"], 
			$_POST["wfs"], 
			$_POST["featuretype"], 
			$_POST["g_label"], 
			$_POST["g_label_id"], 
			$_POST["g_button"], 
			$_POST["g_button_id"], 
			$_POST["g_style"], 
			$_POST["g_buffer"], 
			$_POST["g_res_style"]
		);
		$t = array("s", "s", "s", "s", "s", "s", "s", "s", "s", "s");
        $res = db_prep_query($sql, $v, $t);
        
        $wfsID = db_insert_id($con,'wfs_conf','wfs_conf_id');

        for ($i = 0; $i < $_POST["num"]; $i++){
                $sql = "INSERT INTO wfs_conf_element (fkey_wfs_conf_id,f_id,f_search,f_pos,f_min_input,f_style_id,f_toupper,f_label,f_label_id,f_show,f_respos,f_edit,f_form_element_html,f_mandatory,f_auth_varname,f_show_detail,f_detailpos,f_operator) VALUES(";
                $sql .= "$1, $2, ";
                if (!empty($_POST["f_search".$i])) {
                	$sql .= "'1'";
                }
                else {
                	$sql .= "'0'";
                }
                $sql .= ", $3, $4, $5, ";
				if (!empty($_POST["f_toupper".$i])) {
                	$sql .= "'1'";
                }
                else {
                	$sql .= "'0'";
                }				
                $sql .= ",$6, $7, ";
                if (!empty($_POST["f_show".$i])) {
                	$sql .= "'1'";
                }
                else {
                	$sql .= "'0'";
                }
                $sql .= ", $8, ";
                if (!empty($_POST["f_edit".$i])) {
                	$sql .= "'1'";
                } 
                else {
                	$sql .= "'0'";
                }
                $sql .= ",$9, ";
                if (!empty($_POST["f_mandatory".$i])) {
                	$sql .= "'1'";
                }
                else {
                	$sql .= "'0'";
                }
                $sql .= ",$10,";
                if(!empty($_POST["f_show_detail".$i])){
                	$sql .= "'1'";
                }
                else {
                	$sql .= "'0'";
                }
                $sql .= ",$11,$12";
 				$sql .= "); ";

				$v = array($wfsID, $_POST["f_id".$i], $_POST["f_pos".$i], $_POST["f_min_input".$i], $_POST["f_style_id".$i], $_POST["f_label".$i], $_POST["f_label_id".$i], $_POST["f_respos".$i], $_POST["f_form_element_html".$i], $_POST["f_auth_varname".$i], $_POST["f_detailpos".$i], $_POST["f_operator".$i]);
				$t = array("i", "s", "s", "i", "s", "s", "s", "i", "s", "s", "i", "s");
                $res = db_prep_query($sql, $v, $t);
        }
        if (isset($_POST["f_geom"])) {
	        $sql = "UPDATE wfs_conf_element SET f_geom = 1 ";
	        $sql .= "WHERE fkey_wfs_conf_id = $1 AND f_id = $2;";
	        $v = array($wfsID, $_POST["f_geom"]);
	        $t = array("i", "i");
			$res = db_prep_query($sql, $v, $t);
        }
		
		echo "<script language='javascript'>";
		echo "document.location.href = 'mod_wfs_edit.php?gaz=".$wfsID."';";
		echo "</script>";
}

/* end save wfs_conf properties */

/* select wfs */

if(isset($_POST["wfs"]) && $_POST["wfs"] == ""){
        unset($_POST["wfs"]);
        unset($_POST["featuretype"]);
}

echo "<select name='wfs' onchange='selectWFS()'>";
echo "<option value=''>...</option>";
for($i=0; $i<count($aWFS->wfs_id);$i++){
        echo "<option value='".$aWFS->wfs_id[$i]."' ";
        if(isset($_POST["wfs"]) && $aWFS->wfs_id[$i] == $_POST["wfs"]){
                echo "selected";
        }
        echo ">".$aWFS->wfs_id[$i]." ".$aWFS->wfs_title[$i]."</option>";
}
echo "</select>";
echo "";

/* end select wfs */


/* select featuretype */

if(isset($_POST["wfs"])){

        for($i=0; $i<count($aWFS->wfs_id);$i++){
                if($aWFS->wfs_id[$i] == $_POST["wfs"]){
                        echo "<table>";
                        echo "<tr><td>ID:</td><td>".$aWFS->wfs_id[$i]."</td></tr>";
                        echo "<tr><td>Name:</td><td>".$aWFS->wfs_name[$i]."</td></tr>";
                        echo "<tr><td>Title:</td><td>".$aWFS->wfs_title[$i]."</td></tr>";
                        echo "<tr><td>Abstract:</td><td>".$aWFS->wfs_abstract[$i]."</td></tr>";
                        echo "<tr><td>Capabilities:</td><td>".$aWFS->wfs_getcapabilities[$i]."</td></tr>";
                        echo "<tr><td>FeatureTypes:</td><td>".$aWFS->wfs_describefeaturetype[$i]."</td></tr>";
                        echo "<tr><td>Feature:</td><td>".$aWFS->wfs_getfeature[$i]."</td></tr>";
                        echo "</table>";
                }
        }

        $aWFS->getfeatures($_POST["wfs"]);
        echo "<table>";
        for($i=0; $i<count($aWFS->features->featuretype_id); $i++){
                echo "<tr>";
                echo "<td><input type='radio' name='featuretype' value='".$aWFS->features->featuretype_id[$i]."' onclick='submit()' ";
                if(isset($_POST["featuretype"]) && $_POST["featuretype"] == $aWFS->features->featuretype_id[$i]){
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
if(isset($_POST["featuretype"])){


        for($i=0; $i<count($aWFS->features->featuretype_id); $i++){
                if($_POST["featuretype"] == $aWFS->features->featuretype_id[$i]){
                        echo "<hr>SRS: ".$aWFS->features->featuretype_srs[$i];
                }
        }

        /* set featuretype options */
        echo "<table>";
        echo "<tr><td>Abstract:</td><td><input type='text' name='wfs_conf_abstract'></td></tr>" ;
        echo "<tr><td>Label:</td><td><input type='text' name='g_label'></td></tr>" ;
        echo "<tr><td>Label_id:</td><td><input type='text' name='g_label_id'></td></tr>" ;
        echo "<tr><td>Button:</td><td><input type='text' name='g_button'></td></tr>" ;
        echo "<tr><td>Button_id:</td><td><input type='text' name='g_button_id'></td></tr>" ;
        echo "<tr><td>Style:</td><td><textarea cols=50 rows=5 name='g_style'></textarea></td></tr>" ;
        echo "<tr><td>Buffer:</td><td><input type='text' size='4' name='g_buffer' value='1'></td></tr>" ;
        echo "<tr><td>ResultStyle:</td><td><textarea cols=50 rows=5 name='g_res_style'></textarea></td></tr>" ;
//        echo "<tr><td>WZ-Graphics:</td><td><input name='g_use_wzgraphics' type='checkbox'></td></tr>";
        echo "</table>";


        /* set element options */
        $aWFS->getelements($_POST["featuretype"]);
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

        for($i=0; $i<count($aWFS->elements->element_id); $i++){
                echo "<tr>";
                echo "<td>".$aWFS->elements->element_id[$i]."<input type='hidden' name='f_id".$i."' value='".$aWFS->elements->element_id[$i]."'></td>";
                echo "<td>".$aWFS->elements->element_name[$i]."<br><div style='font-size:10'>".$aWFS->elements->element_type[$i]."</div></td>";
                echo "<td><input name='f_geom' type='radio' value='".$aWFS->elements->element_id[$i]."'></td>";
                echo "<td><input name='f_search".$i."' type='checkbox' onclick='controlOperators(document.forms[0].f_search".$i.".checked,\"f_operator".$i."\",\"".$aWFS->elements->element_type[$i]."\");'></td>";
                echo "<td><input name='f_pos".$i."' type='text' size='1' value='0'></td>";
                echo "<td><select name='f_min_input".$i."' id='f_min_input".$i."'>";
                echo "<option value='0'>-----</option>";
                echo "<option value='1'>1</option>";
                echo "<option value='2'>2</option>";
                echo "<option value='3'>3</option>";
                echo "<option value='4'>4</option>";
                echo "<option value='5'>5</option>";
                echo "</select></td>";
                echo "<td><input name='f_style_id".$i."' type='text' size='2' value='0'></td>";
                echo "<td><input name='f_toupper".$i."' type='checkbox'></td>";
                echo "<td><input name='f_label".$i."' type='text' size='4'></td>";
                echo "<td><input name='f_label_id".$i."' type='text' size='2'  value='0'></td>";
                echo "<td><input name='f_show".$i."' type='checkbox'></td>";
                echo "<td><input name='f_respos".$i."' type='text' size='1' value='0'></td>";
                echo "<td><input name='f_show_detail".$i."' type='checkbox'></td>";
                echo "<td><input name='f_detailpos".$i."' type='text' size='1' value='0'></td>";
                echo "<td><input name='f_mandatory".$i."' type='checkbox'></td>";
                echo "<td><input name='f_edit".$i."' type='checkbox'></td>";
                echo "<td><textarea name='f_form_element_html".$i."' cols='15' rows='1' ></textarea></td>";
                echo "<td><input name='f_auth_varname".$i."' type='text' size='8' value=''></td>";
                echo "<td><select name='f_operator".$i."' id='f_operator".$i."' disabled>";
                echo "<option value='0'>-----</option>";
                echo "</select></td>";
                echo "</tr>";
        }
        echo "</table>";
        echo "<input type='hidden' name='num' value='".$i."'>";
        echo "<input type='submit' name='save' value='save'>";
}


/* end configure elements */
?>
</form>
</body>
