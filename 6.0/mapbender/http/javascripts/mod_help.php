<?php
# $Id: mod_help.php 6673 2010-08-02 13:52:19Z christoph $
# http://www.mapbender.org/index.php/mod_help
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
include(dirname(__FILE__)."/../include/dyn_js.php");
?>
try{if(mod_help_color){}}catch(e){mod_help_color = '#cc33cc';}
try{if(mod_help_thickness){}}catch(e){mod_help_color = 3;}
try{if(mod_help_width){}}catch(e){mod_help_width = 1000;}
try{if(mod_help_height){}}catch(e){mod_help_height = 1000;}
try{if(mod_help_text){}}catch(e){mod_help_text = "<?php echo _mb("click highlighted elements for help");?>"}
var mod_help_elName = "<?php echo $e_id; ?>";
var mod_help_str = "";
var mod_help_img_on = new Image(); mod_help_img_on.src =  "<?php  echo preg_replace("/_off/","_on",$e_src);  ?>";
var mod_help_img_off = new Image(); mod_help_img_off.src ="<?php  echo $e_src;  ?>";
var mod_help_img_over = new Image(); mod_help_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

mb_regButton(function (ind) {
	mb_button[ind] = document.getElementById(mod_help_elName);
	mb_button[ind].img_over = mod_help_img_over.src;
	mb_button[ind].img_on = mod_help_img_on.src;
	mb_button[ind].img_off = mod_help_img_off.src;
	mb_button[ind].status = 0;
	mb_button[ind].elName = mod_help_elName;
	mb_button[ind].go = function () {
		mod_help_click();
	};
	mb_button[ind].stop = function () {
		mod_help_disable();
	};
});

function mod_help_click(){
	//create html tags
	mod_help_set();
	//request help string
	var usemapHtml = "";
	for (var module in Mapbender.modules) {
		var currentModule = Mapbender.modules[module];
		var top = parseInt(currentModule.top, 10) || 0;
		var left = parseInt(currentModule.left, 10) || 0;
		var width = parseInt(currentModule.width, 10) || 0;
		var height = parseInt(currentModule.height, 10) || 0;
		
		if (!currentModule.url) {
			continue;
		}
		usemapHtml += "<area id='helpArea_" + module + "' " + 
			"shape='rect' coords='" + left + "," + 
			top + "," + (parseInt(left + width)) + "," + 
			parseInt(top + height) + "' href='#' " + 
			"alt='" + ((currentModule.id == mod_help_elName) ? mod_help_text : "HELP: " + currentModule.url) + "' " +
			"title='" + ((currentModule.id == mod_help_elName) ? mod_help_text : "HELP: " + currentModule.url) + "' " + 
			"nohref />";

	}		
	
	var transparentImgHtml = "<img src='../img/transparent.gif' style='cursor:help' " + 
		"width='" + mod_help_width + "' height='" + mod_help_height + 
		"' usemap='#mod_help_imagemap' border='0'>";
	
	var html = "<div id='helpMapContainer'>" + transparentImgHtml + "<map name='mod_help_imagemap'>" + usemapHtml + "</map></div>";

	$('#mod_help_img').empty().html(html);

	for (var module in Mapbender.modules) {
		(function () {
			var currentModule = Mapbender.modules[module];
			if (currentModule.url) {
				$("#helpArea_" + module).click(function () {
					mod_help_disable();
					var w = window.open(currentModule.url, "help");
				});
			}
		}());
	}
		
	mod_help_set_str();
	return;
}

function mod_help_disable(){
	$('#mod_help_img').empty().css({
		width: '0px',
		height: '0px'
	});
	$('#mod_help_draw').empty();
	
	mb_disableThisButton(mod_help_elName);
}
function mod_help_set(){
	var helpimg = document.createElement('div');
	var tmp = document.body.appendChild(helpimg);
	tmp.id = 'mod_help_img';
	tmp.style.position = 'absolute';
	tmp.style.zIndex = '1000';
	tmp.style.top = '0px';
	tmp.style.left = '0px';
	tmp.style.width = '1px';
	tmp.style.height='1px';
	
	var helpdraw = document.createElement('div');
	var tmp = document.body.appendChild(helpdraw);
	tmp.id = 'mod_help_draw';
	tmp.style.position = 'absolute';
	tmp.style.zIndex = '999';
	tmp.style.top = '0px';
	tmp.style.left = '0px';
	tmp.style.width = '0px';
	tmp.style.height='0px';
	
	return true;
}
function mod_help_set_str(){
	mod_help_update();
	mod_help_draw();
}

function mod_help_update(){
	//try to update tab coords
	try{
		for (var module in Mapbender.modules) {
			var tab = $("#tabs_" + module).get(0);
			if (tab) {
				var area = $("#helpArea_" + module).get(0);			
				var top = parseInt(tab.style.top, 10);
				var left = parseInt(tab.style.left, 10);
				var width = parseInt(tab.style.width, 10);
				var height = parseInt(tab.style.height, 10);
				var lly = parseInt(top-height, 10);
				var urx = parseInt(left+width, 10);
				area.coords = left + "," + lly + "," + urx + "," + top;
			}
		}
	}
	catch(e){
		var e = new Mb_warning(e);
	}
}

function mod_help_draw(){
	var canvas = new jsGraphics('mod_help_draw');
	canvas.setStroke(parseInt(mod_help_thickness));
	canvas.setColor(mod_help_color);
	var my = document.getElementsByName("mod_help_imagemap")[0];
	for(var i=0; i<my.areas.length; i++){
		var myc = my.areas[i].coords.split(",");
		canvas.drawRect(parseInt(myc[0]),parseInt(myc[1]),parseInt(myc[2]) - parseInt(myc[0]),parseInt(myc[3]) - parseInt(myc[1]));
		canvas.paint();		
	}
}

