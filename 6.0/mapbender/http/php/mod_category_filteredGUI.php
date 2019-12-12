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


$e_id="category_filteredGUI";
require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");
/*  
 * @security_patch irv done 
 */ 
//security_patch_log(__FILE__,__LINE__); 

$filter1 = $_POST["filter1"];
$selected_category = $_POST["selected_category"];
$selected_gui = $_POST["selected_gui"];
$remove_gui = $_POST["remove_gui"];
$insert = $_POST["insert"];
$remove = $_POST["remove"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
	echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>Administration</title>
<?php
include '../include/dyn_css.php';

?>
<script language="JavaScript">
function validate(wert){
	if(document.forms[0]["selected_category"].selectedIndex == -1){
		document.getElementsByName("selected_category")[0].style.backgroundColor = '#ff0000';
		return;
	}else{
		if(wert == "remove"){
			if(document.forms[0]["remove_gui[]"].selectedIndex == -1){
				document.getElementsByName("remove_gui[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.remove.value = 'true';
			document.form1.submit();
		}
		if(wert == "insert"){
			if(document.forms[0]["selected_gui[]"].selectedIndex == -1){
				document.getElementsByName("selected_gui[]")[0].style.backgroundColor = '#ff0000';
				return;
			}
			document.form1.insert.value = 'true';
			document.form1.submit();
		}
	}
}
/**
 * filter the category list
 */
function filtercategory(list, all, str){
	str=str.toLowerCase();
	var selection=[];
	var i,j,selected;
	for(i=0;i<list.options.length;i++){
		if(list.options[i].selected)
			selection[selection.length]=list.options[i].value;
	}
	
	list.options.length = 0;
	for(i=0; i<all.length; i++){
		if(all[i]['name'].toLowerCase().indexOf(str)==-1)
			continue;
		selected=false;
		for(j=0;j<selection.length;j++){
			if(selection[j]==all[i]['id']){
				selected=true;
				break;
			}
		}
		var newOption = new Option(selected?all[i]['name']:all[i]['name'],all[i]['id'],false,selected);
		newOption.setAttribute("title", all[i]['description']);
		list.options[list.options.length] = newOption;
	}	
}
</script>

</head>
<body>
<?php

$fieldHeight = 20;

$cnt_gui = 0;
$cnt_category = 0;
$cnt_gui = 0;
$cnt_gui_category = 0;
$cnt_gui_gui = 0;
$exists = false;
$logged_user_name=Mapbender::session()->get("mb_user_name");
$logged_user_id=Mapbender::session()->get("mb_user_id");

$admin = new administration();


/*handle remove, update and insert*****************************************************************/
if($insert){
	if(count($selected_gui)>0){
		for($i=0; $i<count($selected_gui); $i++){
			$exists = false;
			$sql_insert = "SELECT * from gui_gui_category where fkey_gui_category_id = $1 and fkey_gui_id = $2";
			$v = array($selected_category,$selected_gui[$i]);
			$t = array('i','s');
			$res_insert = db_prep_query($sql_insert,$v,$t);
			while(db_fetch_row($res_insert)){$exists = true;}
			if($exists == false){
				$sql_insert = "INSERT INTO gui_gui_category(fkey_gui_category_id, fkey_gui_id) VALUES($1, $2)";
				$v = array($selected_category,$selected_gui[$i]);
				$t = array('i','s');
				$res_insert = db_prep_query($sql_insert,$v,$t);
			}
		}
	}
}
if($remove){
	if(count($remove_gui)>0){
		for($i=0; $i<count($remove_gui); $i++){
			$sql_remove = "DELETE FROM gui_gui_category WHERE fkey_gui_id = $1 and fkey_gui_category_id = $2";
			$v = array($remove_gui[$i],$selected_category);
			$t = array('s','i');
			db_prep_query($sql_remove,$v,$t);
		}
	}
}

/*get owner guis  *******************************************************************************/
$guisByOwner = $admin->getGuisByOwner($logged_user_id,false);
for ($i = 0; $i < count($guisByOwner); $i++) {
	$gui_id[$cnt_gui] = $guisByOwner[$i];
	$gui_name[$cnt_gui] = $guisByOwner[$i];
	$cnt_gui++;
}

/*get categories **********************************************************************************/
$sql_category = "SELECT * FROM gui_category order by category_id;";
$res_category = db_query($sql_category);
while($row = db_fetch_array($res_category)){
	$category_id[$cnt_category] = $row["category_id"];
	$category_name[$cnt_category] = $row["category_name"];
	$category_description[$cnt_category] = $row["category_description"];
	$cnt_category++;
}


$guiCategories = $admin->getGuiCategories();


/*
*	
* get owner gui for selected category
*
*/	
if (count($category_id) == 0 AND count($gui_id) == 0){ die("There is no gui or category available for this user");}


if(!$selected_category){$selectedCategory = $category_id[0];}
if($selected_category){$selectedCategory = $selected_category;}
$getGuisByOwnerBySelectedGuiCategory = $admin->getGuisByOwnerByGuiCategory($logged_user_id,$selectedCategory);

$cnt_gui_category = count($getGuisByOwnerBySelectedGuiCategory);
$gui_category_id = $getGuisByOwnerBySelectedGuiCategory;


/*INSERT HTML*/
echo "<form name='form1' action='" . $self ."' method='post'>";
/*filterbox****************************************************************************************/
echo "<input type='text' value='' class='filter1' id='filter1' name='filter1' onkeyup='filtercategory(document.getElementById(\"selectedcategory\"),category,this.value);'/>";

/*insert all category in selectbox*****************************************************************/
echo "<div class='text1'>Category: </div>";
echo "<select style='background:#ffffff' onchange='submit();' class='select1' id='selectedcategory' name='selected_category' size='10'>";
for($i=0; $i<$cnt_category; $i++){
	echo "<option value='" . $category_id[$i] . "' ";
	if($selected_category && $selected_category == $category_id[$i]){
		echo "selected>".$category_name[$i];
	}
	else
		echo ">" . $category_name[$i];
	echo "</option>";
}
echo "</select>";

/*insert all guis in selectbox********************************************************************/
echo "<div class='text2'>GUI:</div>";
echo "<select style='background:#ffffff' class='select2' multiple='multiple' name='selected_gui[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_gui; $i++){
	echo "<option value='" . $gui_name[$i]  . "'>" . $gui_name[$i]  . "</option>";
}
echo "</select>";

/*Button******************************************************************************************/

echo "<div class='button1'><input type='button'  value='==>' onClick='validate(\"insert\")'></div>";
echo "<input type='hidden' name='insert'>";

echo "<div class='button2'><input type='button' value='<==' onClick='validate(\"remove\")'></div>";
echo "<input type='hidden' name='remove'>";

/*insert category_gui_dependence in selectbox**************************************************/
echo "<div class='text3'>SELECTED GUI:</div>";
echo "<select style='background:#ffffff' class='select3' multiple='multiple' name='remove_gui[]' size='$fieldHeight' >";
for($i=0; $i<$cnt_gui_category; $i++){
	echo "<option value='" . $gui_category_id[$i]  . "'>" .$gui_category_id[$i]. "</option>";
}
echo "</select>";
echo "</form>";

?>
<script type="text/javascript">
<!--
document.forms[0].selected_category.focus();
var category=[];
<?php
for($i=0; $i<$cnt_category; $i++){
	echo "category[".$i."]=[];\n";
	echo "category[".$i."]['id']='" . $category_id[$i]  . "';\n";
	echo "category[".$i."]['name']='" . $category_name[$i]  . "';\n";
	echo "category[".$i."]['description']='" . $category_description[$i]  . "';\n";
}
?>
// -->
</script>
</body>
</html>