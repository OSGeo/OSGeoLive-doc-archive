<?php
# $Id: mod_addWmsFromFilteredList_button.php 7954 2011-07-18 18:57:37Z marc $
# http://www.mapbender.org/Add_WMS_from_filtered_list_%28AJAX%29
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

require_once(dirname(__FILE__)."/../php/mb_validatePermission.php");

echo "var addWmsFromFilteredListId = '" . $e_id . "';";
?>
var addWmsFromFilteredList_title;
var addWmsFromFilteredListPopup;

eventInit.register(function () {
	var $addWmsFromFilteredListButton = $("#" + addWmsFromFilteredListId);
	addWmsFromFilteredList_title = $addWmsFromFilteredListButton.get(0).title;
	$addWmsFromFilteredListButton.click(function () {
		var $addWmsFromFilteredListButton = $("#" + addWmsFromFilteredListId);
		addWmsFromFilteredList_title = $addWmsFromFilteredListButton.get(0).title;
		addWmsFromFilteredList_showPopup();
	});
});

var addWmsFromFilteredList_showPopup = function () {
	if($('.addWmsFromFilteredListIframe').size() > 0) {
		$('.addWmsFromFilteredListIframe').dialog('destroy');
	}
	var $addWmsFromFilteredListPopup = $('<div class="addWmsFromFilteredListIframe"><iframe style="width:100%;height:98%;" src="../javascripts/mod_addWMSfromfilteredList_ajax.php?<?php 
		
	echo session_name() . '=' . session_id() . '&';
	echo "guiID=" . $gui_id . '&';
	echo "elementID=" . $e_id;
	
		?>"></iframe></div>');
	$addWmsFromFilteredListPopup.dialog({
		title : addWmsFromFilteredList_title,
		bgiframe: true,
		autoOpen: true,
		modal: false,
		width: 720,
		height: 600,
		pos: [300,100]
	}).parent().css({position:"absolute"});
};

eventLocalize.register(function () {
	if($('.addWmsFromFilteredListIframe').size() > 0) {
		$('.addWmsFromFilteredListIframe').dialog('destroy');
	}
});

eventInit.register(function () {
	mod_addWmsFromFilteredList_init();
});

var mod_addWmsFromFilteredList_img = new Image(); 
mod_addWmsFromFilteredList_img.src = "<?php  echo $e_src;  ?>";
var mod_addWmsFromFilteredList_img_over = new Image(); 
mod_addWmsFromFilteredList_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

function mod_addWmsFromFilteredList_init() {
	var obj = document.getElementById(addWmsFromFilteredListId);
	obj.src = mod_addWmsFromFilteredList_img.src;
	obj.onmouseover = new Function("mod_addWmsFromFilteredList_over()");
	obj.onmouseout = new Function("mod_addWmsFromFilteredList_out()");
}
function mod_addWmsFromFilteredList_over(){
	document.getElementById(addWmsFromFilteredListId).src = mod_addWmsFromFilteredList_img_over.src;
}

function mod_addWmsFromFilteredList_out(){
	document.getElementById(addWmsFromFilteredListId).src = mod_addWmsFromFilteredList_img.src;
}
