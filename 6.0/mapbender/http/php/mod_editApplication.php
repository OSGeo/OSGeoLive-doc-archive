<?php
# $Id: mod_editElements.php 2413 2008-04-23 16:21:04Z christoph $
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

require_once(dirname(__FILE__)."/../php/mb_validateSession.php");
require_once(dirname(__FILE__)."/../classes/class_administration.php");
require_once(dirname(__FILE__)."/../classes/class_user.php");

$editApplicationId = $_REQUEST["editApplicationId"];

$user = new User(Mapbender::session()->get("mb_user_id"));
$myApplicationArray = $user->getApplicationsByPermission(false);
if (!in_array($editApplicationId, $myApplicationArray)) {
	die("You are not allowed to edit the application '" . $editApplicationId . "'");	
}
?>
<html>
<head>
<style type="text/css">
.ui-selecting {
  border-width:thin;
  border-style:solid;
  border-color:red;
  background-color:transparent;
  font-size:9px;
}
.ui-selected {
  border-width:thin;
  border-style:solid;
  border-color:red;
  background-color:transparent;
  font-size:9px;
}
.ui-draggable {
}
.div-border {
  border-width:thin;
  border-style:solid;
  border-color:black;
  background-color:transparent;
  font-size:9px;
}
</style>
<link rel='stylesheet' type='text/css' href='../css/popup.css'>
<script type='text/javascript' src='../extensions/jquery-1.2.6.min.js'></script>
<script type='text/javascript' src='../extensions/jquery-ui-personalized-1.5.min.js'></script>
<script type='text/javascript' src='../extensions/jqjson.js'></script>
<script type='text/javascript' src='../javascripts/popup.js'></script>
<script language="JavaScript" type="text/javascript">
<?php
	include("../javascripts/core.php");
	include("../../lib/buttonNew.js");
	include("../../lib/draggableButton.js");
	include("../../lib/selectableButton.js");
	include("../../lib/resizableButton.js");
	include("../../lib/alignButton.js");
	include("../../lib/saveButton.js");

	echo "var editApplicationId = '" . $editApplicationId . "';";
?>

$(function() {
	//
	// create the toolbox
	//
	var controlPopup;
	controlPopup = new mb_popup({
		left:300,
		top:300,
		width:180,
		height:80,
		html:"<div id='controls'></div>",
		title:"Toolbox"
	});
	var toolbox = new ButtonGroup("controls");
	
	//
	// add tools to the toolbox
	//
	
	// select tool
	var selectButton = new Button(Selectable.buttonParameters);
	toolbox.add(selectButton);
	
	// drag tool
	var dragButton = new Button(Draggable.buttonParameters);
	toolbox.add(dragButton);
	
	// resize tool
	var resizeButton = new Button(Resizable.buttonParameters);
	toolbox.add(resizeButton);
	
	// save tool
	var saveButton = new Button(Save.buttonParameters);
	toolbox.add(saveButton);
	
	//align top
	alignTopButton = new Button(Align.top.buttonParameters);
	toolbox.add(alignTopButton);
	
	//align left
	alignLeftButton = new Button(Align.left.buttonParameters);
	toolbox.add(alignLeftButton);
	
	//align bottom
	alignBottomButton = new Button(Align.bottom.buttonParameters);
	toolbox.add(alignBottomButton);
	
	//align right
	alignRightButton = new Button(Align.right.buttonParameters);
	toolbox.add(alignRightButton);
	
	//
	// add functionality to the buttons
	//

	selectButton.registerPush(Selectable.makeSelectable);
	selectButton.registerRelease(Selectable.removeSelection);

	dragButton.registerPush(Draggable.makeDraggable);
	dragButton.registerStop(Draggable.removeDraggable);

	resizeButton.registerPush(Selectable.removeSelection);
	resizeButton.registerPush(Resizable.makeResizable);
	resizeButton.registerStop(Resizable.removeResizable);

	saveButton.registerPush(function () {
		Save.updateDatabase(saveButton.triggerStop);	
	});
	
	alignTopButton.registerPush(Align.top.align);
	alignLeftButton.registerPush(Align.left.align);
	alignBottomButton.registerPush(Align.bottom.align);
	alignRightButton.registerPush(Align.right.align);
	
	//
	// display the toolbox
	//
	controlPopup.show();
	
});
	
