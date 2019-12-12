<?php
# $Id: mod_editElements.php 8349 2012-05-11 07:43:18Z verenadiewald $
# http://www.mapbender.org/index.php/mod_editElements.php
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

$e_id="editElements";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done
 */
security_patch_log(__FILE__,__LINE__);
//import_request_variables("PG");
$postvars=array("guiList1","guiDesc","guiId","guiList2","myElement","e_id_","orig_e_id_","e_pos","e_public","e_comment",
    "e_title","e_element","e_src","e_attributes","e_left","e_top","e_width","e_height","e_z_index","e_more_styles",
    "e_content","e_closetag","e_js_file","e_mb_mod","e_target","e_requires","e_url",
    "originGuiOfSelectedElement","myDelete","myDuplicate","mySave","myShow","all");
foreach($postvars as $value){
        $$value=$_POST[$value];
}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if (isset($_GET["guiList1"])){
        $guiList1=$_GET["guiList1"];
    }
    if (isset($_GET["guiList2"])){
        $guiList2=$_GET["guiList2"];
    }
}
//import_request_variables("PG");
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
<title>Edit Elements</title>
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
   .guiList1{
   	position:absolute;
   	top:30px;
   	left:10px;
   	width:200px
   }
   .buttonbar{
   	position:absolute;
   	top:60px;
   	left:10px;
   }
   .guiList1_text{
   	position:absolute;
   	top:10px;
   	left:10px;
      font-size:16px;
      color: #0066cc;
   }
   .guiList2{
   	position:absolute;
   	top:40px;
   	left:400px;
   	width:200px
   }
   .all{
   	position:absolute;
   	top:38px;
   	left:610px;
   }
   .guiList2_header{
   	position:absolute;
   	top:10px;
   	left:400px;
      font-size:16px;
      color: #0066cc;
   }
   .myElements{
   	position:absolute;
   	top:70px;
   	left:400px;
   }
   .myTable{
   	border: 1px solid;
      font-size: 11px;
   }
   .myForm{
   	position:absolute;
   	top:90px;
   	left:10px;
   }
   .textfield{
      width:277px
   }
   .textfield_small{
      width:150px
   }
   .on{
      color: #0066cc;
   }
   .templateTable{
		background-color:lightgrey;
   }
   -->
</style>
<?php

if((isset($myDelete) && $myDelete == '1') || (isset($mySave) && ($mySave == '1' || $mySave == '2')) || (isset($myDuplicate) && $myDuplicate == '1')){
    $checkAdmin = new administration();
    $myOwnedGuis = $checkAdmin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
    $modOwnerAlert = false;
    $ownerAlert = "You do not have the permission to change the elements of this application.";
    
    if($_POST["originGuiOfSelectedElement"] && !in_array($_POST["originGuiOfSelectedElement"], $myOwnedGuis)) {
    if($modOwnerAlert == false){
    		$modOwnerAlert = true;
    	}
    }
    if($modOwnerAlert == true){
    	unset($myDelete);
    	unset($mySave);
    	unset($myDuplicate);
    	echo "<script language='JavaScript'>";
    	echo "alert('".$ownerAlert."');";
    	echo "</script>";
    
    }
}
	
