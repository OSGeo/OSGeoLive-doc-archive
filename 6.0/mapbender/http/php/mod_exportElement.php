<?php
# $Id: mod_exportElement.php 3941 2009-05-13 19:14:54Z marc $
# http://www.mapbender.org/index.php/mod_exportElement.php
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<?php
echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';	
?>
<title>exportElement</title>
<?php
   include '../include/dyn_css.php';
?>
</head>
<body>
<?php
$insert = "";
$sql = "SELECT * FROM gui_element WHERE fkey_gui_id = $1 AND e_id= $2";
$v = array($_REQUEST["gui"],$_REQUEST["element"]);
$t = array('s','s');
$res = db_prep_query($sql,$v,$t);
if($row = db_fetch_array($res)) {
   $insert .=  "INSERT INTO gui_element(";
      $insert .=  "fkey_gui_id, ";
      $insert .=  "e_id, ";
      $insert .=  "e_pos, ";
      $insert .=  "e_public, ";
      $insert .=  "e_comment, ";
      $insert .=  "e_title, ";
      $insert .=  "e_element, ";
      $insert .=  "e_src, ";
      $insert .=  "e_attributes, ";
      $insert .=  "e_left, ";
      $insert .=  "e_top, ";
      $insert .=  "e_width, ";
      $insert .=  "e_height, ";
      $insert .=  "e_z_index, ";
      $insert .=  "e_more_styles, ";
      $insert .=  "e_content, ";
      $insert .=  "e_closetag, ";
      $insert .=  "e_js_file, ";
      $insert .=  "e_mb_mod, ";
      $insert .=  "e_target, ";
      $insert .=  "e_requires, ";
      $insert .=  "e_url";
   $insert .=  ") VALUES(";
      $insert .=  "'".$row["fkey_gui_id"]."',";
      $insert .=  "'".$row["e_id"]."',";
      $insert .=  "".$row["e_pos"]. ",";
      $insert .=  "".$row["e_public"]. ",";
      $insert .=  "'".db_escape_string($row["e_comment"])."',";
      $insert .=  "'".db_escape_string($row["e_title"])."',";      
      $insert .=  "'".$row["e_element"]."',";
      $insert .=  "'".$row["e_src"]."',";
      $insert .=  "'".db_escape_string($row["e_attributes"])."',";
      $insert .=  "".$row["e_left"]. ",";
      $insert .=  "".$row["e_top"]. ",";
      $insert .=  "".$row["e_width"]. ",";
      $insert .=  "".$row["e_height"]. ",";
      $insert .=  "".$row["e_z_index"]. ",";
      $insert .=  "'".$row["e_more_styles"]."',";
      $insert .=  "'".db_escape_string($row["e_content"])."',";
      $insert .=  "'".$row["e_closetag"]."',";
      $insert .=  "'".$row["e_js_file"]."',";
      $insert .=  "'".$row["e_mb_mod"]."',";
      $insert .=  "'".$row["e_target"]."',";
      $insert .=  "'".$row["e_requires"]."',";
      $insert .=  "'".$row["e_url"]."'";
   $insert .=  ");\n";
   $insert = preg_replace("/,,/", ",NULL ,", $insert);
   $insert = preg_replace("/,,/", ",NULL ,", $insert);
}

	# export element vars
	$sql = "SELECT * FROM gui_element_vars WHERE fkey_gui_id = $1 AND fkey_e_id = $2";
	$v = array($_REQUEST["gui"],$_REQUEST["element"]);
	$t = array('s','s');
	$res = db_prep_query($sql,$v,$t);
	$cnt_res = 0;
	while ($row = db_fetch_array($res)){
   	$insert .=  "INSERT INTO gui_element_vars(";
      $insert .=  "fkey_gui_id, ";
      $insert .=  "fkey_e_id, ";
      $insert .=  "var_name, ";
      $insert .=  "var_value, ";
      $insert .=  "context, ";
      $insert .=  "var_type";
   	$insert .=  ") VALUES(";
      $insert .=  "'".$row["fkey_gui_id"]."', ";
      $insert .=  "'".$row["fkey_e_id"]."', ";
      $insert .=  "'".$row["var_name"]. "', ";
      $insert .=  "'".db_escape_string($row["var_value"]). "', ";
      $insert .=  "'".db_escape_string($row["context"])."' ,";
      $insert .=  "'".db_escape_string($row["var_type"])."'";
   	$insert .=  ");\n";
   	$insert = preg_replace("/,,/", ",NULL ,", $insert);
   	$cnt_res++;
	}
   #------------------

   echo "<textarea rows=30 cols=100>";
    echo preg_replace("/, ,/", ",NULL ,", $insert);
   echo "</textarea>";

?>
</body>
</html>
