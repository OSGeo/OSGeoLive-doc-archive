<?php
# $Id: mod_perimeter.php 2540 2008-06-23 15:58:56Z christoph $
# http://www.mapbender.org/index.php/mod_perimeter.php
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
include(dirname(__FILE__).'/../include/dyn_js.php');
?>

var mod_perimeter_target = "<?php echo $e_target[0]; ?>";
mb_registerInitFunctions("mod_perimeter_prepare()");
mb_registerSubFunctions("mod_perimeter_draw()");
mb_registerPanSubElement("perimeter");
var mod_perimeter_img_on = new Image(); mod_perimeter_img_on.src =  "<?php  echo preg_replace("/_off/","_on",$e_src);  ?>";
var mod_perimeter_img_off = new Image(); mod_perimeter_img_off.src ="<?php  echo $e_src;  ?>";
var mod_perimeter_img_over = new Image(); mod_perimeter_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";
var mod_perimeter_prevEvent = null;
var mod_perimeter_x = null;
var mod_perimeter_y = null;
var mod_perimeter_r = null;
var mod_perimeter_canvas = null;
var mod_perimeter_status = 0;
var mod_perimeter_img_obj = null;

function mb_checkTag(frameName, tagName, elementId, appendAtTagName, attributes){
	var oldElement;
	var newElement;
	var prefix;
	if(frameName && frameName !== ""){
		prefix = window.frames[frameName].document;
	}
	else if(!frameName || frameName === ""){
		prefix = document;
	}
	oldElement = prefix.getElementById(elementId);
	if (oldElement === null) {
		newElement = prefix.createElement(tagName);
		newElement = prefix.getElementsByTagName(appendAtTagName)[0].appendChild(newElement);
	}
	else {
		if (oldElement.nodeName.toLowerCase() == tagName.toLowerCase()) {
			for (var i=0; i<attributes.length; i++) {
				oldElement.setAttribute(attributes[i][0], attributes[i][1]);
			}
			return oldElement;
		}
		else {
			return false;
		}
	}
	var newElementAttributeNode = document.createAttribute("id");
	newElementAttributeNode.value = elementId;
	newElement.setAttributeNode(newElementAttributeNode);
	for (var i=0; i<attributes.length; i++) {
		newElement.setAttribute(attributes[i][0], attributes[i][1]);
	}
	return newElement;
}
function mod_perimeter_checkDefaults(){
	try{var t = mod_perimeter_thickness;}catch(e){mod_perimeter_thickness = 2;}
	try{var t = mod_perimeter_color;}catch(e){mod_perimeter_color = '#000000';}
	try{var t = mod_perimeter_text;}catch(e){mod_perimeter_text = 'Please insert a radius: ';}
	try{var t = mod_perimeter_error;}catch(e){mod_perimeter_error = 'Invalid Input.';}
}
function mod_perimeter_click(o){
	mod_perimeter_img_obj = o;
	mod_perimeter_status = (mod_perimeter_status == 0) ? 1 : 0;
	o.src = (mod_perimeter_status == 0) ? mod_perimeter_img_off.src : mod_perimeter_img_on.src;
	if(mod_perimeter_status == 1){
		mod_perimeter_saveEvents();
	}
	else{
		mod_perimeter_disable();	
	}
}
function mod_perimeter_saveEvents(){
	var myE = window.frames[mod_perimeter_target].document;
	mod_perimeter_mouseclick = myE.onclick;
	mod_perimeter_mouseover = myE.onmouseover;
	mod_perimeter_mousedown = myE.onmousedown;
	mod_perimeter_mouseup = myE.onmouseup;
	mod_perimeter_mousemove = myE.onmousemove;
	myE.onclick = mod_perimeter_event;
	myE.onmouseover = null;
	myE.onmousedown = null;
	myE.onmouseup = null;
	myE.onmousemove = null;	
}
function mod_perimeter_restoreEvents(){
	var myE = window.frames[mod_perimeter_target].document;
	myE.onclick = mod_perimeter_mouseclick;
	myE.onmouseover = mod_perimeter_mouseover;
	myE.onmousedown = mod_perimeter_mousedown;
	myE.onmouseup = mod_perimeter_mouseup;
	myE.onmousemove = mod_perimeter_mousemove;
}
function mod_perimeter_over(o){
	o.src = mod_perimeter_img_over.src;
}
function mod_perimeter_out(o){
	o.src = (mod_perimeter_status == 0) ? mod_perimeter_img_off.src : mod_perimeter_img_on.src;
}
function mod_perimeter_disable(){
	window.frames[mod_perimeter_target].document.getElementById("perimeter").innerHTML = '';
}
function mod_perimeter_event(e){
	if(ie){
		clickX = window.frames[mod_perimeter_target].event.clientX;
		clickY = window.frames[mod_perimeter_target].event.clientY;
	}
	else{
		clickX = e.pageX;
		clickY = e.pageY;
	}
	var pos = makeClickPos2RealWorldPos(mod_perimeter_target, clickX, clickY)
	mod_perimeter_x = pos[0];
	mod_perimeter_y = pos[1];
	var units = prompt(mod_perimeter_text);
	var myUnits = mod_perimeter_validate(units);
	if(myUnits != false){
		mod_perimeter_r = myUnits;
		mod_perimeter_draw();
	}
	else{
		mod_perimeter_click(mod_perimeter_img_obj);	
	}
	mod_perimeter_restoreEvents();
}
function mod_perimeter_validate(u){
	if(isNaN(u) == true){
  		alert(mod_perimeter_error);
  		return false;
 	}
 	else{
		return parseInt(u);
 	}
}
function mod_perimeter_prepare(){
	cw_opacity=1;
	mod_perimeter_checkDefaults();	
	var attributes = new Array();
	attributes[0] = new Array();
	attributes[0][0] = "style";
	attributes[0][1] = "position:absolute; top:0px; left:0px; z-index:100; font-size:10px;"; 
	var node = mb_checkTag(mod_perimeter_target, "div", "perimeter", "body", attributes);
	mod_perimeter_canvas = new jsGraphics("perimeter", window.frames[mod_perimeter_target]);
	mod_perimeter_canvas.setStroke(mod_perimeter_thickness);
	mod_perimeter_canvas.setColor(mod_perimeter_color);
}
function mod_perimeter_draw(){
	if(mod_perimeter_status == 1){
		mod_perimeter_disable();
		var posCenter = makeRealWorld2mapPos(mod_perimeter_target,mod_perimeter_x, mod_perimeter_y);
		var posRadius = makeRealWorld2mapPos(mod_perimeter_target,(mod_perimeter_x + mod_perimeter_r), mod_perimeter_y);
		var pxRadius =  posRadius[0] - posCenter[0];
		mod_perimeter_canvas.drawEllipse((posCenter[0]-pxRadius), (posCenter[1]-pxRadius), pxRadius*2, pxRadius*2);
		mod_perimeter_canvas.paint();
	}
}