# handle database updates etc.....
if((isset($mySave) && ($mySave == '1' || $mySave == '2')) || isset($myDuplicate)) {
	# check module-permission ---------------------------------------------------------
	# e_attributes
	# e_js_file
	# e_mb_mod
	# e_src
	$check = CHECK;
	$alert = "Security alert: You do not have the permission to use the specified ";
	$alert .= "module-path. Please contact your mapbender system administrator.";

	$mod = new administration();
	$aGuis = $mod->getGuisByPermission(Mapbender::session()->get("mb_user_id"),true);
	$modPermAlert = false;

	if($check == true){
		if(isset($e_attributes) && preg_match("/((\w+|\/)+.php)/i", $e_attributes, $matches)){
			$modPerm = $mod->checkModulePermission($aGuis, $matches[1], "e_attributes");
			if($modPerm == false){
				$modPermAlert = true;
			}
		}
		if(isset($e_js_file) && preg_match("/((\w+|\/)+.php)/i", $e_js_file, $matches)){
			$modPerm = $mod->checkModulePermission($aGuis, $matches[1], "e_js_file");
			if($modPerm == false){
				$modPermAlert = true;
			}
		}
		if(isset($e_mb_mod) && preg_match("/((\w+|\/)+.php)/i", $e_mb_mod, $matches)){
			$modPerm = $mod->checkModulePermission($aGuis, $matches[1], "e_mb_mod");
			if($modPerm == false){
				$modPermAlert = true;
			}
		}
		if(isset($e_src) && preg_match("/((\w+|\/)+.php)/i", $e_src, $matches)){
			$modPerm = $mod->checkModulePermission($aGuis, $matches[1], "e_src");

			if($modPerm == false){
				$modPermAlert = true;
			}
		}
		if($modPermAlert == true){
			unset($mySave);
			echo "<script language='JavaScript'>";
			echo "alert('".$alert."');";
			echo "</script>";

		}
	}

	# end permission-check -------------------------------------------------------------

	if ($mySave == '1'){
		//copy element vars
		//If the name of the element  (e_id) was  changed in the update, then the elementvarrs must be copied from the old e_id to the new e_id
		$sql = "SELECT * FROM gui_element_vars WHERE fkey_e_id = $1 AND fkey_gui_id = $2";
		$v = array($orig_e_id_,$_POST["originGuiOfSelectedElement"]);
		$t = array('s','s');
		$c = 0;
		$res_vars = db_prep_query($sql,$v,$t);
	
		db_begin();
		
		$sql = "DELETE FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2 ";
		$v = array($orig_e_id_,$guiList1);
		$t = array('s','s');
		$res = db_prep_query($sql,$v,$t);
	
		if($e_left == ''){$e_left = NULL;}
		if($e_top == ''){$e_top = NULL;}
		if($e_width < 1){$e_width = NULL;}
		if($e_height < 1){$e_height = NULL;}
		if($e_z_index < 1){$e_z_index = NULL;}
		if($e_pos == ''){$e_pos = 2;}
		if($e_public == ''){$e_public = 1;}	
		
		$sql = "INSERT INTO gui_element(fkey_gui_id,e_id,e_pos,e_public,e_comment,e_element,e_src,";
		$sql .= "e_attributes,e_left,e_top,e_width,e_height,e_z_index,e_more_styles,e_content,";
		$sql .= "e_closetag,e_js_file,e_mb_mod,e_target,e_requires,e_url,e_title) ";
		$sql .= "VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22)";
		$v = array($guiList1,$e_id_,$e_pos,$e_public,$e_comment,$e_element,$e_src,$e_attributes,$e_left,
			$e_top,$e_width,$e_height,$e_z_index,$e_more_styles,$e_content,$e_closetag,$e_js_file,
			$e_mb_mod,$e_target,$e_requires,$e_url,$e_title);
		$t = array('s','s','i','i','s','s','s','s','i','i','i','i','i','s','s','s','s','s','s','s','s','s');
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			db_rollback();	
		}
	
		//copy element vars
		
		while($row = db_fetch_array($res_vars)){	
			$sql = array();
			$v = array();
			$t = array();
			$r = array();	
			$sql[$c] = "INSERT INTO gui_element_vars (fkey_gui_id,fkey_e_id,var_name,var_value,context,var_type) ";
			$sql[$c] .= "VALUES ($1,$2,$3,$4,$5,$6)";
			$v[$c] = array($guiList1,$e_id_,$row["var_name"],$row["var_value"],$row["context"],$row["var_type"]);
			$t[$c] = array('s','s','s','s','s','s');
			$r[$c] = db_prep_query($sql[$c],$v[$c],$t[$c]);
			if(!$r){
				db_rollback();	
			}
			$c++;
		}
		db_commit();
	}
	# mySave == 2 <=> just save GUI description
	elseif ($mySave == '2') {
		$sql = "UPDATE gui SET gui_description = $1 WHERE gui_id = $2";
		$v = array($guiDesc,$guiId);
		$t = array('s','s');
		$res = db_prep_query($sql,$v,$t);
	}elseif($myDuplicate == '1'){
		//copy element vars
		//If the name of the element  (e_id) was  changed in the update, then the elementvarrs must be copied from the old e_id to the new e_id
		$sql = "SELECT * FROM gui_element_vars WHERE fkey_e_id = $1 AND fkey_gui_id = $2";
		$v = array($orig_e_id_,$_POST["originGuiOfSelectedElement"]);
		$t = array('s','s');
		$c = 0;
		$res_vars = db_prep_query($sql,$v,$t);
	
		db_begin();
		
		//$sql = "DELETE FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2 ";
		//$v = array($orig_e_id_,$guiList1);
		//$t = array('s','s');
		//$res = db_prep_query($sql,$v,$t);
	
		if($e_left == ''){$e_left = NULL;}
		if($e_top == ''){$e_top = NULL;}
		if($e_width < 1){$e_width = NULL;}
		if($e_height < 1){$e_height = NULL;}
		if($e_z_index < 1){$e_z_index = NULL;}
		if($e_pos == ''){$e_pos = 2;}
		if($e_public == ''){$e_public = 1;}	
		
		$sql = "INSERT INTO gui_element(fkey_gui_id,e_id,e_pos,e_public,e_comment,e_element,e_src,";
		$sql .= "e_attributes,e_left,e_top,e_width,e_height,e_z_index,e_more_styles,e_content,";
		$sql .= "e_closetag,e_js_file,e_mb_mod,e_target,e_requires,e_url,e_title) ";
		$sql .= "VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22)";
		$v = array($guiList1,$e_id_,$e_pos,$e_public,$e_comment,$e_element,$e_src,$e_attributes,$e_left,
			$e_top,$e_width,$e_height,$e_z_index,$e_more_styles,$e_content,$e_closetag,$e_js_file,
			$e_mb_mod,$e_target,$e_requires,$e_url,$e_title);
		$t = array('s','s','i','i','s','s','s','s','i','i','i','i','i','s','s','s','s','s','s','s','s','s');
		$res = db_prep_query($sql,$v,$t);
		if(!$res){
			db_rollback();	
		}
	
		//copy element vars
		
		while($row = db_fetch_array($res_vars)){	
			$sql = array();
			$v = array();
			$t = array();
			$r = array();	
			$sql[$c] = "INSERT INTO gui_element_vars (fkey_gui_id,fkey_e_id,var_name,var_value,context,var_type) ";
			$sql[$c] .= "VALUES ($1,$2,$3,$4,$5,$6)";
			$v[$c] = array($guiList1,$e_id_,$row["var_name"],$row["var_value"],$row["context"],$row["var_type"]);
			$t[$c] = array('s','s','s','s','s','s');
			$r[$c] = db_prep_query($sql[$c],$v[$c],$t[$c]);
			if(!$r){
				db_rollback();	
			}
			$c++;
		}
		db_commit();

	}

}
if(isset($myDelete) && $myDelete == '1'){
    $sql = "DELETE FROM gui_element WHERE e_id = $1 AND fkey_gui_id = $2";   
	$v = array($e_id_,$guiList1);
	$t = array('s','s');
	$res = db_prep_query($sql,$v,$t);
	$e_id_ = ""; $e_pos = ""; $e_public = ""; $e_comment = "";  $e_title = ""; $e_element = "";
	$e_src = ""; $e_attributes = ""; $e_left = ""; $e_top = ""; $e_width = ""; $e_height = ""; $e_z_index = "";
	$e_more_styles = ""; $e_content = ""; $e_closetag = ""; $e_js_file = ""; $e_mb_mod = ""; 
	$e_target = ""; $e_requires = ""; $e_url = "";
}
if(isset($myShow) && $myShow == '1'){
	 Mapbender::session()->set("mb_user_myGui",$guiList1);

   echo "<script language='javascript'>";
   echo "window.open('../frames/login.php?".strip_tags (SID)."&name=".Mapbender::session()->get("mb_user_name")."&password=".urlencode(Mapbender::session()->get("mb_user_password"))."&mb_user_myGui=".$guiList1."','','');";
   echo "</script>";
}
if(isset($all) && $all == '1'){
	$sql = "SELECT * FROM gui_element WHERE fkey_gui_id = $1";
	$v = array($guiList2);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	db_begin();
	while($row = db_fetch_array($res)){
		$sql_del = "DELETE FROM gui_element WHERE fkey_gui_id = $1 AND e_id = $2";
		$v = array($guiList1,$row["e_id"]);
		$t = array('s','s');
		$res_del = db_prep_query($sql_del,$v,$t);
		if($row["e_left"] == ""){$myleft = NULL;} else{$myleft = $row["e_left"];}
		if($row["e_top"] == ""){$mytop = NULL;} else{$mytop = $row["e_top"];}
		if($row["e_width"] == ""){$mywidth = NULL;} else{$mywidth = $row["e_width"];}
		if($row["e_height"] == ""){$myheight = NULL;} else{$myheight = $row["e_height"];}
		if($row["e_z_index"] == ""){$my_z_index = NULL;} else{$my_z_index = $row["e_z_index"];}

		$sql_ins = "INSERT INTO gui_element(fkey_gui_id,e_id,e_pos,e_public,e_comment,e_element,";
		$sql_ins .= "e_src,e_attributes,e_left,e_top,e_width,e_height,e_z_index,e_more_styles,";
		$sql_ins .= "e_content,e_closetag,e_js_file,e_mb_mod,e_target,e_requires,e_url,e_title) ";
		$sql_ins .= "VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22)";
		$v = array($guiList1,$row["e_id"],$row["e_pos"],$row["e_public"],$row["e_comment"],$row["e_element"],
			$row["e_src"],$row["e_attributes"],$myleft,$mytop,$mywidth,$myheight,$my_z_index,
			$row["e_more_styles"],$row["e_content"],$row["e_closetag"],$row["e_js_file"],$row["e_mb_mod"],
			$row["e_target"],$row["e_requires"],$row["e_url"],$row["e_title"]);
		$t = array('s','s','i','i','s','s','s','s','i','i','i','i','i','s','s','s','s','s','s','s','s','s');
		$res_ins = db_prep_query($sql_ins,$v,$t);
		if(!$res_ins){db_rollback(); }
		$cnt++;
	}
	$sql = "SELECT * FROM gui_element_vars WHERE fkey_gui_id = $1";
	$v = array($guiList2);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	while($row = db_fetch_array($res)){
		$sql_ins2 = "INSERT INTO gui_element_vars(fkey_gui_id,fkey_e_id,var_name,var_value,context,var_type) ";
		$sql_ins2 .= "VALUES ($1,$2,$3,$4,$5,$6)";
		$v = array($guiList1,$row["fkey_e_id"],$row["var_name"],$row["var_value"],$row["context"],$row["var_type"]);
		$t = array('s','s','s','s','s','s');
		$res_ins2 = db_prep_query($sql_ins2,$v,$t);
		if(!$res_ins2){db_rollback(); }
		$cnt++;
	}
	db_commit();
}
# end
echo "<script language='javascript'>";
echo "var guiIDs = new Array();";
if(isset($guiList1)){
	$sql = "SELECT e_id FROM gui_element WHERE  fkey_gui_id = $1";
	$v = array($guiList1);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	while($row = db_fetch_array($res)){
		echo  "guiIDs[".$cnt."] = '".$row["e_id"]."'; ";
		$cnt++;
	}
}
echo "</script>";
?>
<script type="text/javascript">
<!--
function setOriginGui(){
   document.form1.originGuiOfSelectedElement.value = document.form1.guiList2.value;
}