</script>
</head>
<?php
$pattern = "/sessionID/";

$sql = "SELECT fkey_gui_id,e_id,e_pos,e_public,e_comment,gettext($1, e_title) as e_title, e_element,";
$sql .= "e_src,e_attributes,e_left,e_top,e_width,e_height,e_z_index,e_more_styles,";
$sql .= "e_content,e_closetag,e_js_file,e_mb_mod,e_target,e_requires,e_url FROM gui_element WHERE ";
$sql .= "e_public = 1 AND fkey_gui_id = $2 ORDER BY e_pos";
$v = array(Mapbender::session()->get("mb_lang"), $editApplicationId);
$t = array('s', 's');
$res = db_prep_query($sql,$v,$t);
$i = 0;
while(db_fetch_row($res)){
	$replacement = $urlParameters."&elementID=".db_result($res,$i,"e_id");
	if (db_result($res,$i,"e_element") == "body" ) {
		echo "<".db_result($res,$i,"e_element")." ";
		echo "' ><div class='collection'>";
	}
	else {
		if (db_result($res,$i,"e_left") && db_result($res,$i,"e_top")) {
			//
			// open tag
			//
			if (db_result($res,$i,"e_closetag") != "iframe" && db_result($res,$i,"e_closetag") != "form" ) {
				echo "<".db_result($res,$i,"e_element")." ";
			}
			else {
				echo "<div ";
			}
			echo " class='div-border' ";
			
			//
			// style
			//
			echo " style = '";
			if(db_result($res,$i,"e_left") != "" && db_result($res,$i,"e_top") != ""){
				echo "position:absolute;";
				echo "left:".db_result($res,$i,"e_left")."px;";
				echo "top:".db_result($res,$i,"e_top")."px;";
			}
			if(db_result($res,$i,"e_width") != "" && db_result($res,$i,"e_height") != ""){
				echo "width:".db_result($res,$i,"e_width")."px;";
				echo "height:".db_result($res,$i,"e_height")."px;";
			}
			else {
				echo "width:15px;";
				echo "height:15px;";
			}
			
			echo "' ";

			//
			// attributes
			//
			if(db_result($res,$i,"e_id") != ""){
				echo " id='".db_result($res,$i,"e_id")."' ";
				echo " name='".db_result($res,$i,"e_id")."' ";
			}
			if (db_result($res,$i,"e_closetag") == "select" ) {
				echo " disabled ";
			}
			if(db_result($res,$i,"e_title") != ""){
				echo " title='".db_result($res,$i,"e_title")."' ";
			}
			if(db_result($res,$i,"e_src") != "" && db_result($res,$i,"e_closetag") != "iframe" ){
				if(db_result($res,$i,"e_closetag") == "iframe" && db_result($res,$i,"e_id") != 'loadData'){
		      		echo " src = '".preg_replace($pattern,$replacement,db_result($res,$i,"e_src"));
						if(mb_strpos(db_result($res,$i,"e_src"), "?")) {
							echo "&";
						}
						else {
			      			echo "?";
		      			}
		      			echo "e_id_css=".db_result($res,$i,"e_id")."&e_id=".db_result($res,$i,"e_id") . 
							"&e_target=".db_result($res,$i,"e_target").
							"&" . $urlParameters . "'";
				}
				else{
					echo " src = '".preg_replace($pattern,$replacement,db_result($res,$i,"e_src"))."'";
				}
			}
			echo "' >";
			
			if (db_result($res,$i,"e_closetag") == "iframe" || db_result($res,$i,"e_closetag") == "div") {
				echo db_result($res,$i,"e_id");
			}
			if (db_result($res,$i,"e_closetag") != "iframe" && db_result($res,$i,"e_closetag") != "form" ) {
				echo "</".db_result($res,$i,"e_element").">";
			}
			else {
				echo "</div>";
			}
		}
	}
	$i++;
}
?>
</div></body>
</html>