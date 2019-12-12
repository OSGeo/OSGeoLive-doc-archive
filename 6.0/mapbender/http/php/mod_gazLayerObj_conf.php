<?php
# $Id: mod_gazLayerObj_conf.php 2413 2008-04-23 16:21:04Z christoph $
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

<?php
echo "<input type='hidden' name='gui' value='".$_REQUEST["gui"]."'>";	
echo "<input type='hidden' name='wms' value='".$_REQUEST["wms"]."'>";
echo "<input type='hidden' name='layer' value='".$_REQUEST["layer"]."'>";


$aWFS = new wfs_conf();
$aWFS->getallwfs();	

/* save gazetteer properties */

if(isset($_REQUEST["save"])){
	$sql = "UPDATE gui_layer SET gui_layer_wfs_featuretype = $1 ";
	$sql .= "WHERE fkey_gui_id = $2 AND fkey_layer_id = $3";
	$v = array($_REQUEST["myWFS"], $_REQUEST["gui"], $_REQUEST["layer"]);
	$t = array("s", "s", "i");
	$res = db_prep_query($sql, $v, $t);
	echo "layer is connected with: ".$_REQUEST["myWFS"];
	die();
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
			$str_request = $aWFS->wfs_getfeature[$i]."&REQUEST=getFeature&VERSION=".$aWFS->wfs_version[$i]."&SERVICE=WFS";
		}
	}
	
	$aWFS->getfeatures($_REQUEST["wfs"]);
	echo "<table>";
	for($i=0; $i<count($aWFS->features->featuretype_id); $i++){
		echo "<tr>";
		echo "<td><input type='radio' name='featuretype' value='".$aWFS->features->featuretype_id[$i]."' onclick='submit()' ";
		if(isset($_REQUEST["featuretype"]) && $_REQUEST["featuretype"] == $aWFS->features->featuretype_id[$i]){
			echo "checked ";
			$str_request = $str_request . "&Typename=".$aWFS->features->featuretype_name[$i];
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
echo "<input type='hidden' name='myWFS' value='".$str_request."'>";
echo "<input type='submit' name='save' value='save'>";	
}


/* end configure elements */
?>
</form>
</body>