function setGui(val){
   document.form1.guiList2.value = val;
   document.form1.submit();
}
function clearRadio(){
   for(var i=0; i< document.form1.elements.length; i++){
      if(document.form1.elements[i].type == "radio"){
         document.form1.elements[i].checked = false;
      }
   }
}
function thisSave(){
   if(document.form1.e_id_.value == ""){
      alert("ID ? ");
      document.form1.e_id_.focus();
      return;
   }
   var permission = false;
   var isElement = false;
   for(var i=0; i<guiIDs.length; i++){
      clearRadio();
      if(document.form1.e_id_.value == guiIDs[i]){
         permission = confirm("update: " + document.form1.e_id_.value + " ?");
         isElement = true;
         break;
      }
   }
   if(permission == true || isElement == false){
      document.form1.mySave.value = 1;
      document.form1.submit();
   }
}

function thisDelete(){
   clearRadio();
   var permission =  confirm("delete: " + document.form1.e_id_.value + " ?");
   if(permission == true){
      document.form1.myDelete.value = 1;
      document.form1.submit();
   }
}

function thisDuplicate(){
	var newName = prompt("Enter new name: ");
	if(newName === null){ return; }
	document.form1.e_id_.value = newName; 
	document.form1.myDuplicate.value = 1;
	document.form1.submit();
}

