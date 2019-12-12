<?php
# $Id:
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

$e_id="deleteCategory";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done
 */
//security_patch_log(__FILE__,__LINE__);
$categoryList=$_POST["categoryList"];
$del=$_POST["del"];

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
<title>Delete Category</title>
<?php
include '../include/dyn_css.php';
?>
<script type="text/javascript">
function validate(){
	var ind = document.form1.categoryList.selectedIndex;
	if(ind > -1){
		var permission =  confirm("delete: " + document.form1.categoryList.options[ind].text + " ?");
		if(permission == true){
			document.form1.del.value = 1;
			document.form1.submit();
		}
	}
}
</script>
</head>
<body>

<?php
###delete
if($del){
	$sql = "DELETE FROM gui_category WHERE category_name = $1";
	$v = array($categoryList);
	$t = array('s');
	$res = db_prep_query($sql,$v,$t);
}
###
	$v = array();
	$t = array();
	$sql = "SELECT * from gui_category";
	$sql .= " order by category_name";
	$res = db_prep_query($sql,$v,$t);
	$cnt = 0;
	echo "<form name='form1' action='" . $self ."' method='post'>";
	echo "<select size='20' style='width:400px' name='categoryList' class='categoryList' onchange='document.form1.categoryList.value = this.value;submit()'>";
	while($row = db_fetch_array($res)){
		$categoryvalue = $row["category_name"];
		//mark previously selected GUI <==> text = " selected" 
		if ($categoryvalue == $categoryList) {
			$text = " selected";
		}
		else {
			$text = "";
		}
	   echo "<option value='".$categoryvalue."'" . $text . ">".$row["category_name"]."(".$row["category_description"].")</option>";
	   $cnt++;
	}
	echo "</select><br>";
	if($cnt>0){
		echo "<input class='button_del' type='button' value='delete' onclick='validate()'>";
	
	}else{
		echo "There are no categories available.";
	}
	echo "<input type='hidden' name='del'>";
	echo "</form>";
?>
</body>
</html>
