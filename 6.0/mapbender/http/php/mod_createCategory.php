<?php
# $Id:$
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

$e_id="createCategory";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done 
 */ 
//security_patch_log(__FILE__,__LINE__); 
$newCategory = $_POST["newCategory"];
$newDesc = $_POST["newDesc"];

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
<title>New Category</title>
<?php include '../include/dyn_css.php'; ?>
<?php
if(isset($newCategory) && $newCategory != ""){
  $sql = "SELECT category_name FROM gui_category WHERE category_name = $1";
  $v = array($newCategory);
  $t = array('s');
  $res = db_prep_query($sql,$v,$t);
  if(db_fetch_row($res)){
     echo "<script type='text/javascript'>";
     echo "alert('Error: Category already exists!');";
     echo "</script>";
  }
  else{
	$sql = "INSERT INTO gui_category (category_name,category_description) ";
	$sql .= "VALUES($1, $2)";
	$v = array($newCategory,$newDesc);
	$t = array('s','s');
	
	$res = db_prep_query($sql,$v,$t);
	$categoryCreated=true;
  }
}
?>
<script type="text/javascript">
<!--
function setFocus(){
	document.form1.newCategory.focus();
}
function validate(){
	if(document.form1.newCategory.value == ""){
		alert("<?php echo _mb("Please enter a category name!")?>");
		document.form1.newCategory.focus();
		return;
	}
	else if(document.form1.newDesc.value == ""){
		alert("<?php echo _mb("Please enter a category description!")?>");
		document.form1.newDesc.focus();
		return;
	}
	else{
		document.form1.submit();
	}
}
// -->
</script>
</head>
<body onload='setFocus()'>
<form name='form1' action="<?php echo $self; ?>" method="POST">

<?php
	$v = array();
	$t = array();
	$c = 1;
	$sql = "SELECT * from gui_category";
	$sql .= " order by lower(category_name);";
	$res = db_prep_query($sql,$v,$t);
	$count=0;
	while($row = db_fetch_array($res)){
		$category_name[$count]= $row["category_name"];
		$category_description[$count]=$row["category_description"];
		$count++;
	}
	echo "<p\n";
	echo "<div class= 'guiList1_text'>"._mb("existing Categories").":</div>\n";
#echo "<select class='categoryList' size='14' name='categoryList' onchange='setCategory(this.value)'>\n";
	echo "<select class='categoryList' size='14' name='categoryList' onchange=''>\n";
	for ($i=0; $i<count($category_name);$i++){
		echo "<option value='".$category_name[$i]."' ";
		echo ">".$category_name[$i]. " - ".$category_description[$i]."</option>\n";
	}
	echo "</select>\n";
	echo "</p\n";
?>

<table>
<tr><td><?php echo _mb("Category Name"); ?>: </td><td><input type='text' name='newCategory'></td></tr>
<tr><td><?php echo _mb("Description"); ?>: </td><td><input type='text' name='newDesc'></td></tr>
<tr><td></td><td><input type='button' onclick='validate()' value="<?php echo _mb("new"); ?>"></td></tr>
</table>

<?php
if(isset($newCategory) && $newCategory != ""){
	if ($categoryCreated==true){
		echo "<p class = 'categroyList'>";
		echo "<b>".$newCategory."</b> - "._mb("The Category has been created successfully.");
		echo "<p>";
		}else{
			echo"error";
		}
}
?>
</form>
</body>
</html>