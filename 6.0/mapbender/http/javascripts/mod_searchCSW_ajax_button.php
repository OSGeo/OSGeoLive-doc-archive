<?php
# $Id: mod_addWmsFromFilteredList_button.php 6834 2010-08-30 08:52:00Z verenadiewald $
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

echo "var searchCSWId = '" . $e_id . "';";
?>
var searchCSW_title;
var searchCSWPopup;

eventInit.register(function () {
	var $searchCSWButton = $("#" + searchCSWId);
	searchCSW_title = $searchCSWButton.get(0).title;
	$searchCSWButton.click(function () {
		var $searchCSWButton = $("#" + searchCSWId);
		searchCSW_title = $searchCSWButton.get(0).title;
		searchCSW_showPopup();
	});
});

var searchCSW_showPopup = function () {
	if($('.searchCSWIframe').size() > 0) {
		$('.searchCSWIframe').dialog('destroy');
	}
	var $searchCSWPopup = $('<div class="searchCSWIframe"><iframe style="border-style:none;width:100%;height:98%;" src="../javascripts/mod_searchCSW_ajax.php?<?php 
		
	echo session_name() . '=' . session_id() . '&';
	echo "guiID=" . $gui_id . '&';
	echo "elementID=" . $e_id;
	
		?>"></iframe></div>');
	$searchCSWPopup.dialog({
		title : searchCSW_title,
		bgiframe: true,
		autoOpen: true,
		modal: false,
		width: 720,
		height: 600,
		pos: [300,100]
	}).parent().css({position:"absolute"});
};

eventLocalize.register(function () {
	if($('.searchCSWIframe').size() > 0) {
		$('.searchCSWIframe').dialog('destroy');
	}
});

eventInit.register(function () {
	mod_searchCSW_init();
});

var mod_searchCSW_img = new Image(); 
mod_searchCSW_img.src = "<?php  echo $e_src;  ?>";
var mod_searchCSW_img_over = new Image(); 
mod_searchCSW_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

function mod_searchCSW_init() {
	var obj = document.getElementById(searchCSWId);
	obj.src = mod_searchCSW_img.src;
	obj.onmouseover = new Function("mod_searchCSW_over()");
	obj.onmouseout = new Function("mod_searchCSW_out()");
}
function mod_searchCSW_over(){
	document.getElementById(searchCSWId).src = mod_searchCSW_img_over.src;
}

function mod_searchCSW_out(){
	document.getElementById(searchCSWId).src = mod_searchCSW_img.src;
}
