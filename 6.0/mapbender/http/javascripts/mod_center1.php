<?php
# $Id: mod_center1.php 6659 2010-07-30 09:33:43Z christoph $
# http://www.mapbender.org/index.php/mod_center1.php
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
echo "var mod_center_target = '".$e_target[0]."';";
?>

var mod_center_mapObj = null;
var mod_center_elName = "center1";
var mod_center_frameName = "";


var mod_center_img_on = new Image(); mod_center_img_on.src = "<?php  echo preg_replace("/_off/","_on",$e_src);  ?>";
var mod_center_img_off = new Image(); mod_center_img_off.src = "<?php  echo $e_src;  ?>";
var mod_center_img_over = new Image(); mod_center_img_over.src = "<?php  echo preg_replace("/_off/","_over",$e_src);  ?>";

function init_gui1_center(ind){ 
	//get Pointer to my MapObj
	mod_center_mapObj = getMapObjByName(mod_center_target);
	
	mb_button[ind] = document.getElementById(mod_center_elName);
	mb_button[ind].img_over = mod_center_img_over.src;
	mb_button[ind].img_on = mod_center_img_on.src;
	mb_button[ind].img_off = mod_center_img_off.src;
	mb_button[ind].status = 0;
	mb_button[ind].elName = mod_center_elName;
	mb_button[ind].fName = mod_center_frameName;
	mb_button[ind].go = new Function ("mod_center_click()");
	mb_button[ind].stop = new Function ("mod_center_disable()");
}
function mod_center_click(){
	$(mod_center_mapObj.getDomElement()).bind("click", mod_center_event);
}
function mod_center_disable(){
	$(mod_center_mapObj.getDomElement()).unbind("click", mod_center_event);
}
function mod_center_event(e){
	var pos = mod_center_mapObj.getMousePosition(e);
	pos = mod_center_mapObj.convertPixelToReal(pos);
	zoom(mod_center_target,true,1.0,pos.x, pos.y);
}
