<?php
# $Id: mod_editElementVars.php 8349 2012-05-11 07:43:18Z verenadiewald $
# http://www.mapbender.org/index.php/mod_editElementVars.php
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

/*  
 * @security_patch irv done
 */ 
security_patch_log(__FILE__,__LINE__);
$postvars=array("myElement","var_name","var_value","context","var_type","fkey_gui_id","fkey_e_id","myDelete","mySave");
foreach($postvars as $value){
        $$value=$_POST[$value];
}
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    if (isset($_GET["fkey_gui_id"])){
        $guiList1=$_GET["fkey_gui_id"];
    }
    if (isset($_GET["fkey_e_id"])){
        $guiList2=$_GET["fkey_e_id"];
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
<title>Edit Element Vars</title>
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
   	top:40px;
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
   	top:70px;
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
   td.secondary {
     color: #ededed;
     font-size: 10px;
     border: none;
   }
   tr.show-secondary td.secondary {
     color: #333;
   }
   -->
</style>

<?php

if((isset($myDelete) && $myDelete == '1') || (isset($mySave) && $mySave == '1')){
    $checkAdmin = new administration();
    $myOwnedGuis = $checkAdmin->getGuisByOwner(Mapbender::session()->get("mb_user_id"),true);
    $modOwnerAlert = false;
    $ownerAlert = "You do not have the permission to change the elements of this application.";
    
    if(!in_array($fkey_gui_id, $myOwnedGuis)) {
    if($modOwnerAlert == false){
    		$modOwnerAlert = true;
    	}
    }
    if($modOwnerAlert == true){
    	unset($myDelete);
    	unset($mySave);
    	echo "<script language='JavaScript'>";
    	echo "alert('".$ownerAlert."');";
    	echo "</script>";
    
    }
}

# handle database updates etc.....

if(isset($mySave) && ($mySave == '1')){
	$sql  = "DELETE FROM gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2 AND var_name = $3";  
	$v = array($fkey_gui_id,$fkey_e_id,$var_name);
	$t = array('s','s','s');
	$res = db_prep_query($sql,$v,$t);
	$sql  = "INSERT INTO gui_element_vars(fkey_gui_id,fkey_e_id,var_name,var_value,context,var_type) ";
	$sql .= "VALUES ($1, $2, $3, $4, $5, $6)";
	//db_escape_string($var_value)?,db_escape_string($context)?
	$v = array($fkey_gui_id,$fkey_e_id,$var_name,$var_value,$context,$var_type);
	$t = array('s','s','s','s','s','s');
	$res = db_prep_query($sql,$v,$t);
	$mySave = 0;
}

if(isset($myDelete) && ($myDelete == '1')){
	$sql  = "DELETE FROM gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2 AND var_name = $3";  
	$v = array($fkey_gui_id,$fkey_e_id,$var_name);
	$t = array('s','s','s');
	$res = db_prep_query($sql,$v,$t);
	$myDelete = 0;
}
?>

<script type="text/javascript">
<!--
function thisSave(){
   if(document.form1.var_name.value != ""){
      var permission =  confirm("save: " + document.form1.var_name.value + " ?");
      if(permission == true){
         document.form1.mySave.value = 1;     
         document.form1.submit();
      }
   }
}

function thisDelete(){
   if(document.form1.var_name.value != ""){
      var permission =  confirm("delete: " + document.form1.var_name.value + " ?");
      if(permission == true){
         document.form1.myDelete.value = 1;
         document.form1.submit();
      }
   }
}
// -->
</script>
</head>
<body>

<?php
   echo "<form name='form1' action='" . $self ."' method='POST'>\n";

   $fkey_gui_id = $_REQUEST["fkey_gui_id"];
   $fkey_e_id   = $_REQUEST["fkey_e_id"];
   if(isset($_REQUEST["myElement"])){
      $myElement = $_REQUEST["myElement"];
   }

   echo "<div class= 'guiList1_text'>";
   echo "Edit Element Vars: ".$fkey_gui_id." / ".$fkey_e_id;

   echo "</div>\n";
   echo "<div class='buttonbar'>\n";
   echo "<input type='button' class='' name='' value='save'   onclick='thisSave()'> \n";
   echo "<input type='button' class='' name='' value='delete' onclick='thisDelete()'> \n";
   
   $href = "self.location.href='mod_editElements.php?".$urlParameters."&guiList1=".$fkey_gui_id."&guiList2=".$fkey_gui_id."'";

   echo "<input type='button' class='' name='' value='return' onclick=\"".$href."\"> \n";
   echo "</div>\n";

	$sql = "SELECT * FROM gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2 ORDER BY var_name";
	$v = array($fkey_gui_id,$fkey_e_id);
	$t = array('s','s');
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	
   echo "<div class='myElements'>\n<table>\n";
	
   while(db_fetch_row($res)){
      echo "<tr onmouseover='this.className=\"show-secondary\"' onmouseout='this.className=\"\"'>\n";
      echo "<td class='myTable'>";
      echo "<input type='radio' name='myElement' value='".db_result($res, $cnt, "var_name")."' onclick='submit()' ";
      if(isset($myElement) && $myElement == db_result($res, $cnt, "var_name")){
	echo "checked='checked' ";
      }
      echo ">";
      echo "</td>\n";
      echo "<td class='myTable'>".db_result($res, $cnt, "var_name")."</td>\n";
      echo "<td class='secondary'>".db_result($res, $cnt, "var_type")."</td>\n";
      echo "</tr>\n";
      $cnt++;
   }
   echo "</table>\n</div>\n";
   
   echo "<table class='myForm'>\n";
   $formOk = 0;
   if(isset($myElement)){
      $sql = "SELECT * FROM gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2 AND var_name = $3";
      $v = array($fkey_gui_id,$fkey_e_id,$myElement);
      $t = array('s','s','s');
      $res = db_prep_query($sql,$v,$t);
      if(db_fetch_row($res)){
         echo "<tr><td>Name:</td><td><input type='text' class='textfield' name='var_name' value='".db_result($res,0,"var_name")."'></td></tr>\n";
         echo "<tr><td>Value:</td><td><textarea cols='32' rows='5'  name='var_value' >".stripslashes(db_result($res,0,"var_value"))."</textarea></td></tr>\n";
         echo "<tr><td>Context:</td><td><input type='text' class='textfield' name='context' value='".db_result($res,0,"context")."'></td></tr>\n";
         echo "<tr><td>Type:</td><td><input type='text' class='textfield' name='var_type' value='".db_result($res,0,"var_type")."'></td></tr>\n";
         $formOk = 1;
      }
   }
	if($formOk == 0){
		echo "<tr><td>Name:</td><td><input type='text' class='textfield' name='var_name' value=''></td></tr>\n";
		echo "<tr><td>Value:</td><td><textarea cols='32' rows='5'  name='var_value' ></textarea></td></tr>\n";
		echo "<tr><td>Context:</td><td><input type='text' class='textfield' name='context' value=''></td></tr>\n";
		echo "<tr><td>Type:</td><td>";
			echo "<select class='textfield' name='var_type'>";
				echo "<option value='text/css'>text/css</option>";
				echo "<option value='file/css'>file/css</option>";
				echo "<option value='var'>JavaScript Variable</option>";
				echo "<option value='php_var'>PHP Variable</option>";
			echo "</select>";
		echo "</td></tr>\n";
   }
   echo "</table>\n";

   echo "<input type=\"hidden\" name=\"fkey_gui_id\" value=\"".$fkey_gui_id."\">\n";
   echo "<input type=\"hidden\" name=\"fkey_e_id\"   value=\"".$fkey_e_id."\">\n";
?>
<input type="hidden" name="myDelete">
<input type="hidden" name="mySave">
</form>
</body>
</html>
