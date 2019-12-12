<?php
# $Id: mod_coords.php 5825 2010-03-22 14:18:33Z christoph $
# http://www.mapbender.org/index.php/mod_coords.php
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
echo "var mod_showCoords_target = '".$e_target[0]."';";
?>

var mod_showCoords_map = null;
var mod_showCoords_win = null;
var mod_showCoords_elName = "showCoords";
var mod_showCoords_frameName = "";
var mod_showCoords_img_on = new Image(); mod_showCoords_img_on.src = "<?php  echo preg_replace("/_off/","_on",$e_src);  ?>";
var mod_showCoords_img_off = new Image(); mod_showCoords_img_off.src = "<?php  echo $e_src;  ?>";
var mod_showCoords_img_over = new Image(); mod_showCoords_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

function init_mod_showCoords(ind){
	mb_button[ind] = document.getElementById(mod_showCoords_elName);
	mb_button[ind].img_over = mod_showCoords_img_over.src;
	mb_button[ind].img_on = mod_showCoords_img_on.src;
	mb_button[ind].img_off = mod_showCoords_img_off.src;
	mb_button[ind].status = 0;
	mb_button[ind].elName = mod_showCoords_elName;
	mb_button[ind].fName = mod_showCoords_frameName;
	mb_button[ind].go = new Function ("mod_showCoords_run()");
	mb_button[ind].stop = new Function ("mod_showCoords_disable()");
	
	mod_showCoords_map = getMapObjByName(mod_showCoords_target);
}
function mod_showCoords_run(){
	var el = mod_showCoords_map.getDomElement();
	var $el = $(el);
	$el.bind("mousemove", mod_showCoords_display);
	$el.bind("click", mod_showCoords_click);
}
function mod_showCoords_disable(){
	var el = mod_showCoords_map.getDomElement();
	var $el = $(el);
	$el.unbind("mousemove", mod_showCoords_display);
	$el.unbind("click", mod_showCoords_click);
	window.status = "";
}
function mod_showCoords_click(e){
	mod_showCoords_win = window.open("","","width=150, height=100");
	mod_showCoords_win.document.open("text/html");

	mod_showCoords_map.getMousePos(e);
	var pos = makeClickPos2RealWorldPos(mod_showCoords_target, clickX, clickY);
	mod_showCoords_win.document.write((Math.round(pos[0]*100)/100) + " / " +  (Math.round(pos[1]*100)/100));
	mod_showCoords_win.document.close();
	mod_showCoords_win.focus();
}
function mod_showCoords_display(e){
	mod_showCoords_map.getMousePos(e);
	var pos = makeClickPos2RealWorldPos(mod_showCoords_target, clickX, clickY);
	window.status = pos[0] + " / " +  pos[1];
}
