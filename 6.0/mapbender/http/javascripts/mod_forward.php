<?php
# $Id: mod_forward.php 4368 2009-07-16 13:46:33Z christoph $
# http://www.mapbender.org/index.php/mod_forward.php
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
echo "var mod_forward_map = '".$e_target[0]."';";
echo "var mod_forward_overview = '".$e_target[1]."';";

?>
var mod_forward_img_off = new Image(); 
mod_forward_img_off.src = "<?php  echo preg_replace("/_off_disabled/","_off",$e_src);?>";
var mod_forward_img_off_disabled = new Image(); 
mod_forward_img_off_disabled.src = "<?php  echo $e_src;  ?>";
var mod_forward_img_over = new Image(); 
mod_forward_img_over.src = "<?php  echo preg_replace("/_off_disabled/","_over",$e_src);?>";
var mod_forward_img_previous = mod_forward_img_off_disabled.src;

eventAfterMapRequest.register(function (obj) {
	mod_forward_check(obj.map.elementName);
});

$('#<?php echo $e_id;?>').mouseover(function () {
	mod_forward_over(this);
}).mouseout(function() {
	mod_forward_out(this);
}).click(function() {
	mod_forward_set();
});

var mod_forward_cnt = 0;
var mod_forward_hist_cnt = 0;

function mod_forward_check(frameName){
	var ind = getMapObjIndexByName(frameName);
	var map = mb_mapObj[ind];
	if(!map.mb_MapFutureObj) {
		map.mb_MapFutureObj = [];
	}
	if(frameName == mod_forward_map){
		if(map.mb_MapFutureObj.length > 0){
			document.getElementById("forward").src =  mod_forward_img_off.src;
		}
		else{
			document.getElementById("forward").src =  mod_forward_img_off_disabled.src;
			mod_forward_img_previous = document.getElementById("forward").src;
		}
	}
	var indForward = getMapObjIndexByName(mod_forward_map);
	if(mb_mapObj[indForward].mb_MapHistoryObj){
		if(mb_mapObj[indForward].mb_MapHistoryObj.length > mod_forward_hist_cnt && mb_mapObj[indForward].mb_MapFutureObj.length == mod_forward_cnt){
			mod_forward_reset();
		}
		mod_forward_cnt = mb_mapObj[indForward].mb_MapFutureObj.length;
		mod_forward_hist_cnt = mb_mapObj[indForward].mb_MapHistoryObj.length;
	}
}
function mod_forward_reset(){
	var ind = getMapObjIndexByName(mod_forward_map);
	if (mb_mapObj[ind].mb_MapFutureObj.length > 0){
		mb_mapObj[ind].mb_MapFutureObj = [];
		document.getElementById("forward").src = mod_forward_img_off_disabled.src;
		mod_forward_img_previous = document.getElementById("forward").src;
	}
}

function mod_forward_over(obj){
	mod_forward_img_previous = document.getElementById("forward").src;
	if(document.getElementById("forward").src ==  mod_forward_img_off.src){
		document.getElementById("forward").src = mod_forward_img_over.src;
	}
}

function mod_forward_out(obj){
	document.getElementById("forward").src  = mod_forward_img_previous;
}

function mod_forward_set(){
	if(mod_forward_img_previous ==  mod_forward_img_off.src){
		var ind = getMapObjIndexByName(mod_forward_map);
		var map = mb_mapObj[ind];
		var cnt = map.mb_MapFutureObj.length - 1;
		if(map.epsg != map.mb_MapFutureObj[cnt].epsg){
			var oind = getMapObjIndexByName(mod_forward_overview);
			var ov = mb_mapObj[oind];
			for(var i=0; i<ov.mb_MapHistoryObj.length; i++){
				if(ov.mb_MapHistoryObj[i].epsg == map.mb_MapFutureObj[cnt].epsg){
					ov.epsg = ov.mb_MapHistoryObj[i].epsg;
					ov.extent = new Mapbender.Extent(ov.mb_MapHistoryObj[i].extent.min, ov.mb_MapHistoryObj[i].extent.max);
					ov.setMapRequest();
					break;
				}
			}
		}
		document.getElementById(mod_forward_map).style.width = map.mb_MapFutureObj[cnt].width;
		document.getElementById(mod_forward_map).style.height = map.mb_MapFutureObj[cnt].height;     
		map.width = map.mb_MapFutureObj[cnt].width;
		map.height = map.mb_MapFutureObj[cnt].height;
		map.epsg = map.mb_MapFutureObj[cnt].epsg;
		map.extent = new Mapbender.Extent(map.mb_MapFutureObj[cnt].extent.min, map.mb_MapFutureObj[cnt].extent.max);
		map.layers = map.mb_MapFutureObj[cnt].layers;
		map.styles = map.mb_MapFutureObj[cnt].styles;
		map.querylayers = map.mb_MapFutureObj[cnt].querylayers;
		map.mb_MapFutureObj.length = (map.mb_MapFutureObj.length - 1);
		map.setMapRequest();
	}
}