function thisShow(){   document.form1.myShow.value = 1;
   document.form1.submit();
}
function addAll(){
   var permission =  confirm("add all elements ?");
   if(permission == true){
      clearRadio();
      document.form1.all.value = 1;
      document.form1.submit();
   }
}
function thisExport(){

   window.open("mod_exportElement.php?element=" + document.forms[0].e_id_.value+ "&gui=" +document.forms[0].guiList1.value ,"","");
}
function editDesc(){
	var newDesc = prompt("Enter new GUI description", document.form1.guiDesc.value);
	if (newDesc != null) {
		document.form1.guiDesc.value = newDesc;
		document.form1.mySave.value = 2;
		document.form1.submit();
	}
}

// -->
</script>
</head>
<body>

<?php
$admin = new administration();
$ownguis = $admin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
$permguis = $admin->getGuisByPermission(Mapbender::session()->get("mb_user_id"),true);
echo "<form name='form1' action='" . $self ."' method='post'>\n";
if(count($ownguis)>0){
	$v = array();
	$t = array();
	$c = 1;
	$sql = "SELECT * from gui WHERE gui.gui_id IN(";
	for($i=0; $i<count($ownguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".$c;
		array_push($v,$ownguis[$i]);
		array_push($t,'s');
		$c++;
	}
	$sql .= ") order by UPPER(gui_id);";
	$res = db_prep_query($sql,$v,$t);
	$count=0;
	while($row = db_fetch_array($res)){
		$gui_id_own[$count]=$row["gui_id"];
		$gui_name_own[$count]=$row["gui_name"];
		$gui_description_own[$count]=$row["gui_description"];
		$count++;
	}

	$v = array();
	$t = array();
	$c = 1;
	$sql = "SELECT * from gui WHERE gui.gui_id IN(";
	for($i=0; $i<count($permguis); $i++){
		if($i>0){ $sql .= ",";}
		$sql .= "$".$c;
		array_push($v,$permguis[$i]);
		array_push($t,'s');
		$c++;
	}
	$sql .= ") order by UPPER(gui_id);";
	$res = db_prep_query($sql,$v,$t);
	$count=0;
	while($row = db_fetch_array($res)){
		$gui_id_perm[$count]= $row["gui_id"];
		$gui_name_perm[$count]=$row["gui_name"];
		$gui_description_perm[$count]=$row["gui_description"];
		$count++;
	}

	#Gui to edit
	if(!isset($guiList1)){
		echo "<div class= 'guiList1_text'>GUI:</div>\n";
		echo "<select class='guiList1' size='20' name='guiList1' onchange='setGui(this.value)'>\n";
		for ($i=0; $i<count($gui_id_own);$i++){
			echo "<option value='".$gui_id_own[$i]."' ";
			if($guiList1 && $guiList1 == $gui_id_own[$i]){
				echo "selected";
			}
			echo ">".$gui_name_own[$i]."</option>\n";
		}
		echo "</select>\n";

		for ($i=0; $i<count($gui_id_own);$i++){
			echo "<input type='hidden'  name='guiDesc_" . $gui_name_own[$i] . "' value='" . $gui_description_own[$i] . "' >\n";
		    echo "<input type='hidden'  name='guiId_" . $gui_name_own[$i] . "' value='" . $gui_id_own[$i] . "' >\n";
		}
		echo "<input type='hidden'  name='guiList2' value='' >\n";
	}
	else{
	   echo "<div class= 'guiList1_text'>";
	   echo 	"Edit Elements: ".$guiList1;

	   // set guiDesc and guiId if guiList1 has just been selected
	   if (!$guiDesc) {
		  $Desc = "guiDesc_" . $guiList1;
                  $guiDesc = $_POST[$Desc];
	   }
	   if (!$guiId) {
		  $Id = "guiId_" . $guiList1;
                  //$$Id;
                  $guiId = $_POST[$Id];
	   }

	   echo "&nbsp;&nbsp;<br /><span style='font-size:75%'>(" . $guiDesc;
	   echo	" <input type='button' class='' name='' value='edit' onclick='editDesc()'> ) </span>\n";
	   echo "</div>\n";
	   echo "<div class='buttonbar'>\n";
	   echo "<input type='button' class='' name='' value='save' onclick='thisSave()'> \n";
	   echo "<input type='button' class='' name='' value='duplicate' onclick='thisDuplicate()'> \n";
	   echo "<input type='button' class='' name='' value='delete' onclick='thisDelete()'> \n";
	   echo "<input type='button' class='' name='' value='show' onclick='thisShow()'> \n";
	   echo "<input type='button' class='' name='' value='sql' onclick='thisExport()'> \n";
	   echo	"<input type='button' class='' name='' value='arrange' " .
	   		"onclick='if (confirm(\"" . 
			_mb("Please make sure you have made a backup of your application before using this feature! Continue?") . 
			"\")) window.open(\"mod_editApplication.php?" . SID . "&" .
	   		"guiID=" . Mapbender::session()->get("mb_user_gui") . "&" .
	   		"editApplicationId=" . $guiList1 . "\", " .
	   		"\"edit application\", " .
	   		"\"width=500,height=500,dependent\");'> \n";
	   echo "</div>\n";
	   echo "<input type='hidden'  name='guiList1' value='".$guiList1."' >\n";
	   echo "<input type='hidden'  name='guiId' value='".$guiId."' >\n";
	   echo "<input type='hidden'  name='guiDesc' value='".$guiDesc."' >\n";
	}
	#Depot
	if(isset($guiList1)){
		echo "<select name='guiList2' class='guiList2' onchange='clearRadio();submit()'>\n";
		echo "<option>GUI...</option>\n";
		for ($i=0; $i<count($gui_id_perm);$i++){
			echo "<option value='".$gui_id_perm[$i]."' ";
			if($guiList2 && $guiList2 == $gui_id_perm[$i]){
				echo "selected";
			}
			echo ">".$gui_name_perm[$i]."</option>\n";
		}
		echo "</select>\n";
		if($guiList1 != $guiList2){echo "<input class='all' type='button' value='add all elements' onclick='addAll()'>\n";}
	}
	#Elements
	if(isset($guiList2)){
		if($guiList1 == $guiList2){
			echo "<div class='guiList2_header'>Edit Element: </div>\n";
			$isTemplate = false;
		}
		else{
			echo "<div class='guiList2_header'>Templates</div>\n";
			$isTemplate = true;
		}
		$sql = "SELECT * FROM gui_element WHERE fkey_gui_id = $1 ORDER BY e_id";
		$v = array($guiList2);
		$t = array('s');
		$res = db_prep_query($sql,$v,$t);
		$cnt = 0;

		echo "<div class='myElements'>\n<table ";
		if($isTemplate){ echo "class='templateTable'";}
		echo ">\n";

		while($row = db_fetch_array($res)){
			echo "<tr>\n";
			echo "<td class='myTable'><input type='radio' name='myElement' value='".$row["e_id"]."' onclick='setOriginGui();submit()' ";
			if($row["e_id"] == $myElement){echo "checked";}
			echo "></td>\n";
			echo "<td>";
			if(is_file($row["e_src"]) && getimagesize($row["e_src"])){
				echo "<img src='".$row["e_src"]."' width='28px' height='28px'>";
			}
			echo "</td>";
			echo "<td class='myTable'>";
			if($row["e_public"] == 1){echo "<div class='on'>on</div>";}
			//echo "</td>\n<td class='myTable'>". $row["e_id"]. "</td><td class='myTable'>" .$row["e_comment"]."</td>";
			echo "</td>\n";
			echo "<td class='myTable'>";
	 		echo "<a href=\"mod_editElementVars.php?".$urlParameters."&fkey_gui_id=".$guiList2."&fkey_e_id=".$row["e_id"]."\">";
	 		echo $row["e_id"];
			echo "</a>\n</td>\n";
			echo "<td class='myTable'>".$row["e_comment"]."</td>";
			echo "</tr>\n";
			$cnt++;
		}
		echo "</table>\n</div>\n";
	}
	#Formular:
	echo "<table class='myForm'>\n";
		//echo "<tr><td>" . $_POST["originGuiOfSelectedElement"] . "</td></tr>";
if(isset($myElement)){

	$sql = "SELECT * FROM gui_element WHERE fkey_gui_id = '".$guiList2."' AND e_id = '".$myElement."'";
	$v = array();
	$t = array();
	$res = db_prep_query($sql,$v,$t);
	if($row = db_fetch_array($res)){
		echo "<tr><td>ID: </td><td><input type='text' class='textfield' name='e_id_' value='".$row["e_id"]."'><input type='hidden' class='textfield' name='orig_e_id_' value='".$row["e_id"]."'></td></tr>\n";
		echo "<tr><td>Position: </td><td><input type='text' class='textfield' name='e_pos' value='".$row["e_pos"]."'></td></tr>\n";
		echo "<tr><td>ON/OFF: </td><td><input type='text' class='textfield' name='e_public' value='".$row["e_public"]."'></td></tr>\n";
		echo "<tr><td>Comment: </td><td><textarea cols='32' rows='5'  name='e_comment' >".stripslashes($row["e_comment"])."</textarea></td></tr>\n";
		echo "<tr><td>title: </td><td><input type='text' class='textfield' name='e_title' value='".$row["e_title"]."'></td></tr>\n";
		echo "<tr><td>HTML-TAG: </td><td><input type='text' class='textfield' name='e_element' value='".$row["e_element"]."'></td></tr>\n";
		echo "<tr><td>SRC: </td><td><input type='text' class='textfield' name='e_src' value='".$row["e_src"]."'></td></tr>\n";
		echo "<tr><td>Attributes: </td><td><textarea cols='32' rows='5'  name='e_attributes' >".stripslashes($row["e_attributes"])."</textarea></td></tr>\n";
		echo "<tr><td>Left: </td><td><input type='text' class='textfield' name='e_left' value='".$row["e_left"]."'></td></tr>\n";
		echo "<tr><td>Top: </td><td><input type='text' class='textfield' name='e_top' value='".$row["e_top"]."'></td></tr>\n";
		echo "<tr><td>Width: </td><td><input type='text' class='textfield' name='e_width' value='".$row["e_width"]."'></td></tr>\n";
		echo "<tr><td>Height: </td><td><input type='text' class='textfield' name='e_height' value='".$row["e_height"]."'></td></tr>\n";
		echo "<tr><td>Z-INDEX: </td><td><input type='text' class='textfield' name='e_z_index' value='".$row["e_z_index"]."'></td></tr>\n";
		echo "<tr><td>Styles: </td><td><input type='text' class='textfield' name='e_more_styles' value='".$row["e_more_styles"]."'></td></tr>\n";
		echo "<tr><td>Content: </td><td><textarea cols='32' rows='4'  name='e_content' >".htmlentities(stripslashes($row["e_content"]), ENT_QUOTES, CHARSET)."</textarea></td></tr>\n";
		echo "<tr><td>Close-TAG: </td><td><input type='text' class='textfield' name='e_closetag' value='".$row["e_closetag"]."'></td></tr>\n";
		echo "<tr><td>JavaScript: </td><td><input type='text' class='textfield' name='e_js_file' value='".$row["e_js_file"]."'></td></tr>\n";
		echo "<tr><td>Modul: </td><td><input type='text' class='textfield' name='e_mb_mod' value='".$row["e_mb_mod"]."'></td></tr>\n";
		echo "<tr><td>Target: </td><td><input type='text' class='textfield' name='e_target' value='".$row["e_target"]."'></td></tr>\n";
		echo "<tr><td>Requires: </td><td><input type='text' class='textfield' name='e_requires' value='".$row["e_requires"]."'></td></tr>\n";
		echo "<tr><td>URL: </td><td><input type='text' class='textfield' name='e_url' value='".$row["e_url"]."'></td></tr>\n";
	}
	echo "</table>";
}
else if(isset($guiList1)){
	echo "<tr><td>ID: </td><td><input type='text' class='textfield' name='e_id_' value='".$e_id_."'><input type='hidden' class='textfield' name='orig_e_id_' value='".$orig_e_id_."'></td></tr>\n";
	echo "<tr><td>Position: </td><td><input type='text' class='textfield' name='e_pos' value='".$e_pos."'></td></tr>\n";
	echo "<tr><td>ON/OFF: </td><td><input type='text' class='textfield' name='e_public' value='".$e_public."'></td></tr>\n";
	echo "<tr><td>Comment: </td><td><textarea cols='32' rows='5'  name='e_comment' >".stripslashes($e_comment)."</textarea></td></tr>\n";
	echo "<tr><td>title: </td><td><input type='text' class='textfield' name='e_title' value='".$e_title."'></td></tr>\n";
	echo "<tr><td>HTML-TAG: </td><td><input type='text' class='textfield' name='e_element' value='".$e_element."'></td></tr>\n";
	echo "<tr><td>SRC: </td><td><input type='text' class='textfield' name='e_src' value='".$e_src."'></td></tr>\n";
	echo "<tr><td>Attributes: </td><td><textarea cols='32' rows='5'  name='e_attributes' >".stripslashes($e_attributes)."</textarea></td></tr>\n";
	echo "<tr><td>Left: </td><td><input type='text' class='textfield' name='e_left' value='".$e_left."'></td></tr>\n";
	echo "<tr><td>Top: </td><td><input type='text' class='textfield' name='e_top' value='".$e_top."'></td></tr>\n";
	echo "<tr><td>Width: </td><td><input type='text' class='textfield' name='e_width' value='".$e_width."'></td></tr>\n";
	echo "<tr><td>Height: </td><td><input type='text' class='textfield' name='e_height' value='".$e_height."'></td></tr>\n";
	echo "<tr><td>Z-INDEX: </td><td><input type='text' class='textfield' name='e_z_index' value='".$e_z_index."'></td></tr>\n";
	echo "<tr><td>Styles: </td><td><input type='text' class='textfield' name='e_more_styles' value='".$e_more_styles."'></td></tr>\n";
	echo "<tr><td>Content: </td><td><textarea cols='32' rows='4'  name='e_content' >".htmlentities(stripslashes($e_content), ENT_QUOTES, CHARSET)."</textarea></td></tr>\n";
	echo "<tr><td>Close-TAG: </td><td><input type='text' class='textfield' name='e_closetag' value='".$e_closetag."'></td></tr>\n";
	echo "<tr><td>JavaScript: </td><td><input type='text' class='textfield' name='e_js_file' value='".$e_js_file."'></td></tr>\n";
	echo "<tr><td>Module: </td><td><input type='text' class='textfield' name='e_mb_mod' value='".$e_mb_mod."'></td></tr>\n";
	echo "<tr><td>Target: </td><td><input type='text' class='textfield' name='e_target' value='".$e_target."'></td></tr>\n";
	echo "<tr><td>Requires: </td><td><input type='text' class='textfield' name='e_requires' value='".$e_requires."'></td></tr>\n";
	echo "<tr><td>URL: </td><td><input type='text' class='textfield' name='e_url' value='".$e_url."'></td></tr>\n";
}
echo "</table>\n";
echo "<input type='hidden' name='originGuiOfSelectedElement' value='" . $_POST["originGuiOfSelectedElement"] . "'>";

echo "<input type='hidden' name='myDelete'>";
echo "<input type='hidden' name='myDuplicate'>";
echo "<input type='hidden' name='mySave'>";
echo "<input type='hidden' name='myShow'>";
echo "<input type='hidden' name='all'>";
echo "</form>";
}
else{
	echo "There are no guis available for this user. Please create a gui first.";
}
?>
</body>
</html>
